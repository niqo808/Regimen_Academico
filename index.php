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
        <img src="./imagenes/FrenteEscuela.jpeg" alt="Frente escuela">
    </div>
</body>
</html>
