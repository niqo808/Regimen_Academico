<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Verificar que sea director
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Director') {
    header("Location: index.php");
    exit;
}

// Obtener estadísticas generales
$query_stats = "
    SELECT 
        (SELECT COUNT(*) FROM usuarios WHERE Rol = 'Alumno' AND Estado = 1) as total_alumnos,
        (SELECT COUNT(*) FROM usuarios WHERE Rol = 'Profesor' AND Estado = 1) as total_profesores,
        (SELECT COUNT(*) FROM usuarios WHERE Rol = 'Preceptor' AND Estado = 1) as total_preceptores,
        (SELECT COUNT(*) FROM cursos WHERE Estado = 1) as total_cursos,
        (SELECT COUNT(*) FROM materias WHERE Estado = 1) as total_materias,
        (SELECT COUNT(*) FROM inasistencias WHERE Fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as inasistencias_mes
";
$result_stats = mysqli_query($CONN, $query_stats);
$stats = mysqli_fetch_assoc($result_stats);

// Obtener cursos activos
$query_cursos = "SELECT cursos.*, 
                 usuarios.Primer_nombre, usuarios.Apellido,
                 (SELECT COUNT(*) FROM curso_alumno WHERE curso_alumno.ID_Curso = cursos.ID AND curso_alumno.Estado = 1) as total_alumnos
                 FROM cursos
                 INNER JOIN preceptor ON cursos.DNI_Preceptor = preceptor.DNI_Preceptor
                 INNER JOIN usuarios ON preceptor.DNI_Preceptor = usuarios.DNI
                 WHERE cursos.Estado = 1
                 ORDER BY cursos.Anio, cursos.Division";
$result_cursos = mysqli_query($CONN, $query_cursos);

// Obtener últimas acciones de auditoría
$query_auditoria = "SELECT * FROM auditoria ORDER BY Fecha DESC LIMIT 10";
$result_auditoria = mysqli_query($CONN, $query_auditoria);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Director</title>
    <link rel="stylesheet" href="./style/styles.css">
</head>
<body>
    <div class="container mt-4">
        <div class="welcome-section text-center py-4 mb-4">
            <h1 class="display-4">Panel de Dirección</h1>
            <p class="lead text-muted">Sistema de Gestión Académica - EEST N°2</p>
        </div>

        <!-- Estadísticas Generales -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-user-graduate fa-3x mb-3"></i>
                        <h2><?php echo $stats['total_alumnos']; ?></h2>
                        <p class="mb-0">Alumnos Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                        <h2><?php echo $stats['total_profesores']; ?></h2>
                        <p class="mb-0">Profesores</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                        <h2><?php echo $stats['total_preceptores']; ?></h2>
                        <p class="mb-0">Preceptores</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-book fa-3x mb-3"></i>
                        <h2><?php echo $stats['total_cursos']; ?></h2>
                        <p class="mb-0">Cursos Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-secondary">
                    <div class="card-body text-center">
                        <i class="fas fa-book-open fa-3x mb-3"></i>
                        <h2><?php echo $stats['total_materias']; ?></h2>
                        <p class="mb-0">Materias</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-white bg-danger">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <h2><?php echo $stats['inasistencias_mes']; ?></h2>
                        <p class="mb-0">Inasistencias (últimos 30 días)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><i class="fas fa-tools me-2"></i>Gestión del Sistema</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="gestionar_usuarios_director.php" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-users fa-2x d-block mb-2"></i>
                            Gestionar Usuarios
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="gestionar_cursos_director.php" class="btn btn-success w-100 py-3">
                            <i class="fas fa-school fa-2x d-block mb-2"></i>
                            Gestionar Cursos
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="ver_todos_boletines.php" class="btn btn-info w-100 py-3">
                            <i class="fas fa-file-alt fa-2x d-block mb-2"></i>
                            Ver Boletines
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="reportes_director.php" class="btn btn-warning w-100 py-3">
                            <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                            Reportes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Cursos -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-list me-2"></i>Cursos Activos</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Curso</th>
                                <th>Especialidad</th>
                                <th>Turno</th>
                                <th>Preceptor</th>
                                <th>Alumnos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($curso = mysqli_fetch_assoc($result_cursos)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($curso['Anio'] . "° " . $curso['Division']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($curso['Especialidad']); ?></td>
                                    <td><?php echo htmlspecialchars($curso['Turno']); ?></td>
                                    <td><?php echo htmlspecialchars($curso['Primer_nombre'] . " " . $curso['Apellido']); ?></td>
                                    <td><span class="badge bg-primary"><?php echo $curso['total_alumnos']; ?></span></td>
                                    <td>
                                        <a href="ver_detalle_curso.php?id=<?php echo $curso['ID']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Auditoría Reciente -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0"><i class="fas fa-history me-2"></i>Actividad Reciente</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tabla</th>
                                <th>Acción</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($audit = mysqli_fetch_assoc($result_auditoria)): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($audit['Fecha'])); ?></td>
                                    <td><?php echo htmlspecialchars($audit['Tabla']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $audit['Accion'] == 'INSERT' ? 'success' : 
                                                ($audit['Accion'] == 'DELETE' ? 'danger' : 'warning'); 
                                        ?>">
                                            <?php echo htmlspecialchars($audit['Accion']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($audit['Usuario']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
