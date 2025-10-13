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
    <title>Mis Materias</title>
</head>
<body>
    <div class="container mt-4">
        <h2>Mis Materias</h2>
        <br>
        
        <?php
        // Obtener el curso del alumno
        $query_curso = "SELECT c.*, c.Anio, c.Division, c.Especialidad, c.Turno 
                        FROM cursos c 
                        WHERE c.DNI_Alumno = '$dni_alumno' AND c.Estado = 1";
        $result_curso = mysqli_query($CONN, $query_curso);
        
        if (mysqli_num_rows($result_curso) > 0) {
            $curso = mysqli_fetch_assoc($result_curso);
            $id_curso = $curso['ID'];
            
            echo "<div class='alert alert-info'>";
            echo "<strong>Curso:</strong> " . $curso['Anio'] . "° - División " . $curso['Division'];
            echo " | <strong>Especialidad:</strong> " . $curso['Especialidad'];
            echo " | <strong>Turno:</strong> " . $curso['Turno'];
            echo "</div>";
            
            // Obtener las materias del curso
            $query_materias = "SELECT m.*, u.Primer_nombre, u.Apellido 
                              FROM materias m
                              INNER JOIN profesores p ON m.DNI_Profesor = p.DNI_Profesor
                              INNER JOIN usuarios u ON p.DNI_Profesor = u.DNI
                              WHERE m.ID_Curso = '$id_curso' AND m.Estado = 1
                              ORDER BY m.Nombre";
            $result_materias = mysqli_query($CONN, $query_materias);
            
            if (mysqli_num_rows($result_materias) > 0) {
                echo "<div class='table-responsive'>";
                echo "<table class='table table-striped table-bordered'>";
                echo "<thead class='table-dark'>";
                echo "<tr>";
                echo "<th>Materia</th>";
                echo "<th>Profesor</th>";
                echo "<th>Horarios</th>";
                echo "<th>Ver Notas</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                
                while ($materia = mysqli_fetch_assoc($result_materias)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($materia['Nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($materia['Primer_nombre'] . " " . $materia['Apellido']) . "</td>";
                    echo "<td>" . htmlspecialchars($materia['Horarios']) . "</td>";
                    echo "<td><a href='ver_notas.php?id_materia=" . $materia['ID'] . "' class='btn btn-sm btn-primary'>Ver Notas</a></td>";
                    echo "</tr>";
                }
                
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-warning'>No hay materias registradas para tu curso.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No estás asignado a ningún curso. Contacta con la administración.</div>";
        }
        ?>
    </div>
</body>
</html>