<?php
// ----------------------------------------------------------
// Archivo: get_ultima.php
// Devuelve la última medición registrada en formato JSON
// ----------------------------------------------------------

require_once "config.php";
require_once "logicaNegocio.php";

header('Content-Type: application/json; charset=utf-8');

try {
    $logica = new LogicaNegocio($pdo);
    $resultado = $logica->dondeUltimaMedicion();

    echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error general: " . $e->getMessage()
    ]);
}
?>
