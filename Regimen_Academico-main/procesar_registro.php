<?php
include('./conexion/conexion.php');

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['buscar_dni'])) {
        // Buscar DNI
        $dni = $_POST['DNI'];
        $query = "SELECT DNI, Primer_nombre, Apellido FROM usuarios WHERE DNI = '$dni'";
        $result = mysqli_query($CONN, $query) or die("Error en la consulta: " . mysqli_error($CONN));
        
        if (mysqli_num_rows($result) == 1) {
            $usuario = mysqli_fetch_assoc($result);
            $mostrar_formulario = true;
            $usuario_encontrado = $usuario;
        } else {
            $_SESSION['error_registro'] = "DNI no encontrado en la base de datos.";
        }
    } elseif (isset($_POST['registrar_usuario'])) {
        // Registrar usuario
        $dni = $_POST['DNI'];
        $password = $_POST['password'];
        
        // Verificar que el DNI existe
        $check_query = "SELECT * FROM usuarios WHERE DNI = '$dni'";
        $check_result = mysqli_query($CONN, $check_query) or die("Error de consulta: " . mysqli_error($CONN));
        
        if (mysqli_num_rows($check_result) == 1) {
            // Hashear contraseña
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            
            // Actualizar contraseña
            $update_query = "UPDATE usuarios SET Password_Usuario = '$password_hashed' WHERE DNI = '$dni'";
            $result = mysqli_query($CONN, $update_query) or die("Error al actualizar: " . mysqli_error($CONN));
            
            if ($result) {
                $_SESSION['success_registro'] = "Usuario registrado exitosamente. Ya puede iniciar sesión.";
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['error_registro'] = "Error al registrar el usuario.";
            }
        } else {
            $_SESSION['error_registro'] = "Usuario no encontrado en la base de datos.";
        }
    }
}

// Mostrar mensajes
if (isset($_SESSION['error_registro'])) {
    echo "<script> alert('".$_SESSION['error_registro']."') ;</script>";
    unset($_SESSION['error_registro']);
}
if (isset($_SESSION['success_registro'])) {
    echo "<script> alert('".$_SESSION['success_registro']."') ;</script>";
    unset($_SESSION['success_registro']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/styles.css">
    <title>Registro de Usuarios</title>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Registro de Usuarios</h1>
            <br>
            
            <?php if (!isset($mostrar_formulario)): ?>
                <!-- Formulario de búsqueda de DNI -->
                <form method="POST" action="">
                    <input type="hidden" name="buscar_dni" value="1">
                    <div class="form-group">
                        <label for="DNI">DNI del Usuario:</label>
                        <input type="text" id="DNI" name="DNI" required placeholder="Ingrese su DNI">
                    </div>
                    <br>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Buscar Usuario</button>
                    </div>
                </form>
            <?php else: ?>
                <!-- Formulario de registro de contraseña -->
                <div class="alert alert-info">
                    <strong>Usuario encontrado:</strong> <?php echo htmlspecialchars($usuario_encontrado['Primer_nombre'] . " " . $usuario_encontrado['Apellido']); ?>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="DNI" value="<?php echo $usuario_encontrado['DNI']; ?>">
                    
                    <div class="form-group">
                        <label for="password">Nueva Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <br>
                    <div class="text-center">
                        <button type="submit" name="registrar_usuario" class="btn btn-primary">Registrar Usuario</button>
                        <a href="registro_consolidado.php" class="btn btn-secondary">Buscar Otro Usuario</a>
                    </div>
                </form>
            <?php endif; ?>
            
            <div class="text-center mt-3">
                <a href="index.php" class="btn btn-secondary">Volver al Inicio</a>
            </div>
        </div>
    </div>
    
    <script>
        // Validar que las contraseñas coincidan
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
