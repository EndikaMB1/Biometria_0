<?php
// ----------------------------------------------------------
// Archivo: ping.php
// Para comprobar que PHP y la base de datos funcionan
// ----------------------------------------------------------

header('Content-Type: application/json; charset=utf-8');

require_once "config.php";

try {
    $pdo->query("SELECT 1");

    echo json_encode([
        "status" => "ok",
        "message" => "Conexion correcta con la base de datos biometria_db"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error al conectar: " . $e->getMessage()
    ]);
}
?>
