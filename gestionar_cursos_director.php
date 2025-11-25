<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Verificar que sea director
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Director') {
    header("Location: index.php");
    exit;
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['crear_curso'])) {
        $dni_preceptor = mysqli_real_escape_string($CONN, $_POST['dni_preceptor']);
        $turno = mysqli_real_escape_string($CONN, $_POST['turno']);
        $grupo = mysqli_real_escape_string($CONN, $_POST['grupo']);
        $anio = mysqli_real_escape_string($CONN, $_POST['anio']);
        $division = mysqli_real_escape_string($CONN, $_POST['division']);
        $especialidad = mysqli_real_escape_string($CONN, $_POST['especialidad']);
        
        $query_insert = "INSERT INTO cursos (DNI_Preceptor, Turno, Grupo, Anio, Division, Especialidad, Estado) 
                        VALUES ('$dni_preceptor', '$turno', '$grupo', '$anio', '$division', '$especialidad', 1)";
        
        if (mysqli_query($CONN, $query_insert)) {
            echo "<script>alert('Curso creado exitosamente');</script>";
        } else {
            echo "<script>alert('Error al crear curso');</script>";
        }
    }
    
    if (isset($_POST['editar_curso'])) {
        $id_curso = mysqli_real_escape_string($CONN, $_POST['id_curso']);
        $dni_preceptor = mysqli_real_escape_string($CONN, $_POST['dni_preceptor']);
        $turno = mysqli_real_escape_string($CONN, $_POST['turno']);
        $grupo = mysqli_real_escape_string($CONN, $_POST['grupo']);
        
        $query_update = "UPDATE cursos SET 
                        DNI_Preceptor = '$dni_preceptor',
                        Turno = '$turno',
                        Grupo = '$grupo'
                        WHERE ID = '$id_curso'";
        
        if (mysqli_query($CONN, $query_update)) {
            echo "<script>alert('Curso actualizado exitosamente');</script>";
        }
    }
    
    if (isset($_POST['cambiar_estado_curso'])) {
        $id_curso = mysqli_real_escape_string($CONN, $_POST['id_curso']);
        $estado = mysqli_real_escape_string($CONN, $_POST['estado']);
        
        $query_estado = "UPDATE cursos SET Estado = '$estado' WHERE ID = '$id_curso'";
        
        if (mysqli_query($CONN, $query_estado)) {
            echo "<script>alert('Estado actualizado exitosamente');</script>";
        }
    }
}

// Obtener todos los cursos
$query_cursos = "SELECT cursos.*, 
                 usuarios.Primer_nombre, usuarios.Apellido,
                 (SELECT COUNT(*) FROM curso_alumno WHERE curso_alumno.ID_Curso = cursos.ID AND curso_alumno.Estado = 1) as total_alumnos,
                 (SELECT COUNT(*) FROM materias WHERE materias.ID_Curso = cursos.ID AND materias.Estado = 1) as total_materias
                 FROM cursos
                 LEFT JOIN preceptor ON cursos.DNI_Preceptor = preceptor.DNI_Preceptor
                 LEFT JOIN usuarios ON preceptor.DNI_Preceptor = usuarios.DNI
                 ORDER BY cursos.Estado DESC, cursos.Anio, cursos.Division";
$result_cursos = mysqli_query($CONN, $query_cursos);

// Obtener preceptores para el formulario
$query_preceptores = "SELECT usuarios.DNI, usuarios.Primer_nombre, usuarios.Apellido 
                      FROM usuarios 
                      WHERE usuarios.Rol = 'Preceptor' AND usuarios.Estado = 1
                      ORDER BY usuarios.Apellido";
