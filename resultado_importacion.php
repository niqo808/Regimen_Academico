<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Solo preceptores pueden acceder
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Preceptor') {
    header("Location: index.php");
    exit;
}

// Verificar que haya resultados en la sesión
if (!isset($_SESSION['resultado_importacion'])) {
    header("Location: importar_alumnos.php");
    exit;
}

$resultado = $_SESSION['resultado_importacion'];
$total_filas = $resultado['total_filas'];
$alumnos_insertados = $resultado['alumnos_insertados'];
$alumnos_asignados = $resultado['alumnos_asignados'];
$errores = $resultado['errores'];

// Calcular estadísticas
$total_exitosos = $alumnos_asignados;
$total_errores = count($errores);
$tasa_exito = $total_filas > 0 ? round(($total_exitosos / $total_filas) * 100, 2) : 0;

// Limpiar la sesión
unset($_SESSION['resultado_importacion']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Importación</title>
    <link rel="stylesheet" href="./style/styles.css">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow-lg">
                    <div class="card-header <?php echo $total_errores == 0 ? 'bg-success' : 'bg-warning'; ?> text-white">
                        <h2 class="mb-0">
                            <i class="fas <?php echo $total_errores == 0 ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> me-2"></i>
                            Resultado de Importación
                        </h2>
                    </div>
                    <div class="card-body">
                        
                        <!-- Resumen general -->
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h3 class="text-primary"><?php echo $total_filas; ?></h3>
                                        <p class="mb-0"><strong>Filas Procesadas</strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h3 class="text-success"><?php echo $alumnos_insertados; ?></h3>
                                        <p class="mb-0"><strong>Nuevos Alumnos</strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h3 class="text-info"><?php echo $alumnos_asignados; ?></h3>
                                        <p class="mb-0"><strong>Asignados al Curso</strong></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h3 class="text-danger"><?php echo $total_errores; ?></h3>
                                        <p class="mb-0"><strong>Errores</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de progreso -->
                        <div class="mb-4">
                            <h5>Tasa de Éxito: <?php echo $tasa_exito; ?>%</h5>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" 
                                     style="width: <?php echo $tasa_exito; ?>%"
                                     aria-valuenow="<?php echo $tasa_exito; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <?php echo $tasa_exito; ?>%
                                </div>
                            </div>
                        </div>

                        <?php if ($total_errores == 0): ?>
                            <!-- Mensaje de éxito total -->
                            <div class="alert alert-success">
                                <h4 class="alert-heading">
                                    <i class="fas fa-check-circle me-2"></i>
                                    ¡Importación Exitosa!
                                </h4>
                                <p class="mb-0">
                                    Todos los alumnos del archivo CSV fueron procesados correctamente.
                                    <?php if ($alumnos_insertados > 0): ?>
                                        Se crearon <strong><?php echo $alumnos_insertados; ?></strong> nuevos alumnos en el sistema.
                                    <?php endif; ?>
                                    Se asignaron <strong><?php echo $alumnos_asignados; ?></strong> alumnos al curso seleccionado.
                                </p>
                            </div>
                        <?php else: ?>
                            <!-- Mensaje de éxito parcial con errores -->
                            <div class="alert alert-warning">
                                <h4 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Importación Completada con Advertencias
                                </h4>
                                <p>
                                    La importación se completó, pero se encontraron <strong><?php echo $total_errores; ?></strong> errores.
                                    Los alumnos con errores NO fueron agregados al sistema.
                                </p>
                                <p class="mb-0">
                                    Revisa los errores a continuación y corrige el archivo CSV si deseas importar los registros faltantes.
                                </p>
                            </div>

                            <!-- Tabla de errores -->
                            <div class="card mb-4">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-times-circle me-2"></i>
                                        Detalle de Errores (<?php echo $total_errores; ?>)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark sticky-top">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Descripción del Error</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($errores as $index => $error): ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td><?php echo htmlspecialchars($error); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="revisar_notas_preceptor.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-home me-2"></i>
                                Volver al Inicio
                            </a>
                            <a href="importar_alumnos.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-file-upload me-2"></i>
                                Importar Más Alumnos
                            </a>
                        </div>

                    </div>
                </div>

                <!-- Consejos para corregir errores -->
                <?php if ($total_errores > 0): ?>
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            Consejos para Corregir Errores
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li><strong>DNI inválido:</strong> Verifica que el DNI tenga 7 u 8 dígitos y sea solo numérico</li>
                            <li><strong>Email inválido:</strong> Asegúrate de que el email tenga formato válido (ejemplo@dominio.com)</li>
                            <li><strong>Fecha inválida:</strong> La fecha de nacimiento debe estar en formato YYYY-MM-DD (ejemplo: 2007-03-15)</li>
                            <li><strong>Datos incompletos:</strong> Verifica que todas las columnas estén presentes y con datos</li>
                            <li><strong>Alumno duplicado:</strong> Si el alumno ya existe y está en el curso, no hace falta importarlo nuevamente</li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>
</html>
