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
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./style/styles.css">
    <title>Home Academico</title>
</head>
<body>
    <h1>Bienvenido <?php echo $_SESSION['nombre1']." ". $_SESSION['apellido']; ?></h1>
</body>
</html>
