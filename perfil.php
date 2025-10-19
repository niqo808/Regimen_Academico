<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['DNI'])) {
    header("Location: index.php");
    exit;
}

// Obtener todos los datos actualizados del usuario desde la base de datos
$dni = $_SESSION['DNI'];
$query_usuario = "SELECT * FROM usuarios WHERE usuarios.DNI = '$dni'";
$result_usuario = mysqli_query($CONN, $query_usuario);
$usuario = mysqli_fetch_assoc($result_usuario);

// Aquí decidimos qué información adicional traer según el rol
$info_adicional = [];
$rol = $usuario['Rol'];

if ($rol == 'Alumno') {
    // Para un alumno, buscamos su curso actual
    $query_curso = "SELECT cursos.Anio, cursos.Division, cursos.Especialidad, cursos.Turno, cursos.Grupo 
                    FROM cursos 
                    WHERE cursos.DNI_Alumno = '$dni' AND cursos.Estado = 1";
    $result_curso = mysqli_query($CONN, $query_curso);
    if (mysqli_num_rows($result_curso) > 0) {
        $info_adicional['curso'] = mysqli_fetch_assoc($result_curso);
    }
    
    // También podríamos contar sus materias
    if (isset($info_adicional['curso'])) {
        $query_materias = "SELECT COUNT(*) as total FROM materias
                          INNER JOIN cursos ON materias.ID_Curso = cursos.ID
                          WHERE cursos.DNI_Alumno = '$dni' AND materias.Estado = 1";
        $result_materias = mysqli_query($CONN, $query_materias);
        $info_adicional['total_materias'] = mysqli_fetch_assoc($result_materias)['total'];
    }
    
    // Contar inasistencias totales
    $query_inasistencias = "SELECT 
                              SUM(CASE WHEN inasistencias.Tipo = 'Falta' THEN 1 ELSE 0 END) +
                              SUM(CASE WHEN inasistencias.Tipo = 'Tarde' THEN 0.5 ELSE 0 END) as total
                            FROM inasistencias 
                            WHERE inasistencias.DNI_Alumno = '$dni'";
    $result_inasistencias = mysqli_query($CONN, $query_inasistencias);
    $info_adicional['total_inasistencias'] = mysqli_fetch_assoc($result_inasistencias)['total'] ?? 0;
    
} elseif ($rol == 'Profesor') {
    // Para un profesor, contamos cuántas materias dicta
    $query_materias = "SELECT COUNT(*) as total FROM materias 
                       WHERE materias.DNI_Profesor = '$dni' AND materias.Estado = 1";
    $result_materias = mysqli_query($CONN, $query_materias);
    $info_adicional['materias_dictadas'] = mysqli_fetch_assoc($result_materias)['total'];
    
    // También podríamos listar las materias que dicta
    $query_lista_materias = "SELECT materias.Nombre, cursos.Anio, cursos.Division 
                             FROM materias
                             INNER JOIN cursos ON materias.ID_Curso = cursos.ID
                             WHERE materias.DNI_Profesor = '$dni' AND materias.Estado = 1
                             ORDER BY cursos.Anio, cursos.Division, materias.Nombre";
    $result_lista = mysqli_query($CONN, $query_lista_materias);
    $info_adicional['lista_materias'] = [];
    while ($materia = mysqli_fetch_assoc($result_lista)) {
        $info_adicional['lista_materias'][] = $materia;
    }
    
} elseif ($rol == 'Preceptor') {
    // Para un preceptor, contamos cuántos cursos tiene a cargo
    $query_cursos = "SELECT COUNT(DISTINCT cursos.ID) as total FROM cursos 
                     WHERE cursos.DNI_Preceptor = '$dni' AND cursos.Estado = 1";
    $result_cursos = mysqli_query($CONN, $query_cursos);
    $info_adicional['cursos_a_cargo'] = mysqli_fetch_assoc($result_cursos)['total'];
    
    // Listar los cursos
    $query_lista_cursos = "SELECT cursos.Anio, cursos.Division, cursos.Especialidad, cursos.Turno 
                          FROM cursos 
                          WHERE cursos.DNI_Preceptor = '$dni' AND cursos.Estado = 1
                          ORDER BY cursos.Anio, cursos.Division";
    $result_lista = mysqli_query($CONN, $query_lista_cursos);
    $info_adicional['lista_cursos'] = [];
    while ($curso = mysqli_fetch_assoc($result_lista)) {
        $info_adicional['lista_cursos'][] = $curso;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            margin: 0 auto 20px;
            border: 4px solid white;
        }
        .info-card {
            border-left: 4px solid #667eea;
            margin-bottom: 20px;
        }
        .stat-card {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-5">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php 
                echo strtoupper(substr($usuario['Primer_nombre'], 0, 1) . substr($usuario['Apellido'], 0, 1)); 
                ?>
            </div>
            <h2><?php echo htmlspecialchars($usuario['Primer_nombre'] . ' ' . $usuario['Segundo_nombre'] . ' ' . $usuario['Apellido']); ?></h2>
            <p class="mb-0">
                <span class="badge bg-light text-dark fs-6">
                    <?php echo htmlspecialchars($usuario['Rol']); ?>
                </span>
            </p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card info-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-fill"></i> Datos Personales</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>DNI:</strong><br>
                            <?php echo htmlspecialchars(number_format($usuario['DNI'], 0, '', '.')); ?>
                        </div>
                        <div class="mb-3">
                            <strong>Fecha de Nacimiento:</strong><br>
                            <?php echo date('d/m/Y', strtotime($usuario['Fecha_Nacimiento'])); ?>
                            <?php 
                            $nacimiento = new DateTime($usuario['Fecha_Nacimiento']);
                            $hoy = new DateTime();
                            $edad = $hoy->diff($nacimiento)->y;
                            echo " <span class='text-muted'>($edad años)</span>";
                            ?>
                        </div>
                        <div class="mb-3">
                            <strong>Nacionalidad:</strong><br>
                            <?php echo htmlspecialchars($usuario['Nacionalidad']); ?>
                        </div>
                    </div>
                </div>

                <div class="card info-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-geo-alt-fill"></i> Domicilio</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Localidad:</strong><br>
                            <?php echo htmlspecialchars($usuario['Localidad']); ?>
                        </div>
                        <div class="mb-3">
                            <strong>Dirección:</strong><br>
                            <?php echo htmlspecialchars($usuario['Calle'] . ' ' . $usuario['Altura']); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card info-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-telephone-fill"></i> Contacto</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Email:</strong><br>
                            <a href="mailto:<?php echo htmlspecialchars($usuario['Email']); ?>">
                                <?php echo htmlspecialchars($usuario['Email']); ?>
                            </a>
                        </div>
                        <div class="mb-3">
                            <strong>Teléfono:</strong><br>
                            <a href="tel:<?php echo htmlspecialchars($usuario['Telefono']); ?>">
                                <?php echo htmlspecialchars($usuario['Telefono']); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <?php if ($rol == 'Alumno' && isset($info_adicional['curso'])): ?>
                    <div class="card info-card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-book-fill"></i> Información Académica</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Curso Actual:</strong><br>
                                <?php 
                                $curso = $info_adicional['curso'];
                                echo htmlspecialchars($curso['Anio'] . '° Año - División ' . $curso['Division']); 
                                ?>
                            </div>
                            <div class="mb-3">
                                <strong>Especialidad:</strong><br>
                                <?php echo htmlspecialchars($curso['Especialidad']); ?>
                            </div>
                            <div class="mb-3">
                                <strong>Turno:</strong><br>
                                <?php echo htmlspecialchars($curso['Turno']); ?>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="stat-card bg-light">
                                        <h3 class="text-primary"><?php echo $info_adicional['total_materias'] ?? 0; ?></h3>
                                        <small class="text-muted">Materias</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card bg-light">
                                        <h3 class="text-danger"><?php echo $info_adicional['total_inasistencias']; ?></h3>
                                        <small class="text-muted">Inasistencias</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($rol == 'Profesor'): ?>
                    <div class="card info-card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-mortarboard-fill"></i> Información Docente</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Materias que dicta:</strong>
                                <h3 class="text-primary"><?php echo $info_adicional['materias_dictadas']; ?></h3>
                            </div>
                            <?php if (!empty($info_adicional['lista_materias'])): ?>
                                <hr>
                                <strong>Detalle de materias:</strong>
                                <ul class="list-group mt-2">
                                    <?php foreach ($info_adicional['lista_materias'] as $materia): ?>
                                        <li class="list-group-item">
                                            <strong><?php echo htmlspecialchars($materia['Nombre']); ?></strong><br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($materia['Anio'] . '° ' . $materia['Division']); ?>
                                            </small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($rol == 'Preceptor'): ?>
                    <div class="card info-card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-clipboard-check-fill"></i> Información de Preceptoría</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Cursos a cargo:</strong>
                                <h3 class="text-primary"><?php echo $info_adicional['cursos_a_cargo']; ?></h3>
                            </div>
                            <?php if (!empty($info_adicional['lista_cursos'])): ?>
                                <hr>
                                <strong>Detalle de cursos:</strong>
                                <ul class="list-group mt-2">
                                    <?php foreach ($info_adicional['lista_cursos'] as $curso): ?>
                                        <li class="list-group-item">
                                            <strong><?php echo htmlspecialchars($curso['Anio'] . '° ' . $curso['Division']); ?></strong><br>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($curso['Especialidad'] . ' - Turno ' . $curso['Turno']); ?>
                                            </small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($rol == 'Director'): ?>
                    <div class="card info-card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-award-fill"></i> Información Directiva</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Rol de dirección del establecimiento educativo.</p>
                            <p><strong>Permisos completos del sistema</strong></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card info-card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="bi bi-shield-lock-fill"></i> Información de Cuenta</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Usuario desde:</strong><br>
                            <?php echo date('d/m/Y', strtotime($usuario['Fecha_Creacion'])); ?>
                        </div>
                        <div class="mb-3">
                            <strong>Estado de cuenta:</strong><br>
                            <?php if ($usuario['Estado'] == 1): ?>
                                <span class="badge bg-success">Activa</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactiva</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="home.php" class="btn btn-primary">Volver al Inicio</a>
        </div>
    </div>
</body>
</html>