<?php
require_once "logicaNegocio.php";

$logica = new LogicaNegocio($pdo);

// Prueba guardar una medición
$id = $logica->guardarMedicion(1234, -12, "test_manual");
echo "Medición guardada con id: " . $id . "<br>";

// Prueba obtener la última medición
$ultima = $logica->dondeUltimaMedicion();
echo "<pre>";
print_r($ultima);
echo "</pre>";
?>
