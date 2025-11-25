<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Verificar que sea director
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Director') {
    header("Location: index.php");
    exit;
}

// Obtener estadísticas detalladas
$query_estadisticas = "
    SELECT 
        (SELECT COUNT(*) FROM usuarios WHERE Rol = 'Alumno' AND Estado = 1) as total_alumnos,
        (SELECT COUNT(*) FROM usuarios WHERE Rol = 'Profesor' AND Estado = 1) as total_profesores,
        (SELECT COUNT(*) FROM usuarios WHERE Rol = 'Preceptor' AND Estado = 1) as total_preceptores,
        (SELECT COUNT(*) FROM cursos WHERE Estado = 1) as cursos_activos,
        (SELECT COUNT(*) FROM materias WHERE Estado = 1) as materias_activas,
        (SELECT COUNT(*) FROM notas WHERE Estado_Aprobacion = 'Pendiente') as notas_pendientes,
        (SELECT COUNT(*) FROM notas WHERE Estado_Aprobacion = 'Aprobado') as notas_aprobadas,
        (SELECT AVG(notaFinal) FROM notas WHERE notaFinal IS NOT NULL AND Estado_Aprobacion = 'Aprobado') as promedio_general,
        (SELECT COUNT(*) FROM inasistencias WHERE Tipo = 'Falta') as total_faltas,
        (SELECT COUNT(*) FROM inasistencias WHERE Tipo = 'Tarde') as total_tardes
";
$result_estadisticas = mysqli_query($CONN, $query_estadisticas);
$estadisticas = mysqli_fetch_assoc($result_estadisticas);

// Rendimiento por curso
$query_rendimiento = "SELECT 
                      cursos.Anio, cursos.Division, cursos.Especialidad,
                      COUNT(DISTINCT curso_alumno.DNI_Alumno) as total_alumnos,
                      AVG(notas.notaFinal) as promedio_curso,
                      SUM(CASE WHEN notas.notaFinal < 6 THEN 1 ELSE 0 END) as materias_desaprobadas
                      FROM cursos
                      LEFT JOIN curso_alumno ON cursos.ID = curso_alumno.ID_Curso
                      LEFT JOIN materias ON cursos.ID = materias.ID_Curso
                      LEFT JOIN notas ON (materias.ID = notas.id_materia AND curso_alumno.DNI_Alumno = notas.dni_alumno)
                      WHERE cursos.Estado = 1 AND notas.Estado_Aprobacion = 'Aprobado'
                      GROUP BY cursos.ID
                      ORDER BY cursos.Anio, cursos.Division";
$result_rendimiento = mysqli_query($CONN, $query_rendimiento);

// Top 10 alumnos con mejor promedio
$query_top_alumnos = "SELECT usuarios.DNI, usuarios.Primer_nombre, usuarios.Apellido,
                      AVG(notas.notaFinal) as promedio
                      FROM usuarios
                      INNER JOIN notas ON usuarios.DNI = notas.dni_alumno
                      WHERE usuarios.Rol = 'Alumno' 
                      AND notas.notaFinal IS NOT NULL 
                      AND notas.Estado_Aprobacion = 'Aprobado'
                      GROUP BY usuarios.DNI
                      HAVING COUNT(notas.notaFinal) >= 3
                      ORDER BY promedio DESC
                      LIMIT 10";
$result_top = mysqli_query($CONN, $query_top_alumnos);

// Alumnos con más inasistencias
$query_inasistencias = "SELECT usuarios.DNI, usuarios.Primer_nombre, usuarios.Apellido,
                        SUM(CASE WHEN inasistencias.Tipo = 'Falta' THEN 1 
                                 WHEN inasistencias.Tipo = 'Tarde' THEN 0.5 
                                 ELSE 0 END) as total_inasistencias
                        FROM usuarios
                        INNER JOIN inasistencias ON usuarios.DNI = inasistencias.DNI_Alumno
                        WHERE usuarios.Rol = 'Alumno'
                        GROUP BY usuarios.DNI
                        HAVING total_inasistencias > 15
                        ORDER BY total_inasistencias DESC
                        LIMIT 10";
$result_inasistencias = mysqli_query($CONN, $query_inasistencias);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes y Estadísticas</title>
    <link rel="stylesheet" href="./style/styles.css">
</head>
<body>
    <div class="container mt-4 mb-5">
        <h2><i class="fas fa-chart-bar me-2"></i>Reportes y Estadísticas</h2>
        <hr>

        <!-- Panel de Estadísticas Generales -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                        <h3><?php echo $estadisticas['total_alumnos']; ?></h3>
                        <p class="mb-0">Alumnos Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                        <h3><?php echo $estadisticas['total_profesores']; ?></h3>
                        <p class="mb-0">Profesores</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                        <h3><?php echo $estadisticas['notas_pendientes']; ?></h3>
                        <p class="mb-0">Notas Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <i class="fas fa-star fa-2x mb-2"></i>
                        <h3><?php echo number_format($estadisticas['promedio_general'], 2); ?></h3>
                        <p class="mb-0">Promedio General</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rendimiento por Curso -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Rendimiento por Curso</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Curso</th>
                                <th>Especialidad</th>
                                <th>Total Alumnos</th>
                                <th>Promedio</th>
                                <th>Materias Desaprobadas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($curso = mysqli_fetch_assoc($result_rendimiento)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($curso['Anio'] . "° " . $curso['Division']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($curso['Especialidad']); ?></td>
                                    <td><?php echo $curso['total_alumnos']; ?></td>
                                    <td>
                                        <?php 
                                        $promedio = $curso['promedio_curso'];
                                        $clase = $promedio >= 6 ? 'text-success' : 'text-danger';
                                        echo "<strong class='$clase'>" . number_format($promedio, 2) . "</strong>";
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($curso['materias_desaprobadas'] > 0): ?>
                                            <span class="badge bg-danger"><?php echo $curso['materias_desaprobadas']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success">0</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Top 10 Mejores Promedios -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 10 Mejores Promedios</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Alumno</th>
                                        <th>Promedio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $posicion = 1;
                                    while ($alumno = mysqli_fetch_assoc($result_top)): 
                                    ?>
                                        <tr>
                                            <td>
                                                <?php if ($posicion <= 3): ?>
                                                    <i class="fas fa-medal" style="color: <?php 
                                                        echo $posicion == 1 ? 'gold' : ($posicion == 2 ? 'silver' : '#cd7f32'); 
                                                    ?>"></i>
                                                <?php else: ?>
                                                    <?php echo $posicion; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($alumno['Apellido'] . ", " . $alumno['Primer_nombre']); ?></td>
                                            <td><strong class="text-success"><?php echo number_format($alumno['promedio'], 2); ?></strong></td>
                                        </tr>
                                    <?php 
                                        $posicion++;
                                    endwhile; 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alumnos con Más Inasistencias -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Alumnos con Más Inasistencias</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Alumno</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($alumno = mysqli_fetch_assoc($result_inasistencias)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($alumno['Apellido'] . ", " . $alumno['Primer_nombre']); ?></td>
                                            <td><strong><?php echo $alumno['total_inasistencias']; ?></strong></td>
                                            <td>
                                                <?php if ($alumno['total_inasistencias'] > 25): ?>
                                                    <span class="badge bg-danger">Crítico</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Alerta</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de navegación -->
        <div class="text-center">
            <a href="dashboard_director.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-lg">
                <i class="fas fa-print me-2"></i>Imprimir Reporte
            </button>
        </div>
    </div>
</body>
</html>
