<?PHP
session_start();
$_SESSION = [];
session_write_close();
session_destroy();
header("Location: index.php"); 
exit;
?>