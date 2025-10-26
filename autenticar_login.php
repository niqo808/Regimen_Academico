<?php
include('./conexion/conexion.php');

// Recibir datos del formulario
$dni = trim($_POST['dni']);
$password_ingresada = $_POST['password'];

// Validar que los campos no estén vacíos
if (empty($dni) || empty($password_ingresada)) {
    $_SESSION['error_login'] = "Por favor, complete todos los campos.";
    header("Location: login.php");
    exit;
}

// Validar formato del DNI (7-8 dígitos numéricos)
if (!is_numeric($dni) || strlen($dni) < 7 || strlen($dni) > 8) {
    $_SESSION['error_login'] = "DNI inválido. Debe tener 7 u 8 dígitos.";
    header("Location: login.php");
    exit;
}

// Buscar usuario por DNI
$QUERY = "SELECT * FROM usuarios WHERE DNI = '$dni'";
$result = mysqli_query($CONN, $QUERY) or die("Error en la consulta: " . mysqli_error($CONN));

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    
    // Verificar si el usuario tiene contraseña configurada
    if (empty($row['Password_Usuario'])) {
        $_SESSION['error_login'] = "Tu cuenta aún no tiene contraseña configurada. Por favor, regístrate primero.";
        header("Location: registro_usuarios.php");
        exit;
    }
    
    $password_hasheada = $row['Password_Usuario'];
    
    // Verificar la contraseña
    if (password_verify($password_ingresada, $password_hasheada)) {
        // Contraseña correcta - Iniciar sesión
        $_SESSION['DNI'] = $row['DNI'];
        $_SESSION['nombre1'] = $row['Primer_nombre'];
        $_SESSION['nombre2'] = $row['Segundo_nombre'];
        $_SESSION['apellido'] = $row['Apellido'];
        $_SESSION['email'] = $row['Email'];
        $_SESSION['nacionalidad'] = $row['Nacionalidad'];
        $_SESSION['localidad'] = $row['Localidad'];
        $_SESSION['calle'] = $row['Calle'];
        $_SESSION['altura'] = $row['Altura'];
        $_SESSION['fecha_nacimiento'] = $row['Fecha_Nacimiento'];
        $_SESSION['telefono'] = $row['Telefono'];
        $_SESSION['rol'] = $row['Rol'];
        
        // Redirigir según el rol
        header("Location: home.php");
        exit;
    } else {
        // Contraseña incorrecta
        $_SESSION['error_login'] = "Contraseña incorrecta. Verifica tus datos e intenta nuevamente.";
        header("Location: login.php");
        exit;
    }
} else {
    // Usuario no encontrado
    $_SESSION['error_usuario'] = "No se encontró ninguna cuenta con ese DNI. Si eres nuevo/a, por favor regístrate.";
    header("Location: login.php");
    exit;
}
?>