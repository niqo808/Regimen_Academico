<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Puede acceder el preceptor o el propio alumno
$puede_acceder = false;
$es_preceptor = false;
$dni_alumno_ver = null;

if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] == 'Preceptor') {
        $puede_acceder = true;
        $es_preceptor = true;
    } elseif ($_SESSION['rol'] == 'Alumno') {
        $puede_acceder = true;
        $dni_alumno_ver = $_SESSION['DNI'];
    }
}

if (!$puede_acceder) {
    header("Location: index.php");
    exit;
}

// Si es preceptor, mostrar lista de cursos para elegir alumno
if ($es_preceptor && !isset($_GET['dni_alumno'])) {
    $dni_preceptor = $_SESSION['DNI'];
    
    // Obtener cursos del preceptor
    $query_cursos = "SELECT DISTINCT cursos.Anio, cursos.Division, cursos.Especialidad, cursos.Turno
                    FROM cursos
                    WHERE cursos.DNI_Preceptor = '$dni_preceptor' AND cursos.Estado = 1
                    ORDER BY cursos.Anio, cursos.Division";
    $result_cursos = mysqli_query($CONN, $query_cursos);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Generar Boletín</title>
    </head>
    <body>
        <div class="container mt-4">
            <h2>Generar Boletín Digital</h2>
            <p class="text-muted">Selecciona un curso para ver los boletines de sus alumnos.</p>
            <br>
        <?php if (mysqli_num_rows($result_cursos) > 0): ?>
            <div class="row">
                <?php while ($curso = mysqli_fetch_assoc($result_cursos)): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <?php echo htmlspecialchars($curso['Anio'] . "° " . $curso['Division']); ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Especialidad:</strong> <?php echo htmlspecialchars($curso['Especialidad']); ?></p>
                                <p><strong>Turno:</strong> <?php echo htmlspecialchars($curso['Turno']); ?></p>
                            </div>
                            <div class="card-footer">
                                <a href="seleccionar_alumno_boletin.php?anio=<?php echo $curso['Anio']; ?>&division=<?php echo urlencode($curso['Division']); ?>&especialidad=<?php echo urlencode($curso['Especialidad']); ?>&turno=<?php echo urlencode($curso['Turno']); ?>" 
                                   class="btn btn-info w-100">
                                    Ver Alumnos
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No tienes cursos asignados.</div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
exit;
}
// Si es preceptor y viene DNI de alumno, o si es alumno, mostrar su boletín
if ($es_preceptor && isset($_GET['dni_alumno'])) {
    $dni_alumno_ver = intval($_GET['dni_alumno']);

// Verificar que el alumno pertenezca a un curso del preceptor
$dni_preceptor = $_SESSION['DNI'];
$query_verificar = "SELECT COUNT(*) as total FROM cursos
                   INNER JOIN curso_alumno ON cursos.ID = curso_alumno.ID_Curso
                   WHERE curso_alumno.DNI_Alumno = '$dni_alumno_ver'
                   AND cursos.DNI_Preceptor = '$dni_preceptor'
                   AND cursos.Estado = 1";
$result_verificar = mysqli_query($CONN, $query_verificar);
if (mysqli_fetch_assoc($result_verificar)['total'] == 0) {
    echo "<script>alert('No tienes permiso para ver este boletín.'); window.location='generar_boletin.php';</script>";
    exit;
}
}
// Obtener información del alumno
$query_alumno = "SELECT usuarios.DNI, usuarios.Primer_nombre, usuarios.Segundo_nombre,
                usuarios.Apellido, usuarios.Fecha_Nacimiento,
                cursos.Anio, cursos.Division, cursos.Especialidad, cursos.Turno
                FROM usuarios
                INNER JOIN alumnos ON usuarios.DNI = alumnos.DNI_Alumno
                INNER JOIN curso_alumno ON alumnos.DNI_Alumno = curso_alumno.DNI_Alumno
                INNER JOIN cursos ON curso_alumno.ID_Curso = cursos.ID
                WHERE usuarios.DNI = '$dni_alumno_ver' AND cursos.Estado = 1
                LIMIT 1";
$result_alumno = mysqli_query($CONN, $query_alumno);

if (mysqli_num_rows($result_alumno) == 0) {
echo "<script>alert('Alumno no encontrado.'); window.location='index.php';</script>";
exit;
}
$alumno = mysqli_fetch_assoc($result_alumno);

