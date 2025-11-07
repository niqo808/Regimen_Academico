<?php
include('./conexion/conexion.php');

// Solo preceptores pueden acceder
if (!isset($_SESSION['DNI']) || $_SESSION['rol'] != 'Preceptor') {
    header("Location: index.php");
    exit;
}

// Verificar que sea POST y que venga el archivo
if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_FILES['archivo_csv'])) {
    header("Location: importar_alumnos.php");
    exit;
}

$dni_preceptor = $_SESSION['DNI'];
$id_curso = intval($_POST['id_curso']);

// Verificar que el curso pertenezca a este preceptor
$query_verificar_curso = "SELECT COUNT(*) as total FROM cursos 
                          WHERE ID = '$id_curso' 
                          AND DNI_Preceptor = '$dni_preceptor' 
                          AND Estado = 1";
$result_verificar = mysqli_query($CONN, $query_verificar_curso);

if (mysqli_fetch_assoc($result_verificar)['total'] == 0) {
    $_SESSION['error_importacion'] = "No tienes permiso para asignar alumnos a este curso.";
    header("Location: importar_alumnos.php");
    exit;
}

// Verificar que se haya subido el archivo correctamente
if ($_FILES['archivo_csv']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error_importacion'] = "Error al subir el archivo. Intenta nuevamente.";
    header("Location: importar_alumnos.php");
    exit;
}

// Verificar que sea un archivo CSV
$extension = strtolower(pathinfo($_FILES['archivo_csv']['name'], PATHINFO_EXTENSION));
if ($extension !== 'csv') {
    $_SESSION['error_importacion'] = "El archivo debe ser de tipo CSV.";
    header("Location: importar_alumnos.php");
    exit;
}

// Abrir el archivo CSV
$archivo_temporal = $_FILES['archivo_csv']['tmp_name'];
$handle = fopen($archivo_temporal, 'r');

if (!$handle) {
    $_SESSION['error_importacion'] = "No se pudo leer el archivo CSV.";
    header("Location: importar_alumnos.php");
    exit;
}

// Variables para el reporte
$alumnos_insertados = 0;
$alumnos_asignados = 0;
$errores = [];
$fila_numero = 0;

// Ignorar la primera fila (encabezados)
$encabezados = fgetcsv($handle, 1000, ',');

