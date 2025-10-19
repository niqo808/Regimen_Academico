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
    <title>EEST N°2</title>
</head>
<body>
    <div align="center">
        <h1>Iniciar Sesión</h1>
        <br>
        <!-- El action apunta a 'autenticar_login.php' que procesará este formulario -->
        <form id="loginForm" action="autenticar_login.php" method="post">
            <div>
                <label for="gmail">Correo electrónico</label>
                <input type="text" id="email" name="gmail" required>
            </div>
            <br>
            <div>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" >
            </div>
            <br>
            <div>
                <button type="submit">Ingresar</button>
            </div>
        </form>
        <br>
        <form action="registro_usuarios.php" method="post">
            <div>
               <button type="submit">Registrarse</button>  
            </div>
        </form>
    </div>
</body>
</html>
