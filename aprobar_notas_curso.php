<?php
include('./conexion/conexion.php');
include('./public/header.php');

if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Preceptor') {
    header("Location: index.php");
    exit;
}

$dni_preceptor = $_SESSION['DNI'];
$anio = isset($_GET['anio']) ? $_GET['anio'] : null;
$division = isset($_GET['division']) ? $_GET['division'] : null;
$especialidad = isset($_GET['especialidad']) ? $_GET['especialidad'] : null;
$turno = isset($_GET['turno']) ? $_GET['turno'] : null;

if (!$anio || !$division || !$especialidad || !$turno) {
    header("Location: revisar_notas_preceptor.php");
    exit;
}

// Verificar que este curso pertenezca a este preceptor
$query_verificar = "SELECT COUNT(*) as total FROM cursos
                    WHERE cursos.DNI_Preceptor = '$dni_preceptor'
                    AND cursos.Anio = '$anio'
                    AND cursos.Division = '$division'
                    AND cursos.Especialidad = '$especialidad'
                    AND cursos.Turno = '$turno'
                    AND cursos.Estado = 1";
$result_verificar = mysqli_query($CONN, $query_verificar);
if (mysqli_fetch_assoc($result_verificar)['total'] == 0) {
    echo "<script>alert('No tienes permiso para acceder a este curso.'); window.location='revisar_notas_preceptor.php';</script>";
    exit;
}

// Procesar aprobación si viene del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aprobar'])) {
    $alumnos_aprobados = 0;
    
    foreach ($_POST['aprobar'] as $dni_alumno => $materias) {
        foreach ($materias as $id_materia => $aprobar) {
            if ($aprobar == '1') {
                $observacion = !empty($_POST['observaciones'][$dni_alumno][$id_materia]) ? 
                              $_POST['observaciones'][$dni_alumno][$id_materia] : null;
                
                $update_query = "UPDATE notas SET 
                                Estado_Aprobacion = 'Aprobado',
                                DNI_Preceptor_Aprobador = '$dni_preceptor',
                                Fecha_Aprobacion = NOW(),
                                Observaciones_Preceptor = " . ($observacion ? "'$observacion'" : "NULL") . "
                                WHERE dni_alumno = '$dni_alumno' 
                                AND id_materia = '$id_materia'
                                AND Estado_Aprobacion = 'Pendiente'";
                
                if (mysqli_query($CONN, $update_query)) {
                    $alumnos_aprobados++;
                }
            }
        }
    }
    
    echo "<script>alert('Se aprobaron $alumnos_aprobados registro(s) de notas.'); window.location='" . $_SERVER['PHP_SELF'] . "?anio=$anio&division=$division&especialidad=$especialidad&turno=$turno';</script>";
    exit;
}

// Obtener todos los alumnos del curso con sus notas pendientes
$query_alumnos = "SELECT DISTINCT usuarios.DNI, usuarios.Primer_nombre, usuarios.Apellido
                  FROM cursos
                  INNER JOIN alumnos ON cursos.DNI_Alumno = alumnos.DNI_Alumno
                  INNER JOIN usuarios ON alumnos.DNI_Alumno = usuarios.DNI
                  WHERE cursos.Anio = '$anio'
                  AND cursos.Division = '$division'
                  AND cursos.Especialidad = '$especialidad'
                  AND cursos.Turno = '$turno'
                  AND cursos.Estado = 1
                  ORDER BY usuarios.Apellido, usuarios.Primer_nombre";
