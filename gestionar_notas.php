<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Validación de seguridad: debe ser profesor
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Profesor') {
    header("Location: index.php");
    exit;
}

$dni_profesor = $_SESSION['DNI'];
$id_materia = isset($_GET['id_materia']) ? intval($_GET['id_materia']) : null;

if (!$id_materia) {
    header("Location: mis_materias_profesor.php");
    exit;
}

// Verificamos que esta materia realmente pertenezca a este profesor
$query_verificar = "SELECT materias.ID, materias.Nombre, materias.Horarios,
                    cursos.Anio, cursos.Division, cursos.Especialidad, cursos.Turno, cursos.ID as ID_Curso
                    FROM materias
                    INNER JOIN cursos ON materias.ID_Curso = cursos.ID
                    WHERE materias.ID = '$id_materia' AND materias.DNI_Profesor = '$dni_profesor'";
$result_verificar = mysqli_query($CONN, $query_verificar);

if (mysqli_num_rows($result_verificar) == 0) {
    echo "<script>alert('No tienes permiso para acceder a esta materia.'); window.location='mis_materias_profesor.php';</script>";
    exit;
}

$materia = mysqli_fetch_assoc($result_verificar);

// Procesamos el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['notas'] as $dni_alumno => $notas_alumno) {
        $primerInforme = !empty($notas_alumno['primerInforme']) ? intval($notas_alumno['primerInforme']) : null;
        $primerCuatri = !empty($notas_alumno['primerCuatri']) ? intval($notas_alumno['primerCuatri']) : null;
        $segundoInforme = !empty($notas_alumno['segundoInforme']) ? intval($notas_alumno['segundoInforme']) : null;
        $segundoCuatri = !empty($notas_alumno['segundoCuatri']) ? intval($notas_alumno['segundoCuatri']) : null;
        $notaFinal = !empty($notas_alumno['notaFinal']) ? intval($notas_alumno['notaFinal']) : null;
        
        // Verificamos si ya existe un registro de notas
        $check_query = "SELECT * FROM notas WHERE dni_alumno = '$dni_alumno' AND id_materia = '$id_materia'";
        $check_result = mysqli_query($CONN, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
        // Si existe, hacemos UPDATE
        // IMPORTANTE: Al actualizar, volvemos el estado a Pendiente
        $update_query = "UPDATE notas SET 
                        primerInforme = " . ($primerInforme !== null ? "'$primerInforme'" : "NULL") . ",
                        primerCuatri = " . ($primerCuatri !== null ? "'$primerCuatri'" : "NULL") . ",
                        segundoInforme = " . ($segundoInforme !== null ? "'$segundoInforme'" : "NULL") . ",
                        segundoCuatri = " . ($segundoCuatri !== null ? "'$segundoCuatri'" : "NULL") . ",
                        notaFinal = " . ($notaFinal !== null ? "'$notaFinal'" : "NULL") . ",
                        Estado_Aprobacion = 'Pendiente',
                        DNI_Preceptor_Aprobador = NULL,
                        Fecha_Aprobacion = NULL
                        WHERE dni_alumno = '$dni_alumno' AND id_materia = '$id_materia'";
        mysqli_query($CONN, $update_query);
        } else {
        // Si no existe, hacemos INSERT con estado Pendiente
            if ($primerInforme !== null || $primerCuatri !== null || $segundoInforme !== null || 
                $segundoCuatri !== null || $notaFinal !== null) {
                $insert_query = "INSERT INTO notas (dni_alumno, id_materia, primerInforme, primerCuatri, 
                                segundoInforme, segundoCuatri, notaFinal, Estado_Aprobacion) 
                                VALUES ('$dni_alumno', '$id_materia', 
                                " . ($primerInforme !== null ? "'$primerInforme'" : "NULL") . ",
                                " . ($primerCuatri !== null ? "'$primerCuatri'" : "NULL") . ",
                                " . ($segundoInforme !== null ? "'$segundoInforme'" : "NULL") . ",
                                " . ($segundoCuatri !== null ? "'$segundoCuatri'" : "NULL") . ",
                                " . ($notaFinal !== null ? "'$notaFinal'" : "NULL") . ",
                                'Pendiente')";
                mysqli_query($CONN, $insert_query);
            }
        }
    }
    
    echo "<script>alert('Notas guardadas correctamente.'); window.location='gestionar_notas.php?id_materia=$id_materia';</script>";
    exit;
}

