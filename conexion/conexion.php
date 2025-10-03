<?php
// Inicia la sesión (solo si no está iniciada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pw   = "";
$db   = "sistema_escuela";

$CONN = mysqli_connect($host, $user, $pw, $db);

if (!$CONN) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}
?>
