<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Verificar que sea preceptor
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

// Obtenemos todos los alumnos del curso
$query_alumnos = "SELECT usuarios.DNI, usuarios.Primer_nombre, usuarios.Segundo_nombre, usuarios.Apellido
                  FROM cursos
                  INNER JOIN curso_alumno ON cursos.ID = curso_alumno.ID_Curso
                  INNER JOIN alumnos ON curso_alumno.DNI_Alumno = alumnos.DNI_Alumno
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
    <title>Tomar Asistencia por Curso</title>
    <link rel="stylesheet" href="./style/styles.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Tomar Asistencia - Curso Completo</h2>
        <p class="text-muted">
            Curso: <?php echo htmlspecialchars($anio . "° " . $division . " - " . $especialidad); ?>
            | Turno: <?php echo htmlspecialchars($turno); ?>
        </p>
        <hr>
        
        <?php if (mysqli_num_rows($result_alumnos) > 0): ?>
            <form method="POST" action="procesar_asistencia_curso.php">
                <input type="hidden" name="anio" value="<?php echo $anio; ?>">
                <input type="hidden" name="division" value="<?php echo $division; ?>">
                <input type="hidden" name="especialidad" value="<?php echo $especialidad; ?>">
                <input type="hidden" name="turno" value="<?php echo $turno; ?>">
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="fecha" class="form-label"><strong>Fecha de la clase:</strong></label>
                        <input type="date" 
                               id="fecha" 
                               name="fecha" 
                               class="form-control" 
                               value="<?php echo date('Y-m-d'); ?>" 
                               required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label"><strong>Observaciones generales (opcional):</strong></label>
                        <input type="text" 
                               name="observaciones_generales" 
                               class="form-control" 
                               placeholder="Ej: Día de evaluación, salida educativa, etc.">
                    </div>
                </div>
                
                <h4>Lista de Alumnos del Curso</h4>
                <p class="text-muted">Marca la situación de cada alumno en esta clase:</p>
                
                <?php while ($alumno = mysqli_fetch_assoc($result_alumnos)): ?>
                    <div class="alumno-item">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <strong><?php echo htmlspecialchars($alumno['Apellido'] . ", " . $alumno['Primer_nombre']); ?></strong>
                                <?php if (!empty($alumno['Segundo_nombre'])): ?>
                                    <?php echo htmlspecialchars(" " . $alumno['Segundo_nombre']); ?>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group btn-group-asistencia" role="group">
                                    <input type="radio" 
                                           class="btn-check" 
                                           name="asistencia[<?php echo $alumno['DNI']; ?>]" 
                                           id="presente_<?php echo $alumno['DNI']; ?>" 
                                           value="Presente" 
                                           checked>
                                    <label class="btn btn-outline-success" for="presente_<?php echo $alumno['DNI']; ?>">
                                        Presente
                                    </label>
                                    
                                    <input type="radio" 
                                           class="btn-check" 
                                           name="asistencia[<?php echo $alumno['DNI']; ?>]" 
                                           id="tarde_<?php echo $alumno['DNI']; ?>" 
                                           value="Tarde">
                                    <label class="btn btn-outline-warning" for="tarde_<?php echo $alumno['DNI']; ?>">
                                        Tarde
                                    </label>
                                    
                                    <input type="radio" 
                                           class="btn-check" 
                                           name="asistencia[<?php echo $alumno['DNI']; ?>]" 
                                           id="falta_<?php echo $alumno['DNI']; ?>" 
                                           value="Falta">
                                    <label class="btn btn-outline-danger" for="falta_<?php echo $alumno['DNI']; ?>">
                                        Ausente
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <input type="text" 
                                       name="observaciones[<?php echo $alumno['DNI']; ?>]" 
                                       class="form-control form-control-sm" 
                                       placeholder="Observación (opcional)">
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Guardar Asistencia del Curso</button>
                    <a href="revisar_notas_preceptor.php" class="btn btn-secondary btn-lg">Cancelar</a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">No hay alumnos registrados en este curso.</div>
            <a href="revisar_notas_preceptor.php" class="btn btn-secondary">Volver</a>
        <?php endif; ?>
    </div>
</body>
</html>
