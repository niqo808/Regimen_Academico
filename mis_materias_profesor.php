<?php
include('./conexion/conexion.php');
include('./public/header.php');

// Verificar que el usuario esté logueado y sea profesor
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Profesor') {
    header("Location: index.php");
    exit;
}

$dni_profesor = $_SESSION['DNI'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/styles.css">
    <title>Mis Materias - Profesor</title>
</head>
<body>
    <div class="container mt-4">
        <h2>Mis Materias</h2>
        <p class="text-muted">Aquí puedes ver todas las materias que dictas y gestionar las notas y asistencias de tus alumnos.</p>
        <br>
        
        <?php
        // Consulta SIMPLE: solo obtener las materias del profesor
        $query_materias = "SELECT materias.ID, materias.Nombre, materias.Horarios, materias.ID_Curso,
                          cursos.Anio, cursos.Division, cursos.Especialidad, cursos.Turno
                          FROM materias
                          INNER JOIN cursos ON materias.ID_Curso = cursos.ID
                          WHERE materias.DNI_Profesor = '$dni_profesor' AND materias.Estado = 1
                          ORDER BY cursos.Anio, cursos.Division, materias.Nombre";
        
        $result_materias = mysqli_query($CONN, $query_materias);
        
        if (mysqli_num_rows($result_materias) > 0) {
            echo "<div class='row'>";
            
            while ($materia = mysqli_fetch_assoc($result_materias)) {
                $id_materia = $materia['ID'];
                $id_curso = $materia['ID_Curso'];
                
                // AHORA SÍ: Contar ALUMNOS en este curso específico (en la tabla curso_alumno)
                $query_total_alumnos = "SELECT COUNT(*) as total
                                       FROM curso_alumno
                                       WHERE curso_alumno.ID_Curso = '$id_curso' 
                                       AND curso_alumno.Estado = 1";
                $result_total = mysqli_query($CONN, $query_total_alumnos);
                $total_alumnos = mysqli_fetch_assoc($result_total)['total'];
                
                // Contar cuántos alumnos TIENEN NOTAS en esta materia específica
                $query_con_notas = "SELECT COUNT(DISTINCT notas.dni_alumno) as con_notas
                                   FROM notas
                                   WHERE notas.id_materia = '$id_materia'";
                $result_con_notas = mysqli_query($CONN, $query_con_notas);
                $alumnos_con_notas = mysqli_fetch_assoc($result_con_notas)['con_notas'];
                
                echo "<div class='col-md-6 col-lg-4 mb-4'>";
                echo "<div class='card h-100 shadow-sm'>";
                echo "<div class='card-header bg-primary text-white'>";
                echo "<h5 class='mb-0'>" . htmlspecialchars($materia['Nombre']) . "</h5>";
                echo "</div>";
                echo "<div class='card-body'>";
                
                echo "<p class='mb-2'><strong>Curso:</strong> " . htmlspecialchars($materia['Anio'] . "° " . $materia['Division']) . "</p>";
                echo "<p class='mb-2'><strong>Especialidad:</strong> " . htmlspecialchars($materia['Especialidad']) . "</p>";
                echo "<p class='mb-2'><strong>Turno:</strong> " . htmlspecialchars($materia['Turno']) . "</p>";
                echo "<p class='mb-2'><strong>Horarios:</strong> " . htmlspecialchars($materia['Horarios']) . "</p>";
                
                echo "<hr>";
                echo "<p class='mb-2'><strong>Alumnos en el curso:</strong> " . $total_alumnos . "</p>";
                
                $porcentaje = $total_alumnos > 0 ? 
                    round(($alumnos_con_notas / $total_alumnos) * 100) : 0;
                echo "<small class='text-muted'>Notas cargadas: " . $alumnos_con_notas . "/" . $total_alumnos . "</small>";
                echo "<div class='progress' style='height: 20px;'>";
                echo "<div class='progress-bar' role='progressbar' style='width: {$porcentaje}%' aria-valuenow='{$porcentaje}' aria-valuemin='0' aria-valuemax='100'>{$porcentaje}%</div>";
                echo "</div>";
                
                echo "</div>";
                echo "<div class='card-footer bg-light'>";
                
                echo "<a href='gestionar_notas.php?id_materia=" . $materia['ID'] . "' class='btn btn-success btn-sm w-100 mb-2'>Gestionar Notas</a>";
                echo "<a href='gestionar_asistencia.php?id_materia=" . $materia['ID'] . "' class='btn btn-warning btn-sm w-100'>Tomar Asistencia</a>";
                
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            
            echo "</div>";
        } else {
            echo "<div class='alert alert-info'>";
            echo "No tienes materias asignadas actualmente. Si crees que esto es un error, contacta con la administración.";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>