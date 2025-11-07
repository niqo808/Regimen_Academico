<?php
include('./conexion/conexion.php');
include('./public/header.php');

if (!isset($_SESSION['DNI']) || ($_SESSION['rol'] != 'Profesor' && $_SESSION['rol'] != 'Preceptor')) {
    header("Location: index.php");
    exit;
}

$dni_usuario = $_SESSION['DNI'];
$id_materia = isset($_GET['id_materia']) ? intval($_GET['id_materia']) : null;

if (!$id_materia) {
    if ($_SESSION['rol'] == 'Profesor') {
        header("Location: mis_materias_profesor.php");
    } else {
        header("Location: revisar_notas_preceptor.php");
    }
    exit;
}

// Verificamos permisos según el rol
if ($_SESSION['rol'] == 'Profesor') {
    // Para profesores: verificar que la materia pertenezca a este profesor
    $query_verificar = "SELECT materias.ID, materias.Nombre, materias.Horarios,
                        cursos.Anio, cursos.Division, cursos.Especialidad, cursos.Turno
                        FROM materias
                        INNER JOIN cursos ON materias.ID_Curso = cursos.ID
                        WHERE materias.ID = '$id_materia' AND materias.DNI_Profesor = '$dni_usuario'";
} else {
    // Para preceptores: verificar que la materia pertenezca a un curso del preceptor
    $query_verificar = "SELECT materias.ID, materias.Nombre, materias.Horarios,
                        cursos.Anio, cursos.Division, cursos.Especialidad, cursos.Turno
                        FROM materias
                        INNER JOIN cursos ON materias.ID_Curso = cursos.ID
                        WHERE materias.ID = '$id_materia' AND cursos.DNI_Preceptor = '$dni_usuario'";
}

$result_verificar = mysqli_query($CONN, $query_verificar);

if (mysqli_num_rows($result_verificar) == 0) {
    if ($_SESSION['rol'] == 'Profesor') {
        echo "<script>alert('No tienes permiso para acceder a esta materia.'); window.location='mis_materias_profesor.php';</script>";
    } else {
        echo "<script>alert('No tienes permiso para acceder a esta materia.'); window.location='revisar_notas_preceptor.php';</script>";
    }
    exit;
}

$materia = mysqli_fetch_assoc($result_verificar);

// Obtenemos todos los alumnos del curso
$query_alumnos = "SELECT usuarios.DNI, usuarios.Primer_nombre, usuarios.Segundo_nombre, usuarios.Apellido
                  FROM cursos
                  INNER JOIN curso_alumno ON cursos.ID = curso_alumno.ID_Curso
                  INNER JOIN alumnos ON curso_alumno.DNI_Alumno = alumnos.DNI_Alumno
                  INNER JOIN usuarios ON alumnos.DNI_Alumno = usuarios.DNI
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
    <title>Tomar Asistencia</title>
    <link rel="stylesheet" href="./style/styles.css">
</head>
<body>
    <div class="container mt-4">
        <h2><?php echo htmlspecialchars($materia['Nombre']); ?></h2>
        <p class="text-muted">
            Curso: <?php echo htmlspecialchars($materia['Anio'] . "° " . $materia['Division'] . " - " . $materia['Especialidad']); ?>
            | Turno: <?php echo htmlspecialchars($materia['Turno']); ?>
        </p>
        <hr>
        
        <?php if (mysqli_num_rows($result_alumnos) > 0): ?>
            <form method="POST" action="procesar_asistencia.php">
                <input type="hidden" name="id_materia" value="<?php echo $id_materia; ?>">
                
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
                
                <h4>Lista de Alumnos</h4>
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
                    <button type="submit" class="btn btn-primary btn-lg">Guardar Asistencia</button>
                    <?php if ($_SESSION['rol'] == 'Profesor'): ?>
                        <a href="mis_materias_profesor.php" class="btn btn-secondary btn-lg">Cancelar</a>
                    <?php else: ?>
                        <a href="revisar_notas_preceptor.php" class="btn btn-secondary btn-lg">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">No hay alumnos registrados en este curso.</div>
            <?php if ($_SESSION['rol'] == 'Profesor'): ?>
                <a href="mis_materias_profesor.php" class="btn btn-secondary">Volver</a>
            <?php else: ?>
                <a href="revisar_notas_preceptor.php" class="btn btn-secondary">Volver</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>