<?php
// ----------------------------------------------------------
// Archivo: get_ultima.php
// Para devolver la última medición guardada en la BD
// ----------------------------------------------------------

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); 

require_once "logicaNegocio.php"; 
try {
    // ------------------------------------------------------
    // 1. Crear objeto de la lógica de negocio
    // ------------------------------------------------------
    $logica = new LogicaNegocio($pdo);

    // ------------------------------------------------------
    // 2. Obtener la última medición
    // ------------------------------------------------------
    $ultima = $logica->dondeUltimaMedicion();

    // ------------------------------------------------------
    // 3. Devolver los datos como JSON
    // ------------------------------------------------------
    echo json_encode([
        "status" => "ok",
        "ultima_medicion" => $ultima
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error al obtener la última medición: " . $e->getMessage()
    ]);
}
?>
