<?PHP
    include('./conexion/conexion.php');
    include('./public/header.php');
    
    // Mostrar mensajes de error si existen
    if (isset($_SESSION['error_login'])){
        echo "<script> alert('".$_SESSION['error_login']."') ;</script>";
        unset ($_SESSION['error_login']);
    }
    elseif(isset($_SESSION['error_usuario'])){
        echo "<script> alert('".$_SESSION['error_usuario']."') ;</script>";
        unset ($_SESSION['error_usuario']);
    }
    elseif(isset($_SESSION['success_registro'])){
        echo "<script> alert('".$_SESSION['success_registro']."') ;</script>";
        unset ($_SESSION['success_registro']);
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
    <div class="login-container">
        <div class="login-card fade-in">
            <div class="login-header">
                <div class="logo-circle">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h2 class="mb-2">Bienvenido/a</h2>
                <p class="mb-0">Ingresa a tu cuenta escolar</p>
            </div>
            
            <div class="login-body">
                <form id="loginForm" action="autenticar_login.php" method="post">
                    <div class="input-group-custom">
                        <i class="fas fa-id-card icon"></i>
                        <input type="text" 
                               id="dni" 
                               name="dni" 
                               placeholder="DNI (sin puntos)" 
                               required 
                               pattern="[0-9]{7,8}"
                               maxlength="8"
                               title="Ingresa tu DNI de 7 u 8 dígitos sin puntos">
                    </div>
                    
                    <div class="input-group-custom">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="Contraseña" 
                               required>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Iniciar Sesión
                    </button>
                </form>
                
                <div class="divider">
                    <span>¿Primera vez aquí?</span>
                </div>
                
                <a href="registro_usuarios.php" class="btn-register">
                    <i class="fas fa-user-plus me-2"></i>
                    Crear Nueva Cuenta
                </a>
                
                <div class="help-text">
                    ¿Olvidaste tu contraseña? 
                    <a href="#">Recuperar acceso</a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Validar que solo se ingresen números en el DNI
        document.getElementById('dni').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Validación del formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const dni = document.getElementById('dni').value;
            const password = document.getElementById('password').value;
            
            if (!dni || !password) {
                e.preventDefault();
                alert('Por favor completa todos los campos');
                return false;
            }
            
            if (dni.length < 7 || dni.length > 8) {
                e.preventDefault();
                alert('El DNI debe tener 7 u 8 dígitos');
                return false;
            }
        });
    </script>
</body>
</html>