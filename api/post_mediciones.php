<?php
// ----------------------------------------------------------
// Archivo: post_mediciones.php
// Para recibir una medición desde Android (JSON) y guardarla en la BD
// ----------------------------------------------------------

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Permite peticiones desde Android o navegador
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once "logicaNegocio.php"; // Incluye conexión y clase de lógica

// ----------------------------------------------------------
// 1. Leer el cuerpo de la petición (en formato JSON)
// ----------------------------------------------------------
$input = json_decode(file_get_contents("php://input"), true);

// Si no hay datos, devolvemos un error
if (!$input) {
    echo json_encode([
        "status" => "error",
        "message" => "No se recibieron datos JSON"
    ]);
    exit;
}

// ----------------------------------------------------------
// 2. Extraer los valores del JSON recibido
// ----------------------------------------------------------
$Vgas = $input["Vgas"] ?? null;
$Vtemp = $input["Vtemp"] ?? null;
$dispositivo_id = $input["dispositivo_id"] ?? "desconocido";

// ----------------------------------------------------------
// 3. Validaciones básicas
// ----------------------------------------------------------
if ($Vgas === null && $Vtemp === null) {
    echo json_encode([
        "status" => "error",
        "message" => "Debe enviarse al menos uno de los valores: Vgas o Vtemp"
    ]);
    exit;
}

// ----------------------------------------------------------
// 4. Guardar la medición en la base de datos
// ----------------------------------------------------------
$logica = new LogicaNegocio($pdo);
$resultado = $logica->guardarMedicion($Vgas, $Vtemp, $dispositivo_id);

// ----------------------------------------------------------
// 5. Responder al cliente (Android)
// ----------------------------------------------------------
if (is_numeric($resultado)) {
    echo json_encode([
        "status" => "ok",
        "message" => "Medición guardada correctamente",
        "id" => $resultado
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => $resultado
    ]);
}
?>
