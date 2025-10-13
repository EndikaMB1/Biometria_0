<?php
// ----------------------------------------------------------
// test_api.php
// Comprobamos si la API guarda y muestra bien las mediciones
// ----------------------------------------------------------

// Dirección de la API (ajustar si hace falta)
$url_post = "https://ematbla.upv.edu.es/api/post_mediciones.php";
$url_get  = "https://ematbla.upv.edu.es/api/get_ultima.php";

// -----------------------------------------------
// Primero: enviamos una medición con POST
// -----------------------------------------------
echo "<h2>Test API - POST</h2>";

$datos = [
  "tipo_medicion" => 11,           // 11 = CO2
  "medicion" => 4321,              // valor cualquiera
  "numero_medicion" => 7,          // número de medición
  "dispositivo_id" => "Test_API"   // nombre de prueba
];

// Configuramos la petición
$opciones = [
  "http" => [
    "header" => "Content-Type: application/json\r\n",
    "method" => "POST",
    "content" => json_encode($datos)
  ]
];

$contexto = stream_context_create($opciones);
$respuesta = file_get_contents($url_post, false, $contexto);

echo "<p><b>Respuesta POST:</b></p>";
echo "<pre>$respuesta</pre>";

// -----------------------------------------------
// Segundo: pedimos la última medición con GET
// -----------------------------------------------
echo "<h2>Test API - GET</h2>";

$respuesta_get = file_get_contents($url_get);

echo "<p><b>Última medición:</b></p>";
echo "<pre>$respuesta_get</pre>";

// ----------------------------------------------------------
// Si todo va bien, deben salir los datos enviados arriba
// ----------------------------------------------------------
?>
