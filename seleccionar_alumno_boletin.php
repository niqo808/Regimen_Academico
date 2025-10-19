<?php
include('./conexion/conexion.php');
include('./public/header.php');

if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Preceptor') {
    header("Location: index.php");
    exit;
}

$dni_preceptor = $_SESSION['DNI'];
$anio = isset($_GET['anio']) ? $_GET['anio'] : null;
$division = isset($_GET['division']) ? $_GET['division'] : null;
$especialidad = isset($_GET['especialidad']) ? $_GET['especialidad'] : null;
$turno = isset($_GET['turno']) ? $_GET['turno'] : null;

if (!$anio || !$division || !$especialidad || !$turno) {
    header("Location: generar_boletin.php");
    exit;
}

// Verificar permisos
$query_verificar = "SELECT COUNT(*) as total FROM cursos
                    WHERE cursos.DNI_Preceptor = '$dni_preceptor'
                    AND cursos.Anio = '$anio'
                    AND cursos.Division = '$division'
                    AND cursos.Especialidad = '$especialidad'
                    AND cursos.Turno = '$turno'
                    AND cursos.Estado = 1";
$result_verificar = mysqli_query($CONN, $query_verificar);
if (mysqli_fetch_assoc($result_verificar)['total'] == 0) {
    echo "<script>alert('No tienes permiso.'); window.location='generar_boletin.php';</script>";
    exit;
}

// Obtener alumnos del curso
$query_alumnos = "SELECT DISTINCT usuarios.DNI, usuarios.Primer_nombre, usuarios.Apellido
                  FROM cursos
                  INNER JOIN alumnos ON cursos.DNI_Alumno = alumnos.DNI_Alumno
                  INNER JOIN usuarios ON alumnos.DNI_Alumno = usuarios.DNI
                  WHERE cursos.Anio = '$anio'
                  AND cursos.Division = '$division'
                  AND cursos.Especialidad = '$especialidad'
                  AND cursos.Turno = '$turno'
                  AND cursos.Estado = 1
                  ORDER BY usuarios.Apellido, usuarios.Primer_nombre";
$result_alumnos = mysqli_query($CONN, $query_alumnos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Alumno</title>
</head>
<body>
    <div class="container mt-4">
        <h2>Boletines - <?php echo htmlspecialchars($anio . "° " . $division); ?></h2>
        <p class="text-muted"><?php echo htmlspecialchars($especialidad . " - Turno " . $turno); ?></p>
        <hr>

        <?php if (mysqli_num_rows($result_alumnos) > 0): ?>
            <div class="list-group">
                <?php while ($alumno = mysqli_fetch_assoc($result_alumnos)): ?>
                    <a href="generar_boletin.php?dni_alumno=<?php echo $alumno['DNI']; ?>" 
                       class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <h5 class="mb-1">
                                <?php echo htmlspecialchars($alumno['Apellido'] . ", " . $alumno['Primer_nombre']); ?>
                            </h5>
                            <span class="badge bg-primary">Ver Boletín</span>
                        </div>
                        <small class="text-muted">DNI: <?php echo number_format($alumno['DNI'], 0, '', '.'); ?></small>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">No hay alumnos en este curso.</div>
        <?php endif; ?>

        <div class="mt-3">
            <a href="generar_boletin.php" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</body>
</html>