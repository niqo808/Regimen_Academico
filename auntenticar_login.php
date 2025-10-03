<?php
    include('./conexion/conexion.php');
	$CONN=MYSQLI_CONNECT($host,$user,$pw,$db) OR DIE ("ERROR de conexiones");
    $gmail = $_POST['gmail'];
    $password_ingresada = $_POST['password'];
    $QUERY = "SELECT * FROM usuarios WHERE Email = '$gmail'";
    $result = mysqli_query($CONN, $QUERY) or die("error en la consulta ");
    // 5. Verifica si se encontró un usuario con esas credenciales.
    if (mysqli_num_rows($result) == 1) {
    	$row = mysqli_fetch_assoc($result); // Obtiene la fila como un array asociativo
    	$password_hasheada = $row['Password_Usuario'];
    	echo ("Contraseña no hasheada: $password_ingresada <br>");
    	echo ("Contraseña hasheada de la BD: $password_hasheada <br>");
		if (password_verify($password_ingresada, $password_hasheada)) { // Se verifica la contraseña hasheada con la ingresada 
			$_SESSION['DNI'] = $row['DNI'];
			$_SESSION['nombre1'] = $row['Primer_nombre'];
			$_SESSION['nombre2'] = $row['Segundo_nombre'];
			$_SESSION['apellido'] = $row['Apellido'];
			$_SESSION['email'] = $_POST['gmail'];
			$_SESSION['nacionalidad'] = $row['Nacionalidad'];
			$_SESSION['localidad'] = $row['Localidad'];
			$_SESSION['calle'] = $row['Calle'];
			$_SESSION['altura'] = $row['Altura'];
			$_SESSION['fecha_nacimiento'] = $row['Fecha_nacimiento'];
			$_SESSION['telefono'] = $row['Telefono'];
			$_SESSION['rol'] = $row['Rol'];
		    header("Location: home.php");
		    exit; // Siempre llama a exit() después de header() para asegurar la redirección.
		} else {
		    // Si no hay coincidencias, las credenciales son incorrectas.
		    // Puedes redirigir de vuelta al login con un mensaje de error.
    		$_SESSION['error_login'] = "Credenciales incorrectas. Vuelva a intentar...";
		    header("Location: index.php");
		    exit;
		}
	} else {
        // Usuario no encontrado o múltiples resultados (manejar este caso)
        // Por seguridad, dar un mensaje genérico.
		$_SESSION['error_usuario'] = "Usuario no encontrado o múltiples resultados...";
        header("Location: index.php"); // O a una página de error
        exit;
    }	
?>
