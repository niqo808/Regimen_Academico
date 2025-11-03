<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.css" />
</head>
<body>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>

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
