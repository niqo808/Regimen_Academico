<?PHP
// HOME PHP
    //include('./public/headerLogin.php') // el archivo tiene el encabezado con el menu
    include('./conexion/conexion.php');
    include('./public/header.php');
    if (isset($_SESSION['error_login'])){
        echo "<script> alert('".$_SESSION['error_login']."') ;</script>";
        unset ($_SESSION['error_login']);
    }
    elseif(isset($_SESSION['error_usuario'])){
        echo "<script> alert('".$_SESSION['error_usuario']."') ;</script>";
        unset ($_SESSION['error_usuario']);
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./style/styles.css">
    <title>Dashboard - EEST N°2</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="welcome-section text-center py-5">
                    <div class="welcome-icon mb-4">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h1 class="display-4 mb-3">
                        Hola, <?php echo $_SESSION['nombre1']." ". $_SESSION['apellido']; ?>
                    </h1>
                    <p class="lead text-muted mb-4">
                        Sistema Académico EEST N°2 - República Argentina
                    </p>
                    
                    <div class="role-badge mb-4">
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            <i class="fas fa-user-tag me-2"></i>
                            <?php echo $_SESSION['rol']; ?>
                        </span>
                    </div>
                    
                    <div class="quick-actions mt-5">
                        <h3 class="mb-4">Accesos Rápidos</h3>
                        <div class="row justify-content-center">
                            <?php if ($_SESSION['rol'] == 'Alumno'): ?>
                                <div class="col-md-4 mb-3">
                                    <a href="mis_materias.php" class="btn btn-outline-primary btn-lg w-100">
                                        <i class="fas fa-book me-2"></i>Mis Materias
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="mis_inasistencias.php" class="btn btn-outline-warning btn-lg w-100">
                                        <i class="fas fa-calendar-times me-2"></i>Mis Inasistencias
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="generar_boletin.php" class="btn btn-outline-success btn-lg w-100">
                                        <i class="fas fa-file-alt me-2"></i>Mi Boletín
                                    </a>
                                </div>
                            <?php elseif ($_SESSION['rol'] == 'Profesor'): ?>
                                <div class="col-md-6 mb-3">
                                    <a href="mis_materias_profesor.php" class="btn btn-outline-primary btn-lg w-100">
                                        <i class="fas fa-chalkboard-teacher me-2"></i>Mis Materias
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="perfil.php" class="btn btn-outline-info btn-lg w-100">
                                        <i class="fas fa-user me-2"></i>Mi Perfil
                                    </a>
                                </div>
                            <?php elseif ($_SESSION['rol'] == 'Preceptor'): ?>
                                <div class="col-md-4 mb-3">
                                    <a href="revisar_notas_preceptor.php" class="btn btn-outline-primary btn-lg w-100">
                                        <i class="fas fa-check-circle me-2"></i>Revisar Notas
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="generar_boletin.php" class="btn btn-outline-success btn-lg w-100">
                                        <i class="fas fa-file-alt me-2"></i>Generar Boletín
                                    </a>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <a href="revisar_notas_preceptor.php" class="btn btn-outline-warning btn-lg w-100">
                                        <i class="fas fa-clipboard-check me-2"></i>Tomar Asistencia
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
