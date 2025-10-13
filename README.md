proyecto:
  nombre: "Proyecto GTI 3A - Endika Matute"
  autor: "Endika Matute Blanco"
  grado: "GTI 3A - Universitat Politècnica de València"
  descripcion: |
    Proyecto de comunicación BLE entre una placa SparkFun (Arduino), 
    una aplicación Android y una API REST en PHP.
    El sistema emite mediciones simuladas (CO₂ y temperatura) desde la placa,
    que son recibidas por la app Android y enviadas al servidor mediante JSON.
    Finalmente, los datos se almacenan en una base de datos MySQL y se muestran 
    en una página web.

  estructura:
    - src/arduino: "Código C++ para la placa SparkFun (emisor BLE)"
    - src/android: "Código Java para Android Studio (receptor BLE)"
    - api/: "Lógica del backend PHP y conexión con MySQL"
    - html/: "Visualización web de la última medición"
    - tests/: "Scripts de prueba automáticos (PHP)"
    - doc/: "Documentación, diagramas y esquemas"
    - README.md: "Explicación general del proyecto"

  funcionamiento:
    emisor:
      descripcion: |
        El emisor BLE (SparkFun) usa la librería Bluefruit para mandar beacons 
        con datos de medición. Alterna entre dos tipos:
          - Major = 11 → CO₂
          - Major = 12 → Temperatura
        Los valores medidos (minor) se envían junto con un identificador de dispositivo.
    receptor:
      descripcion: |
        La app Android detecta beacons cercanos. Al encontrar el configurado (por nombre),
        envía su información al servidor con una petición HTTP POST (JSON).
    servidor:
      descripcion: |
        El backend PHP recibe los datos, los guarda en la base de datos y permite 
        consultarlos mediante una API REST. Incluye una página HTML para ver 
        la última medición en tiempo real.

  base_de_datos:
    nombre: "Biometria"
    gestor: "MySQL (phpMyAdmin en Plesk UPV)"
    tabla: "mediciones"
    campos:
      - id: "INT AUTO_INCREMENT PRIMARY KEY"
      - tipo_medicion: "INT (11=CO₂, 12=Temperatura)"
      - medicion: "FLOAT (valor de la medición)"
      - numero_medicion: "INT (contador de la emisión)"
      - fecha: "DATETIME"
      - dispositivo_id: "VARCHAR(50)"
    ejemplo_fila:
      id: 18
      tipo_medicion: 11
      medicion: 235
      numero_medicion: 4
      fecha: "2025-10-13 18:45:12"
      dispositivo_id: "prueba21"

  api_rest:
    endpoints:
      - nombre: "POST /api/post_mediciones.php"
        descripcion: "Guarda una nueva medición"
        ejemplo_entrada: |
          {
            "tipo_medicion": 11,
            "medicion": 235,
            "numero_medicion": 4,
            "dispositivo_id": "prueba21"
          }
        ejemplo_respuesta: |
          {
            "status": "ok",
            "message": "Medición guardada correctamente"
          }
      - nombre: "GET /api/get_ultima.php"
        descripcion: "Devuelve la última medición guardada"
        ejemplo_respuesta: |
          {
            "status": "ok",
            "ultima_medicion": {
              "tipo_medicion": 12,
              "medicion": -12,
              "numero_medicion": 4,
              "fecha": "2025-10-13 18:45:12",
              "dispositivo_id": "prueba21"
            }
          }

  html:
    archivo: "html/index.html"
    descripcion: |
      Página web que muestra la última medición BLE registrada en la base de datos.
      Se actualiza automáticamente cada 3 segundos usando JavaScript.
    ejemplo_codigo: |
      <!DOCTYPE html>
      <html lang="es">
      <head>
        <meta charset="UTF-8">
        <title>Última Medición BLE</title>
        <style>
          body {
            background-color: #111;
            color: #eee;
            font-family: "Segoe UI", Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
          }
          #card {
            background: #222;
            border: 2px solid #00bfff;
            border-radius: 15px;
            padding: 25px 40px;
            box-shadow: 0 0 20px rgba(0,191,255,0.4);
            min-width: 320px;
          }
          h1 { color: #00bfff; }
        </style>
      </head>
      <body>
        <h1>Última Medición BLE</h1>
        <div id="card">
          <p>Dispositivo: <b id="disp">-</b></p>
          <p>Tipo: <b id="tipo">-</b></p>
          <p>Valor: <b id="valor">-</b></p>
          <p>Nº Medición: <b id="num">-</b></p>
          <p>Fecha: <b id="fecha">-</b></p>
        </div>
        <script>
          async function actualizar() {
            try {
              const res = await fetch("get_ultima.php");
              const data = await res.json();
              if (data.status === "ok") {
                const u = data.ultima_medicion;
                document.getElementById("disp").innerText = u.dispositivo_id;
                document.getElementById("tipo").innerText = 
                  u.tipo_medicion == 11 ? "CO₂" : u.tipo_medicion == 12 ? "Temperatura" : "-";
                document.getElementById("valor").innerText = u.medicion;
                document.getElementById("num").innerText = u.numero_medicion;
                document.getElementById("fecha").innerText = u.fecha;
              }
            } catch (e) {
              document.getElementById("disp").innerText = "Error al conectar";
            }
          }
          setInterval(actualizar, 3000);
          actualizar();
        </script>
      </body>
      </html>

  tests:
    descripcion: "Comprobaciones automáticas para la API y la lógica PHP."
    archivos:
      - nombre: "tests/test_logica.php"
        contenido: |
          <?php
          require_once "../logicaNegocio.php";
          $logica = new LogicaNegocio($pdo);

          echo "<h2>Test Lógica PHP</h2>";
          echo "<p>Guardando medición...</p>";
          $res1 = $logica->guardarMedicion(1234, -12, "Test_Manual");
          print_r($res1);

          echo "<hr><p>Última medición:</p>";
          $res2 = $logica->dondeUltimaMedicion();
          print_r($res2);
          ?>
      - nombre: "tests/test_api.php"
        contenido: |
          <?php
          // Test simple para probar la API REST (POST + GET)

          $url_post = "https://ematbla.upv.edu.es/api/post_mediciones.php";
          $url_get  = "https://ematbla.upv.edu.es/api/get_ultima.php";

          echo "<h2>Test API - POST</h2>";
          $datos = [
            "tipo_medicion" => 11,
            "medicion" => 4321,
            "numero_medicion" => 7,
            "dispositivo_id" => "Test_API"
          ];

          $opciones = [
            "http" => [
              "header" => "Content-Type: application/json\r\n",
              "method" => "POST",
              "content" => json_encode($datos)
            ]
          ];

          $contexto = stream_context_create($opciones);
          $respuesta = file_get_contents($url_post, false, $contexto);
          echo "<p><b>Respuesta POST:</b></p><pre>$respuesta</pre>";

          echo "<h2>Test API - GET</h2>";
          $respuesta_get = file_get_contents($url_get);
          echo "<p><b>Última medición:</b></p><pre>$respuesta_get</pre>";
          ?>

  tecnologias:
    - Arduino IDE
    - Android Studio (Java)
    - PHP 8
    - MySQL + phpMyAdmin
    - Plesk (UPV)
    - HTML + CSS + JavaScript
    - BLE (Bluetooth Low Energy)

  ejecucion:
    pasos:
      - "Encender la placa SparkFun (beacon 'Endika')."
      - "Abrir la app Android y pulsar 'Buscar nuestro dispositivo'."
      - "El móvil envía los datos al servidor PHP."
      - "Consultar la web https://ematbla.upv.edu.es/html/index.html para ver los resultados."
      - "Comprobar en phpMyAdmin que la base de datos se actualiza correctamente."
