<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Verificar que sea director o preceptor
if (!isset($_SESSION['DNI']) || ($_SESSION['rol'] != 'Director' && $_SESSION['rol'] != 'Preceptor')) {
    header("Location: index.php");
    exit;
}

$id_curso = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$id_curso) {
    header("Location: dashboard_director.php");
    exit;
}

// Obtener información del curso
$query_curso = "SELECT cursos.*, 
                usuarios.Primer_nombre as Preceptor_Nombre, 
                usuarios.Apellido as Preceptor_Apellido,
                usuarios.Email as Preceptor_Email
                FROM cursos
                LEFT JOIN preceptor ON cursos.DNI_Preceptor = preceptor.DNI_Preceptor
                LEFT JOIN usuarios ON preceptor.DNI_Preceptor = usuarios.DNI
                WHERE cursos.ID = '$id_curso'";
$result_curso = mysqli_query($CONN, $query_curso);

if (mysqli_num_rows($result_curso) == 0) {
    echo "<script>alert('Curso no encontrado'); window.location='dashboard_director.php';</script>";
    exit;
}

$curso = mysqli_fetch_assoc($result_curso);

// Obtener alumnos del curso
$query_alumnos = "SELECT usuarios.DNI, usuarios.Primer_nombre, usuarios.Segundo_nombre, usuarios.Apellido, usuarios.Email,
                  (SELECT COUNT(*) FROM inasistencias WHERE inasistencias.DNI_Alumno = usuarios.DNI) as total_inasistencias,
                  (SELECT AVG(notas.notaFinal) FROM notas 
                   INNER JOIN materias ON notas.id_materia = materias.ID 
                   WHERE notas.dni_alumno = usuarios.DNI 
                   AND materias.ID_Curso = '$id_curso' 
                   AND notas.notaFinal IS NOT NULL) as promedio
                  FROM usuarios
                  INNER JOIN alumnos ON usuarios.DNI = alumnos.DNI_Alumno
                  INNER JOIN curso_alumno ON alumnos.DNI_Alumno = curso_alumno.DNI_Alumno
                  WHERE curso_alumno.ID_Curso = '$id_curso' AND curso_alumno.Estado = 1
                  ORDER BY usuarios.Apellido, usuarios.Primer_nombre";
$result_alumnos = mysqli_query($CONN, $query_alumnos);

// Obtener materias del curso
$query_materias = "SELECT materias.*, 
                   usuarios.Primer_nombre as Profesor_Nombre, 
                   usuarios.Apellido as Profesor_Apellido
                   FROM materias
                   LEFT JOIN profesores ON materias.DNI_Profesor = profesores.DNI_Profesor
                   LEFT JOIN usuarios ON profesores.DNI_Profesor = usuarios.DNI
                   WHERE materias.ID_Curso = '$id_curso' AND materias.Estado = 1
                   ORDER BY materias.Nombre";
$result_materias = mysqli_query($CONN, $query_materias);

// Estadísticas del curso
$total_alumnos = mysqli_num_rows($result_alumnos);
$total_materias = mysqli_num_rows($result_materias);

// Promedio general del curso
$query_promedio_curso = "SELECT AVG(notas.notaFinal) as promedio_general
                         FROM notas
                         INNER JOIN materias ON notas.id_materia = materias.ID
                         WHERE materias.ID_Curso = '$id_curso' 
                         AND notas.notaFinal IS NOT NULL";
