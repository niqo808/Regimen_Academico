<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Verificar que el usuario esté logueado y sea alumno
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Alumno') {
    header("Location: index.php");
    exit;
}

$dni_alumno = $_SESSION['DNI'];
$id_materia = isset($_GET['id_materia']) ? $_GET['id_materia'] : null;

if (!$id_materia) {
    header("Location: mis_materias.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/styles.css">
    <title>Mis Notas</title>
</head>
<body>
    <div class="container mt-4">
        <h2>Mis Notas</h2>
        <br>
        
        <?php
        // Obtener información de la materia
        $query_materia = "SELECT materias.Nombre, usuarios.Primer_nombre, usuarios.Apellido 
                         FROM materias
                         INNER JOIN profesores ON materias.DNI_Profesor = profesores.DNI_Profesor
                         INNER JOIN usuarios ON profesores.DNI_Profesor = usuarios.DNI
                         WHERE materias.ID = '$id_materia'";
        $result_materia = mysqli_query($CONN, $query_materia);
        
        if (mysqli_num_rows($result_materia) > 0) {
            $materia = mysqli_fetch_assoc($result_materia);
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header bg-primary text-white'>";
            echo "<h4>" . htmlspecialchars($materia['Nombre']) . "</h4>";
            echo "<p class='mb-0'>Profesor: " . htmlspecialchars($materia['Primer_nombre'] . " " . $materia['Apellido']) . "</p>";
            echo "</div>";
            echo "</div>";
            
            // Obtener las notas del alumno en esta materia
            $query_notas = "SELECT * FROM notas WHERE dni_alumno = '$dni_alumno' AND id_materia = '$id_materia'";
            $result_notas = mysqli_query($CONN, $query_notas);
            
            if (mysqli_num_rows($result_notas) > 0) {
                $notas = mysqli_fetch_assoc($result_notas);
                
                echo "<div class='table-responsive'>";
                echo "<table class='table table-bordered'>";
                echo "<thead class='table-secondary'>";
                echo "<tr>";
                echo "<th>1er Informe</th>";
                echo "<th>1er Cuatrimestre</th>";
                echo "<th>2do Informe</th>";
                echo "<th>2do Cuatrimestre</th>";
                echo "<th>Nota Final</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr class='text-center'>";
                echo "<td>" . ($notas['primerInforme'] ?? '-') . "</td>";
                echo "<td><strong>" . ($notas['primerCuatri'] ?? '-') . "</strong></td>";
                echo "<td>" . ($notas['segundoInforme'] ?? '-') . "</td>";
                echo "<td><strong>" . ($notas['segundoCuatri'] ?? '-') . "</strong></td>";
                echo "<td class='table-info'><strong>" . ($notas['notaFinal'] ?? '-') . "</strong></td>";
                echo "</tr>";
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-info'>Aún no hay notas cargadas para esta materia.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Materia no encontrada.</div>";
        }
        ?>
        
        <a href="mis_materias.php" class="btn btn-secondary">Volver a Mis Materias</a>
    </div>
</body>
</html>