// Obtener todas las materias con notas APROBADAS del alumno
$query_notas = "SELECT materias.Nombre as Nombre_Materia,
               notas.primerInforme, notas.primerCuatri,
               notas.segundoInforme, notas.segundoCuatri, notas.notaFinal,
               usuarios.Primer_nombre as Nombre_Profesor, usuarios.Apellido as Apellido_Profesor
               FROM notas
               INNER JOIN materias ON notas.id_materia = materias.ID
               INNER JOIN cursos ON materias.ID_Curso = cursos.ID
               INNER JOIN profesores ON materias.DNI_Profesor = profesores.DNI_Profesor
               INNER JOIN usuarios ON profesores.DNI_Profesor = usuarios.DNI
               WHERE notas.dni_alumno = '$dni_alumno_ver'
               AND cursos.Anio = '" . $alumno['Anio'] . "'
               AND cursos.Division = '" . $alumno['Division'] . "'
               AND cursos.Especialidad = '" . $alumno['Especialidad'] . "'
               AND cursos.Turno = '" . $alumno['Turno'] . "'
               AND notas.Estado_Aprobacion = 'Aprobado'
               ORDER BY materias.Nombre";
$result_notas = mysqli_query($CONN, $query_notas);

// Calcular promedio general
$suma_notas_finales = 0;
$cantidad_notas_finales = 0;
$notas_array = [];
while ($nota = mysqli_fetch_assoc($result_notas)) {
    $notas_array[] = $nota;
    if ($nota['notaFinal'] !== null) {
        $suma_notas_finales += $nota['notaFinal'];
        $cantidad_notas_finales++;
    }
}

$promedio_general = $cantidad_notas_finales > 0 ?
round($suma_notas_finales / $cantidad_notas_finales, 2) : 0;
// Obtener inasistencias
$query_inasistencias = "SELECT
                        SUM(CASE WHEN inasistencias.Tipo = 'Falta' THEN 1 ELSE 0 END) +
                        SUM(CASE WHEN inasistencias.Tipo = 'Tarde' THEN 0.5 ELSE 0 END) as total
                        FROM inasistencias
                        WHERE inasistencias.DNI_Alumno = '$dni_alumno_ver'";
