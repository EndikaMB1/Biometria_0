<?php
// ----------------------------------------------------------
// Archivo: post_mediciones.php
// Recibe datos JSON y guarda una nueva medición BLE
// ----------------------------------------------------------

require_once "config.php";
require_once "logicaNegocio.php";

header('Content-Type: application/json');

// Leer el cuerpo JSON recibido
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "No se recibió JSON válido"]);
    exit;
}

try {
    // Crear instancia de la lógica de negocio
    $logica = new LogicaNegocio($pdo);

    // Guardar los datos en la BD
    $resultado = $logica->guardarMedicion(
        $data['tipo_medicion'],
        $data['medicion'],
        $data['numero_medicion'],
        $data['dispositivo_id']
    );

    echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error general: " . $e->getMessage()
    ]);
}
?>
