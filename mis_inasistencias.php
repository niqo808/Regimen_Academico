<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Verificar que el usuario esté logueado y sea alumno
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Alumno') {
    header("Location: index.php");
    exit;
}

$dni_alumno = $_SESSION['DNI'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/styles.css">
    <title>Mis Inasistencias</title>
</head>
<body>
    <div class="container mt-4">
        <h2>Mis Inasistencias</h2>
        <br>
        
        <?php
        // Contar totales
        $query_totales = "SELECT 
                            SUM(CASE WHEN inasistencias.Tipo = 'Falta' THEN 1 ELSE 0 END) as total_faltas,
                            SUM(CASE WHEN inasistencias.Tipo = 'Tarde' THEN 0.5 ELSE 0 END) as total_tardes,
                            SUM(CASE WHEN inasistencias.Tipo = 'Falta Justificada' THEN 1 ELSE 0 END) as total_justificadas
                          FROM inasistencias 
                          WHERE inasistencias.DNI_Alumno = '$dni_alumno'";
        $result_totales = mysqli_query($CONN, $query_totales);
        $totales = mysqli_fetch_assoc($result_totales);
        
        $total_faltas = $totales['total_faltas'] ?? 0;
        $total_tardes = $totales['total_tardes'] ?? 0;
        $total_justificadas = $totales['total_justificadas'] ?? 0;
        $total_general = $total_faltas + $total_tardes;
        
        echo "<div class='row mb-4'>";
        echo "<div class='col-md-3'>";
        echo "<div class='card text-white bg-danger'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>Faltas</h5>";
        echo "<h2>$total_faltas</h2>";
        echo "</div></div></div>";
        
        echo "<div class='col-md-3'>";
        echo "<div class='card text-white bg-warning'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>Tardes</h5>";
        echo "<h2>$total_tardes</h2>";
        echo "</div></div></div>";
        
        echo "<div class='col-md-3'>";
        echo "<div class='card text-white bg-info'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>Justificadas</h5>";
        echo "<h2>$total_justificadas</h2>";
        echo "</div></div></div>";
        
        echo "<div class='col-md-3'>";
        echo "<div class='card text-white bg-dark'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>Total</h5>";
        echo "<h2>$total_general</h2>";
        echo "</div></div></div>";
        echo "</div>";
        
        // Mostrar detalle de inasistencias
        $query_inasistencias = "SELECT * FROM inasistencias 
                                WHERE inasistencias.DNI_Alumno = '$dni_alumno' 
                                ORDER BY inasistencias.Fecha DESC";
        $result_inasistencias = mysqli_query($CONN, $query_inasistencias);
        
        if (mysqli_num_rows($result_inasistencias) > 0) {
            echo "<h4>Detalle de Inasistencias</h4>";
            echo "<div class='table-responsive'>";
            echo "<table class='table table-striped table-bordered'>";
            echo "<thead class='table-dark'>";
            echo "<tr>";
            echo "<th>Fecha</th>";
            echo "<th>Tipo</th>";
            echo "<th>Observaciones</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            
            while ($inasistencia = mysqli_fetch_assoc($result_inasistencias)) {
                $tipo_class = '';
                switch($inasistencia['Tipo']) {
                    case 'Falta':
                        $tipo_class = 'table-danger';
                        break;
                    case 'Tarde':
                        $tipo_class = 'table-warning';
                        break;
                    case 'Falta Justificada':
                        $tipo_class = 'table-info';
                        break;
                }
                
                echo "<tr class='$tipo_class'>";
                echo "<td>" . date('d/m/Y', strtotime($inasistencia['Fecha'])) . "</td>";
                echo "<td>" . htmlspecialchars($inasistencia['Tipo']) . "</td>";
                echo "<td>" . htmlspecialchars($inasistencia['Observaciones'] ?? '-') . "</td>";
                echo "</tr>";
            }
            
            echo "</tbody>";
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div class='alert alert-success'>¡No tienes inasistencias registradas!</div>";
        }
        ?>
    </div>
</body>
</html>