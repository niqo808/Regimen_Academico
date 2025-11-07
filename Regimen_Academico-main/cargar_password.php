<?php
    include('./conexion/conexion.php');
    $dni = $_GET["DNI"] ?? null;
    $query = "SELECT * FROM Usuarios WHERE DNI = '$dni'";
	$result = mysqli_query($CONN, $query) or die("error en la consulta ");
	$row = mysqli_fetch_array($result);
	$Nombre = $row['Primer_nombre'];
	$Apellido = $row['Apellido'];
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <link rel="stylesheet" href="./style/styles.css">
	    <title>Carga Password usuario</title>
	</head>
	<body>	
		<br>
		<div align="center"> <h1> Registrarse </h1>
			<br>
			<form method="POST" action="cargar_registro.php">
			    <br>
			    <?php echo "$Nombre, $Apellido"; ?>
			    <br>
			    <input type="hidden" id="DNI" name="DNI" value="<?php echo $dni ?>" >
			    <input type="hidden" id="Nombre" name="Nombre" value="<?php echo $Nombre ?>" >
			    <input type="hidden" id="Apellido" name="Apellido" value="<?php echo $Apellido ?>" >
			    <div>
                	<!--<label for="password">Contraseña</label>-->
                	<input type="password" id="password" name="password" placeholder="Contraseña" >
            	</div>
            	<br>
			   	<div>
			        <button type="submit">Buscar</button>
			    </div>
			</form>
		</div>
	</body>
</html>  

