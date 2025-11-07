<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Solo preceptores pueden acceder
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Preceptor') {
    header("Location: index.php");
    exit;
}

$dni_preceptor = $_SESSION['DNI'];

// Obtener los cursos del preceptor
$query_cursos = "SELECT DISTINCT cursos.ID, cursos.Anio, cursos.Division, cursos.Especialidad, cursos.Turno
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
    <title>Importar Alumnos desde CSV</title>
    <link rel="stylesheet" href="./style/styles.css">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">
                            <i class="fas fa-file-upload me-2"></i>
                            Importar Alumnos desde CSV
                        </h2>
                    </div>
                    <div class="card-body">
                        <!-- Instrucciones -->
                        <div class="alert alert-info">
                            <h5 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Instrucciones
                            </h5>
                            <ol class="mb-0">
                                <li>Descarga la plantilla CSV haciendo clic en el botón de abajo</li>
                                <li>Completa el archivo con los datos de los alumnos (un alumno por fila)</li>
                                <li>Selecciona el curso al que pertenecerán TODOS los alumnos del archivo</li>
                                <li>Sube el archivo CSV completado</li>
                            </ol>
                        </div>

                        <!-- Botón para descargar plantilla -->
                        <div class="text-center mb-4">
                            <a href="plantilla_csv.csv" download class="btn btn-success btn-lg">
                                <i class="fas fa-download me-2"></i>
                                Descargar Plantilla CSV
                            </a>
                        </div>

                        <hr>

                        <!-- Formulario de importación -->
                        <form action="procesar_importacion.php" method="POST" enctype="multipart/form-data" id="formImportar">
                            
                            <!-- Selección de Curso -->
                            <div class="form-group mb-4">
                                <label for="id_curso" class="form-label">
                                    <strong>1. Selecciona el curso:</strong>
                                </label>
                                <select name="id_curso" id="id_curso" class="form-control" required>
                                    <option value="">-- Selecciona un curso --</option>
                                    <?php while ($curso = mysqli_fetch_assoc($result_cursos)): ?>
                                        <option value="<?php echo $curso['ID']; ?>">
                                            <?php 
                                            echo htmlspecialchars(
                                                $curso['Anio'] . "° " . 
                                                $curso['Division'] . " - " . 
                                                $curso['Especialidad'] . " - Turno " . 
                                                $curso['Turno']
                                            ); 
                                            ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <small class="form-text text-muted">
                                    Todos los alumnos del CSV se asignarán a este curso
                                </small>
                            </div>

                            <!-- Subir archivo CSV -->
                            <div class="form-group mb-4">
                                <label for="archivo_csv" class="form-label">
                                    <strong>2. Selecciona el archivo CSV:</strong>
                                </label>
                                <input type="file" 
                                       name="archivo_csv" 
                                       id="archivo_csv" 
                                       class="form-control" 
                                       accept=".csv"
                                       required>
                                <small class="form-text text-muted">
                                    Solo archivos CSV (máximo 5 MB)
                                </small>
                            </div>

                            <!-- Vista previa del archivo (opcional, con JavaScript) -->
                            <div id="vista_previa" class="mb-4" style="display: none;">
                                <h5>Vista previa:</h5>
                                <div class="alert alert-secondary">
                                    <strong>Archivo seleccionado:</strong> <span id="nombre_archivo"></span><br>
                                    <strong>Tamaño:</strong> <span id="tamano_archivo"></span>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex justify-content-between">
                                <a href="revisar_notas_preceptor.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-upload me-2"></i>
                                    Importar Alumnos
                                </button>
                            </div>
                        </form>

                        <!-- Advertencias finales -->
                        <div class="alert alert-warning mt-4">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Importante:</strong>
                            <ul class="mb-0">
                                <li>El sistema validará que los DNI no estén duplicados</li>
                                <li>Si un alumno ya existe en el sistema, se le asignará al curso pero NO se modificarán sus datos personales</li>
                                <li>Asegúrate de que el archivo CSV esté en formato UTF-8 para evitar problemas con caracteres especiales</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script para mostrar vista previa del archivo seleccionado
        document.getElementById('archivo_csv').addEventListener('change', function(e) {
            const archivo = e.target.files[0];
            if (archivo) {
                document.getElementById('vista_previa').style.display = 'block';
                document.getElementById('nombre_archivo').textContent = archivo.name;
                
                // Convertir tamaño a formato legible
                const tamanoKB = (archivo.size / 1024).toFixed(2);
                document.getElementById('tamano_archivo').textContent = tamanoKB + ' KB';
                
                // Validar tamaño (5 MB = 5120 KB)
                if (archivo.size > 5 * 1024 * 1024) {
                    alert('El archivo es muy grande. El tamaño máximo es 5 MB.');
                    e.target.value = '';
                    document.getElementById('vista_previa').style.display = 'none';
                }
            }
        });

        // Validar formulario antes de enviar
        document.getElementById('formImportar').addEventListener('submit', function(e) {
            const curso = document.getElementById('id_curso').value;
            const archivo = document.getElementById('archivo_csv').value;
            
            if (!curso || !archivo) {
                e.preventDefault();
                alert('Por favor completa todos los campos requeridos');
                return false;
            }
            
            // Confirmar antes de enviar
            if (!confirm('¿Estás seguro de importar estos alumnos al curso seleccionado?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
