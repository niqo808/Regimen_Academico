<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Mostrar mensajes si existen
if (isset($_SESSION['error_registro'])){
    echo "<script> alert('".$_SESSION['error_registro']."') ;</script>";
    unset ($_SESSION['error_registro']);
}

// Variables para el formulario de paso 2
$mostrar_paso2 = false;
$usuario_encontrado = null;

// Procesar búsqueda de DNI (Paso 1)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar_dni'])) {
    $dni = trim($_POST['DNI']);
    
    // Validar DNI
    if (!is_numeric($dni) || strlen($dni) < 7 || strlen($dni) > 8) {
        $_SESSION['error_registro'] = "DNI inválido. Debe tener 7 u 8 dígitos.";
    } else {
        // Buscar usuario
        $query = "SELECT DNI, Primer_nombre, Segundo_nombre, Apellido, Email, Password_Usuario 
                  FROM usuarios WHERE DNI = '$dni'";
        $result = mysqli_query($CONN, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $usuario_encontrado = mysqli_fetch_assoc($result);
            
            // Verificar si ya tiene contraseña
            if (!empty($usuario_encontrado['Password_Usuario'])) {
                $_SESSION['error_registro'] = "Este usuario ya está registrado. Por favor, inicia sesión.";
                header("Location: login.php");
                exit;
            }
            
            $mostrar_paso2 = true;
        } else {
            $_SESSION['error_registro'] = "DNI no encontrado. Contacta con la administración de la escuela.";
        }
    }
}

