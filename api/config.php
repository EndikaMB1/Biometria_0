<?php
// ----------------------------------------------------------
// Archivo: config.php
// Conexión a la base de datos biometria_db mediante PDO
// ----------------------------------------------------------

// Datos de conexión 
$host = "localhost";
$dbname = "Biometria";
$username = "Endika";
$password = "holaMundo";  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    

} catch (PDOException $e) {

    die(json_encode([
        "status" => "error",
        "message" => "Error al conectar: " . $e->getMessage()
    ]));
}
?>
