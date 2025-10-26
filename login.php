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
    <style>
        .login-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            max-width: 450px;
            width: 100%;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2.5rem 2rem;
            text-align: center;
            color: white;
        }
        
        .login-header .logo-circle {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #667eea;
            font-weight: bold;
        }
        
        .login-body {
            padding: 2.5rem 2rem;
        }
        
        .input-group-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .input-group-custom .icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 1.2rem;
        }
        
        .input-group-custom input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .input-group-custom input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }
        
        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e2e8f0;
        }
        
        .divider span {
            background: white;
            padding: 0 1rem;
            position: relative;
            color: #718096;
            font-size: 0.875rem;
        }
        
        .btn-register {
            width: 100%;
            padding: 0.875rem;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-register:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .help-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #718096;
        }
        
        .help-text a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .help-text a:hover {
            text-decoration: underline;
        }
    </style>
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