// Obtenemos todos los alumnos de este curso con sus notas actuales
$query_alumnos = "SELECT usuarios.DNI, usuarios.Primer_nombre, usuarios.Segundo_nombre, usuarios.Apellido,
                  notas.primerInforme, notas.primerCuatri, notas.segundoInforme, notas.segundoCuatri, notas.notaFinal, notas.Estado_Aprobacion
                  FROM cursos
                  INNER JOIN alumnos ON cursos.DNI_Alumno = alumnos.DNI_Alumno
                  INNER JOIN usuarios ON alumnos.DNI_Alumno = usuarios.DNI
                  LEFT JOIN notas ON (alumnos.DNI_Alumno = notas.dni_alumno AND notas.id_materia = '$id_materia')
                  WHERE cursos.Anio = '" . $materia['Anio'] . "' 
                  AND cursos.Division = '" . $materia['Division'] . "'
                  AND cursos.Especialidad = '" . $materia['Especialidad'] . "'
                  AND cursos.Turno = '" . $materia['Turno'] . "'
                  AND cursos.Estado = 1
                  ORDER BY usuarios.Apellido, usuarios.Primer_nombre";
$result_alumnos = mysqli_query($CONN, $query_alumnos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Notas</title>
    <link rel="stylesheet" href="./style/styles.css">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <h2><?php echo htmlspecialchars($materia['Nombre']); ?></h2>
                <p class="text-muted">
                    Curso: <?php echo htmlspecialchars($materia['Anio'] . "° " . $materia['Division'] . " - " . $materia['Especialidad']); ?>
                    | Turno: <?php echo htmlspecialchars($materia['Turno']); ?>
                </p>
                <hr>
                
                <?php if (mysqli_num_rows($result_alumnos) > 0): ?>
                    <form method="POST" action="">
                        <div class="table-responsive">
                            <table class="table table-bordered tabla-notas">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Apellido y Nombre</th>
                                        <th>1° Informe</th>
                                        <th>1° Cuatri</th>
                                        <th>2° Informe</th>
                                        <th>2° Cuatri</th>
                                        <th>Nota Final</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($alumno = mysqli_fetch_assoc($result_alumnos)): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($alumno['Apellido'] . ", " . $alumno['Primer_nombre']); ?></strong>
                                                <?php if (!empty($alumno['Segundo_nombre'])): ?>
                                                    <?php echo htmlspecialchars(" " . $alumno['Segundo_nombre']); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       name="notas[<?php echo $alumno['DNI']; ?>][primerInforme]" 
                                                       class="form-control form-control-sm"
                                                       min="1" max="10" 
                                                       value="<?php echo $alumno['primerInforme'] ?? ''; ?>"
                                                       placeholder="-">
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       name="notas[<?php echo $alumno['DNI']; ?>][primerCuatri]" 
                                                       class="form-control form-control-sm"
                                                       min="1" max="10" 
                                                       value="<?php echo $alumno['primerCuatri'] ?? ''; ?>"
                                                       placeholder="-">
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       name="notas[<?php echo $alumno['DNI']; ?>][segundoInforme]" 
                                                       class="form-control form-control-sm"
                                                       min="1" max="10" 
                                                       value="<?php echo $alumno['segundoInforme'] ?? ''; ?>"
                                                       placeholder="-">
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       name="notas[<?php echo $alumno['DNI']; ?>][segundoCuatri]" 
                                                       class="form-control form-control-sm"
                                                       min="1" max="10" 
                                                       value="<?php echo $alumno['segundoCuatri'] ?? ''; ?>"
                                                       placeholder="-">
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       name="notas[<?php echo $alumno['DNI']; ?>][notaFinal]" 
                                                       class="form-control form-control-sm"
                                                       min="1" max="10" 
                                                       value="<?php echo $alumno['notaFinal'] ?? ''; ?>"
                                                       placeholder="-">
                                            </td>
                                            <td class="text-center">
                                                <?php 
                                                $estado = $alumno['Estado_Aprobacion'] ?? 'Sin cargar';
                                                if ($estado == 'Aprobado') {
                                                    echo "<span class='badge bg-success'>Aprobado</span>";
                                                } elseif ($estado == 'Pendiente') {
                                                    echo "<span class='badge bg-warning'>Pendiente</span>";
                                                } elseif ($estado == 'Rechazado') {
                                                    echo "<span class='badge bg-danger'>Rechazado</span>";
                                                } else {
                                                    echo "<span class='badge bg-secondary'>Sin cargar</span>";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                    </div>
        <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">Guardar Notas</button>
                        <a href="mis_materias_profesor.php" class="btn btn-secondary btn-lg">Volver</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">No hay alumnos registrados en este curso.</div>
                <a href="mis_materias_profesor.php" class="btn btn-secondary">Volver</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>