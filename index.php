<?PHP
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
    <title>EEST N°2 - República Argentina - Sistema Académico</title>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="container">
                <div class="hero-text" data-aos="fade-up">
                    <h1 class="hero-title">
                        Bienvenido al Portal Académico
                        <span class="hero-subtitle">EEST N°2 "República Argentina"</span>
                    </h1>
                    <p class="hero-description">
                        Plataforma integral para la gestión educativa. Accede a tus materias, 
                        calificaciones, asistencias y toda la información académica en un solo lugar.
                    </p>
                    <div class="hero-buttons">
                        <a href="login.php" class="btn-hero btn-hero-primary">
                            <i class="fas fa-sign-in-alt"></i>
                            Iniciar Sesión
                        </a>
                        <a href="registro_usuarios.php" class="btn-hero btn-hero-secondary">
                            <i class="fas fa-user-plus"></i>
                            Registrarse
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-scroll">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Características -->
    <section class="features-section" id="caracteristicas">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Funcionalidades del Sistema</h2>
                <p class="section-description">
                    Herramientas diseñadas para estudiantes, docentes y preceptores
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon icon-primary">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h3 class="feature-title">Gestión de Materias</h3>
                    <p class="feature-description">
                        Accede a todas tus materias, consulta horarios y mantente al día 
                        con el contenido académico.
                    </p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon icon-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Seguimiento de Notas</h3>
                    <p class="feature-description">
                        Consulta tus calificaciones en tiempo real y descarga tu boletín 
                        digital cuando lo necesites.
                    </p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon icon-warning">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="feature-title">Control de Asistencias</h3>
                    <p class="feature-description">
                        Registro digital de asistencias con notificaciones automáticas 
                        para alumnos y tutores.
                    </p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-icon icon-info">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 class="feature-title">Boletín Digital</h3>
                    <p class="feature-description">
                        Genera y descarga tu boletín de calificaciones en formato PDF 
                        de manera instantánea.
                    </p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-icon icon-danger">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Gestión Docente</h3>
                    <p class="feature-description">
                        Herramientas para profesores: carga de notas, toma de asistencia 
                        y comunicación con alumnos.
                    </p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-icon icon-primary">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Seguridad y Privacidad</h3>
                    <p class="feature-description">
                        Sistema seguro con autenticación robusta y protección de datos 
                        personales.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sobre la Institución -->
    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-image" data-aos="fade-right">
                    <img src="./imagenes/FrenteEscuela.jpeg" alt="EEST N°2" class="about-img">
                    <div class="about-badge">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Excelencia Educativa</span>
                    </div>
                </div>
                <div class="about-content" data-aos="fade-left">
                    <h2 class="about-title">EEST N°2 "República Argentina"</h2>
                    <p class="about-text">
                        Somos una institución educativa técnica comprometida con la formación 
                        integral de nuestros estudiantes. Ofrecemos especialidades en Informática, 
                        con un enfoque práctico y moderno.
                    </p>
                    <div class="about-stats">
                        <div class="stat-item">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Estudiantes</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">Docentes</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">20+</div>
                            <div class="stat-label">Años de Trayectoria</div>
                        </div>
                    </div>
                    <div class="about-features">
                        <div class="about-feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Formación técnica de calidad</span>
                        </div>
                        <div class="about-feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Laboratorios equipados</span>
                        </div>
                        <div class="about-feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Orientación profesional</span>
                        </div>
                        <div class="about-feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Convenios con empresas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles del Sistema -->
    <section class="roles-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Accesos por Rol</h2>
                <p class="section-description">
                    Funcionalidades específicas según tu perfil en el sistema
                </p>
            </div>

            <div class="roles-grid">
                <div class="role-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="role-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3 class="role-title">Estudiantes</h3>
                    <ul class="role-features">
                        <li><i class="fas fa-check"></i> Ver materias y horarios</li>
                        <li><i class="fas fa-check"></i> Consultar calificaciones</li>
                        <li><i class="fas fa-check"></i> Revisar inasistencias</li>
                        <li><i class="fas fa-check"></i> Descargar boletín</li>
                    </ul>
                    <a href="registro_usuarios.php" class="role-btn">Registrarse</a>
                </div>

                <div class="role-card" data-aos="zoom-in" data-aos-delay="200">
                    <div class="role-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="role-title">Profesores</h3>
                    <ul class="role-features">
                        <li><i class="fas fa-check"></i> Gestionar materias</li>
                        <li><i class="fas fa-check"></i> Cargar calificaciones</li>
                        <li><i class="fas fa-check"></i> Tomar asistencia</li>
                        <li><i class="fas fa-check"></i> Reportes de rendimiento</li>
                    </ul>
                    <a href="login.php" class="role-btn">Acceder</a>
                </div>

                <div class="role-card" data-aos="zoom-in" data-aos-delay="300">
                    <div class="role-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3 class="role-title">Preceptores</h3>
                    <ul class="role-features">
                        <li><i class="fas fa-check"></i> Aprobar calificaciones</li>
                        <li><i class="fas fa-check"></i> Generar boletines</li>
                        <li><i class="fas fa-check"></i> Importar alumnos</li>
                        <li><i class="fas fa-check"></i> Gestión integral de curso</li>
                    </ul>
                    <a href="login.php" class="role-btn">Acceder</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2 class="cta-title">¿Listo para comenzar?</h2>
                <p class="cta-description">
                    Únete a nuestra comunidad educativa digital y aprovecha todas las herramientas 
                    disponibles para tu crecimiento académico.
                </p>
                <div class="cta-buttons">
                    <a href="registro_usuarios.php" class="btn-cta btn-cta-primary">
                        <i class="fas fa-user-plus"></i>
                        Crear Cuenta
                    </a>
                    <a href="login.php" class="btn-cta btn-cta-outline">
                        <i class="fas fa-sign-in-alt"></i>
                        Ya tengo cuenta
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <div class="footer-logo">
                        <img src="./imagenes/logo.png" alt="Logo EEST N°2" class="footer-logo-img">
                        <span class="footer-logo-text">EEST N°2</span>
                    </div>
                    <p class="footer-description">
                        Escuela de Educación Secundaria Técnica comprometida con 
                        la excelencia educativa y la formación de profesionales.
                    </p>
                </div>

                <div class="footer-column">
                    <h4 class="footer-title">Enlaces Rápidos</h4>
                    <ul class="footer-links">
                        <li><a href="login.php"><i class="fas fa-chevron-right"></i> Iniciar Sesión</a></li>
                        <li><a href="registro_usuarios.php"><i class="fas fa-chevron-right"></i> Registrarse</a></li>
                        <li><a href="#caracteristicas"><i class="fas fa-chevron-right"></i> Características</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4 class="footer-title">Contacto</h4>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>San Miguel, Buenos Aires</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>(011) 1234-5678</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>info@eest2.edu.ar</span>
                        </li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4 class="footer-title">Síguenos</h4>
                    <div class="footer-social">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> EEST N°2 "República Argentina". Todos los derechos reservados.</p>
                <p class="footer-credits">Sistema Académico v1.0</p>
            </div>
        </div>
    </footer>

    <!-- AOS Animation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });

        // Smooth scroll para el botón de scroll
        document.querySelector('.hero-scroll')?.addEventListener('click', function() {
            document.querySelector('.features-section').scrollIntoView({ 
                behavior: 'smooth' 
            });
        });
    </script>
    
</body>
</html>