// Procesar registro de contraseña (Paso 2)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar_usuario'])) {
    $dni = $_POST['DNI'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $_SESSION['error_registro'] = "Las contraseñas no coinciden.";
        $mostrar_paso2 = true;
        // Recuperar datos del usuario para mostrar el formulario nuevamente
        $query = "SELECT DNI, Primer_nombre, Segundo_nombre, Apellido, Email FROM usuarios WHERE DNI = '$dni'";
        $result = mysqli_query($CONN, $query);
        $usuario_encontrado = mysqli_fetch_assoc($result);
    } else {
        // Hashear contraseña
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        
        // Actualizar contraseña en la base de datos
        $update_query = "UPDATE usuarios SET Password_Usuario = '$password_hashed' WHERE DNI = '$dni'";
        
        if (mysqli_query($CONN, $update_query)) {
            $_SESSION['success_registro'] = "¡Registro exitoso! Ya puedes iniciar sesión con tu DNI y contraseña.";
            header("Location: login.php");
            exit;
        } else {
            $_SESSION['error_registro'] = "Error al registrar. Intenta nuevamente.";
            $mostrar_paso2 = true;
            $query = "SELECT DNI, Primer_nombre, Segundo_nombre, Apellido, Email FROM usuarios WHERE DNI = '$dni'";
            $result = mysqli_query($CONN, $query);
            $usuario_encontrado = mysqli_fetch_assoc($result);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/styles.css">
    <title>Registro - EEST N°2</title>
    <style>
        .registro-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        .registro-card {
            max-width: 550px;
            width: 100%;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .registro-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        
        .registro-header .icon {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #10b981;
        }
        
        .registro-body {
            padding: 2.5rem 2rem;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .step-indicator::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 25%;
            right: 25%;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            background: #e2e8f0;
            border-radius: 50%;
            margin: 0 auto 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #94a3b8;
            transition: all 0.3s ease;
        }
        
        .step.active .step-circle {
            background: #10b981;
            color: white;
        }
        
        .step.completed .step-circle {
            background: #10b981;
            color: white;
        }
        
        .step-label {
            font-size: 0.875rem;
            color: #94a3b8;
        }
        
        .step.active .step-label {
            color: #10b981;
            font-weight: 600;
        }
        
        .input-group-registro {
            margin-bottom: 1.5rem;
        }
        
        .input-group-registro label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #334155;
        }
        
        .input-group-registro input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .input-group-registro input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .input-group-registro .help-text {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        
        .user-info-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid #10b981;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .user-info-card h4 {
            color: #059669;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .user-info-card h4 i {
            margin-right: 0.5rem;
        }
        
        .user-info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #a7f3d0;
        }
        
        .user-info-item:last-child {
            border-bottom: none;
        }
        
        .user-info-label {
            font-weight: 600;
            color: #065f46;
        }
        
        .user-info-value {
            color: #047857;
        }
        
        .btn-registro {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .btn-registro:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(16, 185, 129, 0.4);
        }
        
        .btn-secundario {
            width: 100%;
            padding: 0.875rem;
            background: white;
            color: #10b981;
            border: 2px solid #10b981;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-secundario:hover {
            background: #10b981;
            color: white;
            transform: translateY(-2px);
        }
        
        .password-requirements {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .password-requirements h5 {
            font-size: 0.875rem;
            color: #475569;
            margin-bottom: 0.5rem;
        }
        
        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .password-requirements li {
            font-size: 0.875rem;
            color: #64748b;
            padding: 0.25rem 0;
        }
        
        .password-requirements li::before {
            content: "✓ ";
            color: #10b981;
            font-weight: bold;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="registro-container">
        <div class="registro-card fade-in">
            <div class="registro-header">
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2 class="mb-2">Crear Cuenta</h2>
                <p class="mb-0">Completa tu registro escolar</p>
            </div>
            
            <div class="registro-body">
                <!-- Indicador de pasos -->
                <div class="step-indicator">
                    <div class="step <?php echo !$mostrar_paso2 ? 'active' : 'completed'; ?>">
                        <div class="step-circle">1</div>
                        <div class="step-label">Buscar DNI</div>
                    </div>
                    <div class="step <?php echo $mostrar_paso2 ? 'active' : ''; ?>">
                        <div class="step-circle">2</div>
                        <div class="step-label">Crear Contraseña</div>
                    </div>
                </div>
                
                <?php if (!$mostrar_paso2): ?>
                    <!-- PASO 1: Buscar DNI -->
                    <form method="POST" action="">
                        <input type="hidden" name="buscar_dni" value="1">
                        
                        <div class="input-group-registro">
                            <label for="DNI">
                                <i class="fas fa-id-card me-2"></i>
                                Número de DNI
                            </label>
                            <input type="text" 
                                   id="DNI" 
                                   name="DNI" 
                                   placeholder="Ingresa tu DNI sin puntos" 
                                   required
                                   pattern="[0-9]{7,8}"
                                   maxlength="8">
                            <div class="help-text">
                                Ingresa tu DNI de 7 u 8 dígitos (sin puntos ni espacios)
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong><i class="fas fa-info-circle me-2"></i>Nota:</strong>
                            Tu DNI debe estar previamente registrado en el sistema por la administración de la escuela.
                        </div>
                        
                        <button type="submit" class="btn-registro">
                            <i class="fas fa-search me-2"></i>
                            Buscar mi DNI
                        </button>
                        
                        <a href="login.php" class="btn-secundario">
                            <i class="fas fa-arrow-left me-2"></i>
                            Ya tengo cuenta
                        </a>
                    </form>
                    
                <?php else: ?>
                    <!-- PASO 2: Crear contraseña -->
                    
                    <!-- Mostrar información del usuario encontrado -->
                    <div class="user-info-card">
                        <h4>
                            <i class="fas fa-check-circle"></i>
                            Usuario Encontrado
                        </h4>
                        <div class="user-info-item">
                            <span class="user-info-label">Nombre:</span>
                            <span class="user-info-value">
                                <?php echo htmlspecialchars($usuario_encontrado['Primer_nombre'] . ' ' . $usuario_encontrado['Segundo_nombre'] . ' ' . $usuario_encontrado['Apellido']); ?>
                            </span>
                        </div>
                        <div class="user-info-item">
                            <span class="user-info-label">DNI:</span>
                            <span class="user-info-value"><?php echo number_format($usuario_encontrado['DNI'], 0, '', '.'); ?></span>
                        </div>
                        <div class="user-info-item">
                            <span class="user-info-label">Email:</span>
                            <span class="user-info-value"><?php echo htmlspecialchars($usuario_encontrado['Email']); ?></span>
                        </div>
                    </div>
                    
                    <form method="POST" action="" id="formPassword">
                        <input type="hidden" name="DNI" value="<?php echo $usuario_encontrado['DNI']; ?>">
                        <input type="hidden" name="registrar_usuario" value="1">
                        
                        <div class="password-requirements">
                            <h5><i class="fas fa-shield-alt me-2"></i>Tu contraseña debe tener:</h5>
                            <ul>
                                <li>Al menos 8 caracteres</li>
                                <li>Una letra mayúscula</li>
                                <li>Una letra minúscula</li>
                                <li>Un número</li>
                            </ul>
                        </div>
                        
                        <div class="input-group-registro">
                            <label for="password">
                                <i class="fas fa-lock me-2"></i>
                                Contraseña
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Crea tu contraseña" 
                                   required
                                   minlength="8">
                        </div>
                        
                        <div class="input-group-registro">
                            <label for="confirm_password">
                                <i class="fas fa-lock me-2"></i>
                                Confirmar Contraseña
                            </label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   placeholder="Repite tu contraseña" 
                                   required
                                   minlength="8">
                            <div class="help-text" id="password-match"></div>
                        </div>
                        
                        <button type="submit" class="btn-registro">
                            <i class="fas fa-check me-2"></i>
                            Completar Registro
                        </button>
                        
                        <form method="POST" action="">
                            <button type="submit" class="btn-secundario">
                                <i class="fas fa-arrow-left me-2"></i>
                                Buscar otro DNI
                            </button>
                        </form>
                    </form>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
    
    <script>
        // Validar que solo se ingresen números en el DNI
        const dniInput = document.getElementById('DNI');
        if (dniInput) {
            dniInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
        
        // Validar que las contraseñas coincidan en tiempo real
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const matchText = document.getElementById('password-match');
        
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                if (this.value === '') {
                    matchText.textContent = '';
                    matchText.style.color = '';
                } else if (this.value === passwordInput.value) {
                    matchText.textContent = '✓ Las contraseñas coinciden';
                    matchText.style.color = '#10b981';
                } else {
                    matchText.textContent = '✗ Las contraseñas no coinciden';
                    matchText.style.color = '#ef4444';
                }
            });
            
            // Validación al enviar el formulario
            document.getElementById('formPassword').addEventListener('submit', function(e) {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.');
                    return false;
                }
                
                if (passwordInput.value.length < 8) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 8 caracteres.');
                    return false;
                }
            });
        }
    </script>
</body>
</html>