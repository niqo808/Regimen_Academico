<?PHP
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/styles.css">
    <title>EEST N°2 - República Argentina</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h1 class="display-4 mb-3">Bienvenido al Portal Académico</h1>
                    <p class="lead text-muted">EEEST N°2 - República Argentina</p>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <img src="./imagenes/FrenteEscuela.jpeg" alt="Frente de la escuela" class="img-fluid">
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    <a href="login.php" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </a>
                    <a href="registro_usuarios.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Registrarse
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
