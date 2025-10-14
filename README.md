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