$result_alumnos = mysqli_query($CONN, $query_alumnos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprobar Notas</title>
    <style>
        .nota-pendiente {
            background-color: #fff3cd;
        }
        .nota-aprobada {
            background-color: #d1e7dd;
        }
        .tabla-notas th {
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <h2>Aprobar Notas - <?php echo htmlspecialchars($anio . "° " . $division); ?></h2>
        <p class="text-muted">
            <?php echo htmlspecialchars($especialidad . " - Turno " . $turno); ?>
        </p>
        <hr>

        <form method="POST" action="">
            <?php while ($alumno = mysqli_fetch_assoc($result_alumnos)): ?>
                <?php
                $dni_alumno = $alumno['DNI'];
                
                // Obtener todas las materias con notas de este alumno
                $query_materias_alumno = "SELECT materias.ID as ID_Materia, materias.Nombre as Nombre_Materia,
                                          notas.primerInforme, notas.primerCuatri, notas.segundoInforme,
                                          notas.segundoCuatri, notas.notaFinal, notas.Estado_Aprobacion,
                                          usuarios.Primer_nombre as Nombre_Profesor, usuarios.Apellido as Apellido_Profesor
                                          FROM notas
                                          INNER JOIN materias ON notas.id_materia = materias.ID
                                          INNER JOIN profesores ON materias.DNI_Profesor = profesores.DNI_Profesor
                                          INNER JOIN usuarios ON profesores.DNI_Profesor = usuarios.DNI
                                          INNER JOIN cursos ON materias.ID_Curso = cursos.ID
                                          WHERE notas.dni_alumno = '$dni_alumno'
                                          AND cursos.Anio = '$anio'
                                          AND cursos.Division = '$division'
                                          AND cursos.Especialidad = '$especialidad'
                                          AND cursos.Turno = '$turno'
                                          ORDER BY materias.Nombre";
                $result_materias_alumno = mysqli_query($CONN, $query_materias_alumno);
                
                // Solo mostrar si tiene notas
                if (mysqli_num_rows($result_materias_alumno) > 0):
                ?>
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <?php echo htmlspecialchars($alumno['Apellido'] . ", " . $alumno['Primer_nombre']); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered tabla-notas">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Materia</th>
                                            <th>Profesor</th>
                                            <th>1° Inf</th>
                                            <th>1° Cuatri</th>
                                            <th>2° Inf</th>
                                            <th>2° Cuatri</th>
                                            <th>Final</th>
                                            <th>Estado</th>
                                            <th>Aprobar</th>
                                            <th>Observación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($materia_nota = mysqli_fetch_assoc($result_materias_alumno)): ?>
                                            <?php
                                            $clase_fila = '';
                                            if ($materia_nota['Estado_Aprobacion'] == 'Pendiente') {
                                                $clase_fila = 'nota-pendiente';
                                            } elseif ($materia_nota['Estado_Aprobacion'] == 'Aprobado') {
                                                $clase_fila = 'nota-aprobada';
                                            }
                                            ?>
                                            <tr class="<?php echo $clase_fila; ?>">
                                                <td><?php echo htmlspecialchars($materia_nota['Nombre_Materia']); ?></td>
                                                <td><?php echo htmlspecialchars($materia_nota['Nombre_Profesor'] . " " . $materia_nota['Apellido_Profesor']); ?></td>
                                                <td class="text-center"><?php echo $materia_nota['primerInforme'] ?? '-'; ?></td>
                                                <td class="text-center"><strong><?php echo $materia_nota['primerCuatri'] ?? '-'; ?></strong></td>
                                                <td class="text-center"><?php echo $materia_nota['segundoInforme'] ?? '-'; ?></td>
                                                <td class="text-center"><strong><?php echo $materia_nota['segundoCuatri'] ?? '-'; ?></strong></td>
                                                <td class="text-center"><strong><?php echo $materia_nota['notaFinal'] ?? '-'; ?></strong></td>
                                                <td class="text-center">
                                                    <?php if ($materia_nota['Estado_Aprobacion'] == 'Pendiente'): ?>
                                                        <span class="badge bg-warning">Pendiente</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Aprobado</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($materia_nota['Estado_Aprobacion'] == 'Pendiente'): ?>
                                                        <input type="checkbox" 
                                                               name="aprobar[<?php echo $dni_alumno; ?>][<?php echo $materia_nota['ID_Materia']; ?>]" 
                                                               value="1"
                                                               class="form-check-input">
                                                    <?php else: ?>
                                                        <span class="text-success">✓</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($materia_nota['Estado_Aprobacion'] == 'Pendiente'): ?>
                                                        <input type="text" 
                                                               name="observaciones[<?php echo $dni_alumno; ?>][<?php echo $materia_nota['ID_Materia']; ?>]"
                                                               class="form-control form-control-sm"
                                                               placeholder="Opcional">
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>

            <div class="text-center mb-4">
                <button type="submit" class="btn btn-success btn-lg">Aprobar Notas Seleccionadas</button>
                <a href="revisar_notas_preceptor.php" class="btn btn-secondary btn-lg">Volver</a>
            </div>
        </form>
    </div>

    <script>
        // JavaScript para seleccionar/deseleccionar todas las notas pendientes
        document.addEventListener('DOMContentLoaded', function() {
            // Puedes agregar un botón "Seleccionar todas las pendientes" si quieres
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            console.log('Total de checkboxes encontrados: ' + checkboxes.length);
        });
    </script>
</body>
</html>