<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Verificar que sea preceptor
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Preceptor') {
    header("Location: index.php");
    exit;
}

$dni_preceptor = $_SESSION['DNI'];

// Obtener los cursos a cargo de este preceptor
$query_cursos = "SELECT DISTINCT cursos.Anio, cursos.Division, cursos.Especialidad, cursos.Turno
                FROM cursos
                WHERE cursos.DNI_Preceptor = '$dni_preceptor' AND cursos.Estado = 1
                ORDER BY cursos.Anio, cursos.Division";
$result_cursos = mysqli_query($CONN, $query_cursos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/styles.css">
    <title>Revisar Notas - Preceptor</title>
</head>
<body>
    <div class="container mt-4">
        <h2>Revisar y Aprobar Notas</h2>
        <p class="text-muted">Selecciona un curso para revisar las notas cargadas por los profesores.</p>
        <br>

        <?php if (mysqli_num_rows($result_cursos) > 0): ?>
            <div class="row">
                <?php while ($curso = mysqli_fetch_assoc($result_cursos)): ?>
                    <?php
                    // Contar notas pendientes para este curso
                    $query_pendientes = "SELECT COUNT(DISTINCT notas.dni_alumno) as total_pendientes
                                        FROM notas
                                        INNER JOIN alumnos ON notas.dni_alumno = alumnos.DNI_Alumno
                                        INNER JOIN curso_alumno ON alumnos.DNI_Alumno = curso_alumno.DNI_Alumno
                                        INNER JOIN cursos ON curso_alumno.ID_Curso = cursos.ID
                                        WHERE cursos.Anio = '" . $curso['Anio'] . "'
                                        AND cursos.Division = '" . $curso['Division'] . "'
                                        AND cursos.Especialidad = '" . $curso['Especialidad'] . "'
                                        AND cursos.Turno = '" . $curso['Turno'] . "'
                                        AND notas.Estado_Aprobacion = 'Pendiente'";
                    $result_pendientes = mysqli_query($CONN, $query_pendientes);
                    $pendientes = mysqli_fetch_assoc($result_pendientes)['total_pendientes'];
                    ?>
                    
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <?php echo htmlspecialchars($curso['Anio'] . "° " . $curso['Division']); ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Especialidad:</strong> <?php echo htmlspecialchars($curso['Especialidad']); ?></p>
                                <p class="mb-2"><strong>Turno:</strong> <?php echo htmlspecialchars($curso['Turno']); ?></p>
                                <hr>
                                <?php if ($pendientes > 0): ?>
                                    <div class="alert alert-warning mb-3">
                                        <strong><?php echo $pendientes; ?></strong> alumno(s) con notas pendientes
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-success mb-3">
                                        ✓ Todas las notas revisadas
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100" role="group">
                                    <a href="aprobar_notas_curso.php?anio=<?php echo $curso['Anio']; ?>&division=<?php echo urlencode($curso['Division']); ?>&especialidad=<?php echo urlencode($curso['Especialidad']); ?>&turno=<?php echo urlencode($curso['Turno']); ?>" 
                                       class="btn btn-primary">
                                        📝 Revisar Notas
                                    </a>
                                    <a href="tomar_asistencia_curso.php?anio=<?php echo $curso['Anio']; ?>&division=<?php echo urlencode($curso['Division']); ?>&especialidad=<?php echo urlencode($curso['Especialidad']); ?>&turno=<?php echo urlencode($curso['Turno']); ?>" 
                                       class="btn btn-success">
                                        📋 Tomar Asistencia
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No tienes cursos asignados.</div>
        <?php endif; ?>
    </div>
</body>
</html>