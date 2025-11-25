<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Verificar que sea director
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Director') {
    header("Location: index.php");
    exit;
}

// Obtener todos los cursos
$query_cursos = "SELECT DISTINCT cursos.Anio, cursos.Division, cursos.Especialidad, cursos.Turno
                FROM cursos
                WHERE cursos.Estado = 1
                ORDER BY cursos.Anio, cursos.Division";
$result_cursos = mysqli_query($CONN, $query_cursos);

// Si viene un curso seleccionado, mostrar sus alumnos
$curso_seleccionado = false;
if (isset($_GET['anio']) && isset($_GET['division'])) {
    $curso_seleccionado = true;
    $anio = mysqli_real_escape_string($CONN, $_GET['anio']);
    $division = mysqli_real_escape_string($CONN, $_GET['division']);
    $especialidad = mysqli_real_escape_string($CONN, $_GET['especialidad']);
    $turno = mysqli_real_escape_string($CONN, $_GET['turno']);
    
    // Obtener alumnos del curso
    $query_alumnos = "SELECT DISTINCT usuarios.DNI, usuarios.Primer_nombre, usuarios.Apellido,
                      (SELECT AVG(notas.notaFinal) 
                       FROM notas 
                       INNER JOIN materias ON notas.id_materia = materias.ID
                       INNER JOIN cursos ON materias.ID_Curso = cursos.ID
                       WHERE notas.dni_alumno = usuarios.DNI 
                       AND cursos.Anio = '$anio'
                       AND cursos.Division = '$division'
                       AND cursos.Especialidad = '$especialidad'
                       AND cursos.Turno = '$turno'
                       AND notas.notaFinal IS NOT NULL
                       AND notas.Estado_Aprobacion = 'Aprobado') as promedio
                      FROM usuarios
                      INNER JOIN alumnos ON usuarios.DNI = alumnos.DNI_Alumno
                      INNER JOIN curso_alumno ON alumnos.DNI_Alumno = curso_alumno.DNI_Alumno
                      INNER JOIN cursos ON curso_alumno.ID_Curso = cursos.ID
                      WHERE cursos.Anio = '$anio'
                      AND cursos.Division = '$division'
                      AND cursos.Especialidad = '$especialidad'
                      AND cursos.Turno = '$turno'
                      AND cursos.Estado = 1
                      ORDER BY usuarios.Apellido, usuarios.Primer_nombre";
    $result_alumnos = mysqli_query($CONN, $query_alumnos);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Boletines</title>
    <link rel="stylesheet" href="./style/styles.css">
</head>
<body>
    <div class="container mt-4 mb-5">
        <h2><i class="fas fa-file-alt me-2"></i>Boletines de Alumnos</h2>
        <p class="text-muted">Selecciona un curso para ver los boletines de sus alumnos</p>
        <hr>

        <?php if (!$curso_seleccionado): ?>
            <!-- Selección de Curso -->
            <div class="row">
                <?php while ($curso = mysqli_fetch_assoc($result_cursos)): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <?php echo htmlspecialchars($curso['Anio'] . "° " . $curso['Division']); ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Especialidad:</strong> <?php echo htmlspecialchars($curso['Especialidad']); ?></p>
                                <p class="mb-0"><strong>Turno:</strong> <?php echo htmlspecialchars($curso['Turno']); ?></p>
                            </div>
                            <div class="card-footer">
                                <a href="?anio=<?php echo $curso['Anio']; ?>&division=<?php echo urlencode($curso['Division']); ?>&especialidad=<?php echo urlencode($curso['Especialidad']); ?>&turno=<?php echo urlencode($curso['Turno']); ?>" 
                                   class="btn btn-primary w-100">
                                    Ver Alumnos
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="dashboard_director.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                </a>
            </div>
            
        <?php else: ?>
            <!-- Lista de Alumnos -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <?php echo htmlspecialchars($anio . "° " . $division . " - " . $especialidad . " - Turno " . $turno); ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($result_alumnos) > 0): ?>
                        <div class="list-group">
                            <?php while ($alumno = mysqli_fetch_assoc($result_alumnos)): ?>
                                <a href="generar_boletin.php?dni_alumno=<?php echo $alumno['DNI']; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">
                                                <?php echo htmlspecialchars($alumno['Apellido'] . ", " . $alumno['Primer_nombre']); ?>
                                            </h5>
                                            <small class="text-muted">DNI: <?php echo number_format($alumno['DNI'], 0, '', '.'); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <?php if ($alumno['promedio'] !== null): ?>
                                                <div class="mb-1">
                                                    <span class="badge bg-<?php echo $alumno['promedio'] >= 6 ? 'success' : 'danger'; ?> fs-6">
                                                        Promedio: <?php echo number_format($alumno['promedio'], 2); ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            <span class="badge bg-primary">Ver Boletín →</span>
                                        </div>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">No hay alumnos en este curso.</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="text-center">
                <a href="ver_todos_boletines.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Cursos
                </a>
                <a href="dashboard_director.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
