<?php
require_once "logicaNegocio.php";

$logica = new LogicaNegocio($pdo);

//Prueba guardar una medición de ejemplo
$res = $logica->guardarMedicion(11, 1234, 5, "Test_Manual");
echo "<h3>Resultado de guardarMedicion()</h3>";
echo "<pre>";
print_r($res);
echo "</pre>";

//Prueba obtener la última medición
$ultima = $logica->dondeUltimaMedicion();
echo "<h3>Resultado de dondeUltimaMedicion()</h3>";
echo "<pre>";
print_r($ultima);
echo "</pre>";
?>
