<?php
include('./conexion/conexion.php');

if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Profesor') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: mis_materias_profesor.php");
    exit;
}

$id_materia = intval($_POST['id_materia']);
$fecha = $_POST['fecha'];
$observaciones_generales = !empty($_POST['observaciones_generales']) ? $_POST['observaciones_generales'] : null;

// Verificamos que la fecha no sea futura
$hoy = date('Y-m-d');
if ($fecha > $hoy) {
    echo "<script>alert('No puedes tomar asistencia de una fecha futura.'); history.back();</script>";
    exit;
}

// Contador para saber cuántos registros se guardaron
$registros_guardados = 0;

// Recorremos cada alumno
foreach ($_POST['asistencia'] as $dni_alumno => $estado) {
    // Solo registramos si NO está presente
    if ($estado != 'Presente') {
        $observacion_individual = !empty($_POST['observaciones'][$dni_alumno]) ? 
                                 $_POST['observaciones'][$dni_alumno] : $observaciones_generales;
        
        // Verificamos si ya existe un registro para este alumno en esta fecha
        $check_query = "SELECT inasistencias.ID FROM inasistencias 
                       WHERE inasistencias.DNI_Alumno = '$dni_alumno' AND inasistencias.Fecha = '$fecha'";
        $check_result = mysqli_query($CONN, $check_query);
        
        if (mysqli_num_rows($check_result) == 0) {
            // No existe, creamos el registro
            $insert_query = "INSERT INTO inasistencias (DNI_Alumno, Fecha, Tipo, Observaciones) 
                            VALUES ('$dni_alumno', '$fecha', '$estado', " . 
                            ($observacion_individual ? "'$observacion_individual'" : "NULL") . ")";
            
            if (mysqli_query($CONN, $insert_query)) {
                $registros_guardados++;
            }
        } else {
            // Ya existe, actualizamos
            $update_query = "UPDATE inasistencias 
                            SET Tipo = '$estado', 
                                Observaciones = " . ($observacion_individual ? "'$observacion_individual'" : "NULL") . "
                            WHERE inasistencias.DNI_Alumno = '$dni_alumno' AND inasistencias.Fecha = '$fecha'";
            
            if (mysqli_query($CONN, $update_query)) {
                $registros_guardados++;
            }
        }
    }
}

echo "<script>
        alert('Asistencia guardada correctamente. Registros procesados: $registros_guardados'); 
        window.location='gestionar_asistencias.php?id_materia=$id_materia';
      </script>";
exit;
?>