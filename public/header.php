<?php
    include('./conexion/conexion.php');
?> 
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Script para evitar flash de contenido al cargar -->
    <script>
    (function(){
        try {
            const stored = localStorage.getItem('regimen-theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            // Si hay preferencia guardada, usarla; si no, respetar la del navegador
            const theme = stored !== null ? stored : (prefersDark ? 'dark' : 'light');
            
            if(theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            } else {
                document.documentElement.classList.remove('dark-mode');
            }
        } catch(e) {
            console.error('Error al cargar tema:', e);
        }
    })();
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="./style/styles.css?v=<?php echo time(); ?>">
    
    <title>Sistema Académico - EEST N°2</title>
</head>
<body>
 
<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" 
            <?php 
                if (!isset($_SESSION['nombre1'])) {
                    echo 'href="./index.php"';
                } else {
                    echo 'href="./home.php"';
                }
            ?>>
            <div class="brand-icon">
                <img src="./imagenes/logo.png" alt="Logo EEST N°2" class="logo-img">
            </div>
            <div class="brand-text">
                <div class="brand-title">EEST N°2</div>
                <div class="brand-subtitle">República Argentina</div>
            </div>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (isset($_SESSION['nombre1'])):  // SESION INICIADA ?>
            <ul class="navbar-nav ms-auto">
                <?php if ($_SESSION['rol'] == 'Alumno'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="alumnoDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-graduate me-2"></i>
                            Mi Campus
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="./mis_materias.php"><i class="fas fa-book me-2"></i>Mis Materias</a></li>
                            <li><a class="dropdown-item" href="./mis_inasistencias.php"><i class="fas fa-calendar-times me-2"></i>Mis Inasistencias</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="./generar_boletin.php"><i class="fas fa-file-alt me-2"></i>Mi Boletín</a></li>
                        </ul>
                    </li>
                <?php elseif ($_SESSION['rol'] == 'Profesor'): ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="./mis_materias_profesor.php">
                            <i class="fas fa-chalkboard-teacher me-2"></i>
                            Mis Materias
                        </a>
                    </li>
                <?php elseif ($_SESSION['rol'] == 'Preceptor'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="preceptorDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Gestión de Curso
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="./revisar_notas_preceptor.php"><i class="fas fa-check-circle me-2"></i>Revisar y Aprobar Notas</a></li>
                            <li><a class="dropdown-item" href="./generar_boletin.php"><i class="fas fa-file-alt me-2"></i>Generar Boletín</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="./importar_alumnos.php"><i class="fas fa-file-import me-2"></i>Importar alumnos</a></li>
                            <li><a class="dropdown-item" href="./revisar_notas_preceptor.php"><i class="fas fa-clipboard-check me-2"></i>Tomar Asistencia por Curso</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                
                <!-- Theme Toggle Button -->
                <li class="nav-item d-flex align-items-center">
                    <button id="themeToggle" class="theme-toggle" aria-label="Alternar tema oscuro" title="Cambiar tema">
                        <span class="theme-toggle__icons" aria-hidden="true">
                            <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" fill="currentColor"/>
                            </svg>
                            <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4V2M12 22v-2M20 12h2M2 12h2M18.36 5.64l1.41-1.41M4.22 19.78l1.41-1.41M18.36 18.36l1.41 1.41M4.22 4.22l1.41 1.41" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3" fill="currentColor"/>
                            </svg>
                        </span>
                    </button>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php echo $_SESSION['nombre1']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="./perfil.php"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="./logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
            <?php else: // SESION NO INICIADA ?>
            <ul class="navbar-nav ms-auto">
                <!-- Theme Toggle Button -->
                <li class="nav-item d-flex align-items-center">
                    <button id="themeToggle" class="theme-toggle" aria-label="Alternar tema oscuro" title="Cambiar tema">
                        <span class="theme-toggle__icons" aria-hidden="true">
                            <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" fill="currentColor"/>
                            </svg>
                            <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4V2M12 22v-2M20 12h2M2 12h2M18.36 5.64l1.41-1.41M4.22 19.78l1.41-1.41M18.36 18.36l1.41 1.41M4.22 4.22l1.41 1.41" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3" fill="currentColor"/>
                            </svg>
                        </span>
                    </button>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="login.php">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Iniciar Sesión
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="registro_usuarios.php">
                        <i class="fas fa-user-plus me-1"></i>
                        Registrarse
                    </a>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para el toggle de tema -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const KEY = 'regimen-theme';
    const toggleBtn = document.getElementById('themeToggle');

    if (!toggleBtn) return;

    // Función para aplicar tema
    function applyTheme(theme) {
        const root = document.documentElement;
        
        if (theme === 'dark') {
            root.classList.add('dark-mode');
            toggleBtn.setAttribute('aria-pressed', 'true');
        } else {
            root.classList.remove('dark-mode');
            toggleBtn.setAttribute('aria-pressed', 'false');
        }
    }

    // Inicializar: verificar estado actual del DOM
    const isDarkModeActive = document.documentElement.classList.contains('dark-mode');
    toggleBtn.setAttribute('aria-pressed', isDarkModeActive ? 'true' : 'false');

    // Manejar clic en el botón
    toggleBtn.addEventListener('click', function() {
        const isDark = document.documentElement.classList.contains('dark-mode');
        const newTheme = isDark ? 'light' : 'dark';
        
        applyTheme(newTheme);
        localStorage.setItem(KEY, newTheme);
    });

    // Escuchar cambios en preferencias del sistema
    if (window.matchMedia) {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        const handleChange = (e) => {
            const stored = localStorage.getItem(KEY);
            // Solo aplicar si el usuario no ha elegido manualmente
            if (stored === null) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        };
        
        if (mediaQuery.addEventListener) {
            mediaQuery.addEventListener('change', handleChange);
        } else if (mediaQuery.addListener) {
            mediaQuery.addListener(handleChange);
        }
    }
});
</script>

</body>
</html>