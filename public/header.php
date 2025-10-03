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
<title>Sistemas academico</title>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand"
            <?php 
                if (!isset($_SESSION['nombre1'])) {
                    sleep(1);
                    echo 'href="./index.php"';
                } else {
                    sleep(1);
                    echo 'href="./home.php"';
                }
            ?> >Técnica Nº2 República Argentina
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (isset($_SESSION['nombre1'])):  // SESION INICIADA ?>
                <ul class="navbar-nav ms-auto">
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
      
                    <li class="nav-item">
                        <a class="nav-link" href="./perfil.php">Perfil de <?php echo $_SESSION['nombre1']; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./logout.php">Cerrar sesión</a>
                    </li>
                </ul>
            <?php else: // SESION NO INICIADA?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white me-2" href="login.php">Iniciar sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="registro_usuarios.php">Registrarse</a>
                    </li>
                </ul>

            <?php endif; ?>
        </div>
    </div>
</nav>

</body>
</html>