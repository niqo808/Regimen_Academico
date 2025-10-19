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
    <title>Iniciar Sesión - EEST N°2</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="form-container fade-in">
                    <div class="text-center mb-4">
                        <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                        <h1 class="h3 mb-3">Iniciar Sesión</h1>
                        <p class="text-muted">Accede a tu cuenta del sistema académico</p>
                    </div>
                    
                    <form id="loginForm" action="autenticar_login.php" method="post">
                        <div class="form-group mb-3">
                            <label for="gmail" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Correo electrónico
                            </label>
                            <input type="email" class="form-control" id="email" name="gmail" required 
                                   placeholder="tu@email.com">
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Contraseña
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required
                                   placeholder="Tu contraseña">
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="text-muted mb-3">¿No tienes cuenta?</p>
                        <a href="registro_usuarios.php" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>Registrarse
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
