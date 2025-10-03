<?php
include('./conexion/conexion.php');
if (isset($_SESSION['error_registro'])){
    echo "<script> alert('".$_SESSION['error_registro']."') ;</script>";
    unset ($_SESSION['error_registro']);
}
?>
<html>
	<head>
		<title> Cargar registros de usuarios</title>
	</head>
	<header>
		<meta charset="UTF-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <!--<link rel="stylesheet" href="./css/styles.css">
	    <script src="./js/script.js"></script> -->
	    <title>ALTA DE USUARIOS</title>
	</header>
	<body>	
		<?php 
		$query = "SELECT * FROM Usuarios";
		$result = mysqli_query($CONN, $query) or die("error en la consulta ");
		?>
		<br>
		<div align="center"> <h1> Registrarse </h1>
			<br>
			<form method="POST" action="verificar_dni_registro.php">
				<p>  Ingrese su DNI </p>
			    <input type="text" name="DNI" id="DNI" placeholder="Ingrese su dni">
			    <br>
			    <br>
			    <div>
			        <button type="submit">Buscar</button>
			    </div>
			</form>
		</div>
	</body>
</html>