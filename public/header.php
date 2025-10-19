<?php
    include('./conexion/conexion.php');
?> 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="./style/styles.css">
    <title>Sistema Académico - EEST N°2</title>
</head>
<body>

<!-- Header Profesional -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" 
            <?php 
                if (!isset($_SESSION['nombre1'])) {
                    echo 'href="./index.php"';
                } else {
                    echo 'href="./home.php"';
                }
            ?>>
            <div class="brand-icon me-2">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="brand-text">
                <div class="brand-title">EEST N°2</div>
                <div class="brand-subtitle">República Argentina</div>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (isset($_SESSION['nombre1'])):  // SESION INICIADA ?>
            <ul class="navbar-nav ms-auto">
                <?php if ($_SESSION['rol'] == 'Alumno'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="alumnoDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-graduate me-2"></i>
                            Mi Campus
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="./mis_materias.php"><i class="fas fa-book me-2"></i>Mis Materias</a></li>
                            <li><a class="dropdown-item" href="./mis_inasistencias.php"><i class="fas fa-calendar-times me-2"></i>Mis Inasistencias</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="./generar_boletin.php"><i class="fas fa-file-alt me-2"></i>Mi Boletín</a></li>
                        </ul>
                    </li>
                <?php elseif ($_SESSION['rol'] == 'Profesor'): ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="./mis_materias_profesor.php">
                            <i class="fas fa-chalkboard-teacher me-2"></i>
                            Mis Materias
                        </a>
                    </li>
                <?php elseif ($_SESSION['rol'] == 'Preceptor'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="preceptorDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Gestión de Curso
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="./revisar_notas_preceptor.php"><i class="fas fa-check-circle me-2"></i>Revisar y Aprobar Notas</a></li>
                            <li><a class="dropdown-item" href="./generar_boletin.php"><i class="fas fa-file-alt me-2"></i>Generar Boletín</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="./revisar_notas_preceptor.php"><i class="fas fa-clipboard-check me-2"></i>Tomar Asistencia por Curso</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="proyectoDropdown" role="button" data-bs-toggle="dropdown">
                            Menu desplegable (Modificar)
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="./cargar_autos.php">.........</a></li>
                            <li id="modificarAuto"><a class="dropdown-item" href="./buscar.php">.........</a></li>
                            <li id="bajaAuto"><a class="dropdown-item" href="./listarAutos.php">.........</a></li>
                            <li id="Listado"><a class="dropdown-item" href="./listarAutos.php">.........</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php echo $_SESSION['nombre1']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="./perfil.php"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="./logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
            <?php else: // SESION NO INICIADA?>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="login.php">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Iniciar Sesión
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm ms-2" href="registro_usuarios.php">
                        <i class="fas fa-user-plus me-1"></i>
                        Registrarse
                    </a>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

</body>
</html>