$result_preceptores = mysqli_query($CONN, $query_preceptores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Cursos</title>
    <link rel="stylesheet" href="./style/styles.css">
</head>
<body>
    <div class="container mt-4 mb-5">
        <h2><i class="fas fa-school me-2"></i>Gestión de Cursos</h2>
        <hr>

        <!-- Botones de Acción -->
        <div class="mb-3">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearCurso">
                <i class="fas fa-plus me-2"></i>Crear Nuevo Curso
            </button>
            <a href="dashboard_director.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>

        <!-- Tabla de Cursos -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Curso</th>
                                <th>Especialidad</th>
                                <th>Turno</th>
                                <th>Grupo</th>
                                <th>Preceptor</th>
                                <th>Alumnos</th>
                                <th>Materias</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($curso = mysqli_fetch_assoc($result_cursos)): ?>
                                <tr class="<?php echo $curso['Estado'] == 0 ? 'table-secondary' : ''; ?>">
                                    <td><?php echo $curso['ID']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($curso['Anio'] . "° " . $curso['Division']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($curso['Especialidad']); ?></td>
                                    <td><?php echo htmlspecialchars($curso['Turno']); ?></td>
                                    <td><?php echo htmlspecialchars($curso['Grupo']); ?></td>
                                    <td>
                                        <?php 
                                        if ($curso['Primer_nombre']) {
                                            echo htmlspecialchars($curso['Primer_nombre'] . " " . $curso['Apellido']);
                                        } else {
                                            echo "<span class='text-muted'>Sin asignar</span>";
                                        }
                                        ?>
                                    </td>
                                    <td><span class="badge bg-primary"><?php echo $curso['total_alumnos']; ?></span></td>
                                    <td><span class="badge bg-success"><?php echo $curso['total_materias']; ?></span></td>
                                    <td>
                                        <?php if ($curso['Estado'] == 1): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="ver_detalle_curso.php?id=<?php echo $curso['ID']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning" onclick="editarCurso(<?php echo $curso['ID']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-<?php echo $curso['Estado'] == 1 ? 'danger' : 'success'; ?>" 
                                                onclick="cambiarEstadoCurso(<?php echo $curso['ID']; ?>, <?php echo $curso['Estado'] == 1 ? 0 : 1; ?>)">
                                            <i class="fas fa-<?php echo $curso['Estado'] == 1 ? 'ban' : 'check'; ?>"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Curso -->
    <div class="modal fade" id="modalCrearCurso" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Crear Nuevo Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Año *</label>
                            <select name="anio" class="form-control" required>
                                <option value="1">1°</option>
                                <option value="2">2°</option>
                                <option value="3">3°</option>
                                <option value="4">4°</option>
                                <option value="5">5°</option>
                                <option value="6">6°</option>
                                <option value="7">7°</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">División *</label>
                            <input type="text" name="division" class="form-control" required placeholder="Ej: 1ra, 2da, 3ra">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Especialidad *</label>
                            <input type="text" name="especialidad" class="form-control" required placeholder="Ej: Informática">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Turno *</label>
                            <select name="turno" class="form-control" required>
                                <option value="Mañana">Mañana</option>
                                <option value="Tarde">Tarde</option>
                                <option value="Noche">Noche</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Grupo *</label>
                            <input type="text" name="grupo" class="form-control" required placeholder="Ej: A, B">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preceptor *</label>
                            <select name="dni_preceptor" class="form-control" required>
                                <option value="">Seleccione un preceptor</option>
                                <?php 
                                mysqli_data_seek($result_preceptores, 0);
                                while ($preceptor = mysqli_fetch_assoc($result_preceptores)): 
                                ?>
                                    <option value="<?php echo $preceptor['DNI']; ?>">
                                        <?php echo htmlspecialchars($preceptor['Apellido'] . ", " . $preceptor['Primer_nombre']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="crear_curso" class="btn btn-success">Crear Curso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para cambiar estado -->
    <form id="formCambiarEstadoCurso" method="POST" style="display: none;">
        <input type="hidden" name="id_curso" id="id_curso_cambiar">
        <input type="hidden" name="estado" id="nuevo_estado_curso">
        <input type="hidden" name="cambiar_estado_curso" value="1">
    </form>

    <script>
    function cambiarEstadoCurso(idCurso, nuevoEstado) {
        if (confirm('¿Está seguro de cambiar el estado de este curso?')) {
            document.getElementById('id_curso_cambiar').value = idCurso;
            document.getElementById('nuevo_estado_curso').value = nuevoEstado;
            document.getElementById('formCambiarEstadoCurso').submit();
        }
    }

    function editarCurso(idCurso) {
        window.location.href = 'editar_curso_director.php?id=' + idCurso;
    }
    </script>
</body>
</html>
