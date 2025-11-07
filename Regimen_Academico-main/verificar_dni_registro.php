<?php
    include('./conexion/conexion.php');

    
    $CONN=MYSQLI_CONNECT($host,$user,$pw,$db) OR DIE ("ERROR de conexiones");
    $dni = $_POST['DNI'];
    $QUERY = "SELECT DNI, Primer_Nombre FROM usuarios WHERE DNI = '$dni'";
    $result = mysqli_query($CONN, $QUERY) or die("error en la consulta ");
    // 5. Verifica si se encontró un usuario con esas credenciales.
    if (mysqli_num_rows($result) == 1) {
		header("Location: cargar_password.php?DNI=" . urlencode($dni));
		exit; // Siempre llama a exit() después de header() para asegurar la redirección.
	} else {
        // Usuario no encontrado o múltiples resultados (manejar este caso)
        // Por seguridad, dar un mensaje genérico.
        $_SESSION['error_registro'] = "DNI no encontrado...";
        header("Location: registro_usuarios.php"); // O a una página de error
        exit;
    }	
?>