$result_inasistencias = mysqli_query($CONN, $query_inasistencias);
$total_inasistencias_row = mysqli_fetch_assoc($result_inasistencias);
$total_inasistencias = $total_inasistencias_row['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletín Digital - <?php echo htmlspecialchars($alumno['Apellido'] . " " . $alumno['Primer_nombre']); ?></title>
        <style>
            @media print {
                .navbar, .no-print {
                    display: none !important;
                }
            }
        </style>
</head>
<body>
    <!-- Botones de acción (no se imprimen) -->
    <div class="container no-print mt-3">
        <div class="d-flex justify-content-between">
            <a href="<?php echo $es_preceptor ? 'seleccionar_alumno_boletin.php?anio=' . $alumno['Anio'] . '&division=' . urlencode($alumno['Division']) . '&especialidad=' . urlencode($alumno['Especialidad']) . '&turno=' . urlencode($alumno['Turno']) : 'home.php'; ?>" 
               class="btn btn-secondary">
                Volver
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                Imprimir / Guardar PDF
            </button>
        </div>
    </div>
<!-- Boletín -->
<div class="boletin-container">
    <!-- Encabezado -->
    <div class="boletin-header">
        <div class="boletin-logo">
            <img src="./imagenes/logo.png" alt="Logo EEST N°2" class="boletin-logo-img">
        </div>
        <div class="boletin-title">EEST N°2 "República Argentina"</div>
        <div class="boletin-subtitle">Boletín de Calificaciones</div>
        <div class="boletin-subtitle">Ciclo Lectivo <?php echo date('Y'); ?></div>
    </div>

    <!-- Información del Alumno -->
    <div class="info-alumno">
        <h4 style="color: #667eea; margin-bottom: 20px;">Datos del Alumno</h4>
        <div class="info-row">
            <span class="info-label">Apellido y Nombre:</span>
            <span class="info-value">
                <?php echo htmlspecialchars($alumno['Apellido'] . ", " . $alumno['Primer_nombre'] . 
                    (!empty($alumno['Segundo_nombre']) ? " " . $alumno['Segundo_nombre'] : "")); ?>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">DNI:</span>
            <span class="info-value"><?php echo number_format($alumno['DNI'], 0, '', '.'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Nacimiento:</span>
            <span class="info-value"><?php echo date('d/m/Y', strtotime($alumno['Fecha_Nacimiento'])); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Curso:</span>
            <span class="info-value">
                <?php echo htmlspecialchars($alumno['Anio'] . "° Año - División " . $alumno['Division']); ?>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Especialidad:</span>
            <span class="info-value"><?php echo htmlspecialchars($alumno['Especialidad']); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Turno:</span>
            <span class="info-value"><?php echo htmlspecialchars($alumno['Turno']); ?></span>
        </div>
    </div>

    <!-- Tabla de Notas -->
    <div class="notas-section">
        <h4 style="color: #667eea; margin-bottom: 20px;">Calificaciones</h4>
        
        <?php if (count($notas_array) > 0): ?>
            <table class="tabla-boletin">
                <thead>
                    <tr>
                        <th style="text-align: left;">Materia</th>
                        <th>1° Inf.</th>
                        <th>1° Cuatr.</th>
                        <th>2° Inf.</th>
                        <th>2° Cuatr.</th>
                        <th style="background: #5a67d8;">Nota Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notas_array as $nota): ?>
                        <?php 
                        $nota_final_valor = $nota['notaFinal'];
                        $clase_nota = ($nota_final_valor !== null && $nota_final_valor < 6) ? 'nota-desaprobada' : '';
                        ?>
                        <tr>
                            <td class="materia-nombre">
                                <?php echo htmlspecialchars($nota['Nombre_Materia']); ?>
                                <br>
                                <span class="profesor-nombre">
                                    Prof. <?php echo htmlspecialchars($nota['Nombre_Profesor'] . " " . $nota['Apellido_Profesor']); ?>
                                </span>
                            </td>
                            <td><?php echo $nota['primerInforme'] ?? '-'; ?></td>
                            <td><strong><?php echo $nota['primerCuatri'] ?? '-'; ?></strong></td>
                            <td><?php echo $nota['segundoInforme'] ?? '-'; ?></td>
                            <td><strong><?php echo $nota['segundoCuatri'] ?? '-'; ?></strong></td>
                            <td class="nota-final-col <?php echo $clase_nota; ?>">
                                <?php 
                                if ($nota_final_valor !== null) {
                                    echo $nota_final_valor;
                                    if ($nota_final_valor < 6) {
                                        echo " ✗";
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning">
                No hay calificaciones aprobadas disponibles para mostrar en el boletín.
            </div>
        <?php endif; ?>
    </div>

    <!-- Resumen -->
    <div class="resumen-section">
        <h4 style="color: #667eea; margin-bottom: 15px;">Resumen</h4>
        <div class="resumen-grid">
            <div class="resumen-item">
                <div class="resumen-label">Promedio General</div>
                <div class="resumen-valor"><?php echo number_format($promedio_general, 2); ?></div>
            </div>
            <div class="resumen-item">
                <div class="resumen-label">Materias Cursadas</div>
                <div class="resumen-valor"><?php echo count($notas_array); ?></div>
            </div>
            <div class="resumen-item">
                <div class="resumen-label">Inasistencias Totales</div>
                <div class="resumen-valor" style="color: <?php echo $total_inasistencias > 25 ? '#dc3545' : '#667eea'; ?>">
                    <?php echo $total_inasistencias; ?>
                </div>
            </div>
        </div>

        <!-- Estado académico -->
        <div style="text-align: center; margin-top: 30px;">
            <?php
            // Contar materias desaprobadas
            $materias_desaprobadas = 0;
            foreach ($notas_array as $nota) {
                if ($nota['notaFinal'] !== null && $nota['notaFinal'] < 6) {
                    $materias_desaprobadas++;
                }
            }

            if ($materias_desaprobadas == 0 && count($notas_array) > 0):
            ?>
                <span class="estado-badge estado-aprobado">✓ PROMOCIONADO</span>
            <?php elseif ($materias_desaprobadas > 0): ?>
                <span class="estado-badge estado-desaprobado">
                    ⚠ <?php echo $materias_desaprobadas; ?> MATERIA(S) PENDIENTE(S)
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pie del boletín -->
    <div class="footer-boletin">
        <p style="margin: 5px 0;">Documento generado el <?php echo date('d/m/Y H:i'); ?> hs.</p>
        <p style="margin: 5px 0;">Este boletín contiene únicamente las calificaciones aprobadas por el preceptor del curso.</p>
        <p style="margin: 5px 0; font-style: italic;">Sistema de Gestión Académica - EEST N°2 "República Argentina"</p>
    </div>
</div>

</body>
</html>
