<?php
include('./conexion/conexion.php');
$dni = $_POST['DNI'];
$Nombre = $_POST['Nombre'];
$Apellido = $_POST ['Apellido'];
// Verificar si el DNI ya existe
$CHECK_QUERY = "SELECT * FROM Usuarios WHERE DNI = '$dni'";

$CHECK_RESULT = MYSQLI_QUERY($CONN, $CHECK_QUERY) or DIE("Error de consulta: " . mysqli_error($CONN));
$password= $_POST['password'];

//HASHEO 
$password_hashed=password_hash($password, PASSWORD_DEFAULT);
// Insertar nuevo alumno
if (mysqli_num_rows($CHECK_RESULT) == 1) {
    // Usuario existe, actualizar contraseña
    $UPDATE_QUERY = "UPDATE Usuarios SET Password_Usuario = '$password_hashed' WHERE DNI = '$dni'";
    $RESULT = mysqli_query($CONN, $UPDATE_QUERY) or die("Error al actualizar: " . mysqli_error($CONN));

    if ($RESULT) {
        // Mostrar mensaje de éxito
        echo "<div align='center'>
                <nav>
                    <br>
                    <p>Usuario $Nombre $Apellido registrado exitosamente</p>
                    <a href='index.php'>Volver</a>
                </nav>
              </div>";
    }
} else {
    // Usuario no existe
}
?>