// Procesar cada fila del CSV
while (($datos = fgetcsv($handle, 1000, ',')) !== false) {
    $fila_numero++;
    
    // Verificar que la fila tenga el número correcto de columnas (10 según tu plantilla)
    if (count($datos) < 10) {
        $errores[] = "Fila $fila_numero: Datos incompletos (faltan columnas)";
        continue;
    }
    
    // Extraer datos de la fila (limpiamos espacios en blanco)
    $dni = trim($datos[0]);
    $primer_nombre = trim($datos[1]);
    $segundo_nombre = trim($datos[2]);
    $apellido = trim($datos[3]);
    $email = trim($datos[4]);
    $fecha_nacimiento = trim($datos[5]); // Formato: YYYY-MM-DD
    $nacionalidad = trim($datos[6]);
    $localidad = trim($datos[7]);
    $calle = trim($datos[8]);
    $altura = trim($datos[9]);
    $telefono = isset($datos[10]) ? trim($datos[10]) : '';
    
    // VALIDACIONES BÁSICAS
    
    // 1. DNI debe ser numérico y tener entre 7 y 8 dígitos
    if (!is_numeric($dni) || strlen($dni) < 7 || strlen($dni) > 8) {
        $errores[] = "Fila $fila_numero: DNI inválido ($dni)";
        continue;
    }
    
    // 2. Nombres y apellido son obligatorios
    if (empty($primer_nombre) || empty($apellido)) {
        $errores[] = "Fila $fila_numero: Nombre o apellido vacío (DNI: $dni)";
        continue;
    }
    
    // 3. Email debe tener formato válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Fila $fila_numero: Email inválido ($email)";
        continue;
    }
    
    // 4. Fecha de nacimiento debe ser válida (formato YYYY-MM-DD)
    $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
    if (!$fecha_obj || $fecha_obj->format('Y-m-d') !== $fecha_nacimiento) {
        $errores[] = "Fila $fila_numero: Fecha de nacimiento inválida ($fecha_nacimiento). Usa formato YYYY-MM-DD";
        continue;
    }
    
    // 5. Altura debe ser numérica
    if (!is_numeric($altura)) {
        $errores[] = "Fila $fila_numero: Altura inválida ($altura)";
        continue;
    }
    
    // INSERCIÓN EN LA BASE DE DATOS
    
    // Primero verificamos si el alumno ya existe
    $query_check = "SELECT DNI FROM usuarios WHERE DNI = '$dni'";
    $result_check = mysqli_query($CONN, $query_check);
    
    if (mysqli_num_rows($result_check) > 0) {
        // El alumno ya existe, solo lo asignamos al curso
        
        // Verificar que no esté ya asignado a este curso
        $query_check_curso = "SELECT ID FROM curso_alumno 
                             WHERE DNI_Alumno = '$dni' AND ID_Curso = '$id_curso'";
        $result_check_curso = mysqli_query($CONN, $query_check_curso);
        
        if (mysqli_num_rows($result_check_curso) == 0) {
            // No está en el curso, lo asignamos
            $query_asignar = "INSERT INTO curso_alumno (ID_Curso, DNI_Alumno, Estado) 
                            VALUES ('$id_curso', '$dni', 1)";
            
            if (mysqli_query($CONN, $query_asignar)) {
                $alumnos_asignados++;
            } else {
                $errores[] = "Fila $fila_numero: Error al asignar alumno existente al curso (DNI: $dni)";
            }
        } else {
            $errores[] = "Fila $fila_numero: El alumno ya está asignado a este curso (DNI: $dni)";
        }
        
    } else {
        // El alumno no existe, lo creamos
        
        // Escapar datos para evitar SQL injection
        $dni = mysqli_real_escape_string($CONN, $dni);
        $primer_nombre = mysqli_real_escape_string($CONN, $primer_nombre);
        $segundo_nombre = mysqli_real_escape_string($CONN, $segundo_nombre);
        $apellido = mysqli_real_escape_string($CONN, $apellido);
        $email = mysqli_real_escape_string($CONN, $email);
        $fecha_nacimiento = mysqli_real_escape_string($CONN, $fecha_nacimiento);
        $nacionalidad = mysqli_real_escape_string($CONN, $nacionalidad);
        $localidad = mysqli_real_escape_string($CONN, $localidad);
        $calle = mysqli_real_escape_string($CONN, $calle);
        $altura = mysqli_real_escape_string($CONN, $altura);
        $telefono = mysqli_real_escape_string($CONN, $telefono);
        
        // Insertar en usuarios (el trigger insertará automáticamente en alumnos)
        $query_insert = "INSERT INTO usuarios (
            DNI, Primer_nombre, Segundo_nombre, Apellido, Email, 
            Fecha_Nacimiento, Nacionalidad, Localidad, Calle, Altura, 
            Telefono, Rol, Estado
        ) VALUES (
            '$dni', '$primer_nombre', '$segundo_nombre', '$apellido', '$email', 
            '$fecha_nacimiento', '$nacionalidad', '$localidad', '$calle', '$altura', 
            '$telefono', 'Alumno', 1
        )";
        
        if (mysqli_query($CONN, $query_insert)) {
            $alumnos_insertados++;
            
            // Ahora asignar al curso
            $query_asignar = "INSERT INTO curso_alumno (ID_Curso, DNI_Alumno, Estado) 
                            VALUES ('$id_curso', '$dni', 1)";
            
            if (mysqli_query($CONN, $query_asignar)) {
                $alumnos_asignados++;
            } else {
                $errores[] = "Fila $fila_numero: Alumno creado pero no se pudo asignar al curso (DNI: $dni)";
            }
            
        } else {
            $errores[] = "Fila $fila_numero: Error al crear alumno (DNI: $dni) - " . mysqli_error($CONN);
        }
    }
}

// Cerrar el archivo
fclose($handle);

// Guardar resultados en sesión para mostrarlos
$_SESSION['resultado_importacion'] = [
    'total_filas' => $fila_numero,
    'alumnos_insertados' => $alumnos_insertados,
    'alumnos_asignados' => $alumnos_asignados,
    'errores' => $errores
];

header("Location: resultado_importacion.php");
exit;
?>
