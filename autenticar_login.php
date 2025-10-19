<?php
    include('./conexion/conexion.php');
    
    $gmail = $_POST['gmail'];
    $password_ingresada = $_POST['password'];
    
    // Validar que los campos no estén vacíos
    if (empty($gmail) || empty($password_ingresada)) {
        $_SESSION['error_login'] = "Por favor, complete todos los campos.";
        header("Location: index.php");
        exit;
    }
    
    $QUERY = "SELECT * FROM usuarios WHERE Email = '$gmail'";
    $result = mysqli_query($CONN, $QUERY) or die("Error en la consulta: " . mysqli_error($CONN));
    
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $password_hasheada = $row['Password_Usuario'];
        
        if (password_verify($password_ingresada, $password_hasheada)) {
            // Iniciar sesión
            $_SESSION['DNI'] = $row['DNI'];
            $_SESSION['nombre1'] = $row['Primer_nombre'];
            $_SESSION['nombre2'] = $row['Segundo_nombre'];
            $_SESSION['apellido'] = $row['Apellido'];
            $_SESSION['email'] = $gmail;
            $_SESSION['nacionalidad'] = $row['Nacionalidad'];
            $_SESSION['localidad'] = $row['Localidad'];
            $_SESSION['calle'] = $row['Calle'];
            $_SESSION['altura'] = $row['Altura'];
            $_SESSION['fecha_nacimiento'] = $row['Fecha_nacimiento'];
            $_SESSION['telefono'] = $row['Telefono'];
            $_SESSION['rol'] = $row['Rol'];
            
            header("Location: home.php");
            exit;
        } else {
            $_SESSION['error_login'] = "Credenciales incorrectas. Vuelva a intentar.";
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['error_usuario'] = "Usuario no encontrado.";
        header("Location: index.php");
        exit;
    }
?>