$result_promedio = mysqli_query($CONN, $query_promedio_curso);
$promedio_curso = mysqli_fetch_assoc($result_promedio)['promedio_general'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Curso</title>
    <link rel="stylesheet" href="./style/styles.css">
</head>
<body>
    <div class="container mt-4 mb-5">
        <!-- Encabezado del Curso -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">
                    <i class="fas fa-school me-2"></i>
                    <?php echo htmlspecialchars($curso['Anio'] . "° Año - División " . $curso['Division']); ?>
                </h2>
                <p class="mb-0"><?php echo htmlspecialchars($curso['Especialidad'] . " - Turno " . $curso['Turno'] . " - Grupo " . $curso['Grupo']); ?></p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-user-tie me-2"></i>Preceptor</h5>
                        <p>
                            <?php 
                            if ($curso['Preceptor_Nombre']) {
                                echo htmlspecialchars($curso['Preceptor_Nombre'] . " " . $curso['Preceptor_Apellido']);
                                echo "<br><small class='text-muted'>" . htmlspecialchars($curso['Preceptor_Email']) . "</small>";
                            } else {
                                echo "<span class='text-muted'>Sin preceptor asignado</span>";
                            }
                            ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="stat-card bg-light">
                                    <h3 class="text-primary"><?php echo $total_alumnos; ?></h3>
                                    <small>Alumnos</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-card bg-light">
                                    <h3 class="text-success"><?php echo $total_materias; ?></h3>
                                    <small>Materias</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-card bg-light">
                                    <h3 class="text-info"><?php echo number_format($promedio_curso, 2); ?></h3>
                                    <small>Promedio</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Materias del Curso -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="fas fa-book me-2"></i>Materias</h4>
            </div>
            <div class="card-body">
                <?php if ($total_materias > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Materia</th>
                                    <th>Profesor</th>
                                    <th>Horarios</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                mysqli_data_seek($result_materias, 0);
                                while ($materia = mysqli_fetch_assoc($result_materias)): 
                                ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($materia['Nombre']); ?></strong></td>
                                        <td>
                                            <?php 
                                            if ($materia['Profesor_Nombre']) {
                                                echo htmlspecialchars($materia['Profesor_Nombre'] . " " . $materia['Profesor_Apellido']);
                                            } else {
                                                echo "<span class='text-muted'>Sin profesor</span>";
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($materia['Horarios']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No hay materias asignadas a este curso.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Alumnos del Curso -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0"><i class="fas fa-users me-2"></i>Alumnos del Curso</h4>
            </div>
            <div class="card-body">
                <?php if ($total_alumnos > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>DNI</th>
                                    <th>Apellido y Nombre</th>
                                    <th>Email</th>
                                    <th>Promedio</th>
                                    <th>Inasistencias</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                mysqli_data_seek($result_alumnos, 0);
                                while ($alumno = mysqli_fetch_assoc($result_alumnos)): 
                                ?>
                                    <tr>
                                        <td><?php echo number_format($alumno['DNI'], 0, '', '.'); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($alumno['Apellido'] . ", " . $alumno['Primer_nombre']); ?></strong>
                                            <?php if (!empty($alumno['Segundo_nombre'])): ?>
                                                <?php echo htmlspecialchars(" " . $alumno['Segundo_nombre']); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($alumno['Email']); ?></td>
                                        <td>
                                            <?php 
                                            $promedio = $alumno['promedio'];
                                            if ($promedio !== null) {
                                                $clase = $promedio >= 6 ? 'text-success' : 'text-danger';
                                                echo "<strong class='$clase'>" . number_format($promedio, 2) . "</strong>";
                                            } else {
                                                echo "<span class='text-muted'>-</span>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $alumno['total_inasistencias'] > 25 ? 'danger' : 'warning'; ?>">
                                                <?php echo $alumno['total_inasistencias']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="generar_boletin.php?dni_alumno=<?php echo $alumno['DNI']; ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-file-alt"></i> Boletín
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">No hay alumnos inscriptos en este curso.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Botones de navegación -->
        <div class="text-center">
            <?php if ($_SESSION['rol'] == 'Director'): ?>
                <a href="gestionar_cursos_director.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Cursos
                </a>
                <a href="dashboard_director.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i>Dashboard
                </a>
            <?php else: ?>
                <a href="revisar_notas_preceptor.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
