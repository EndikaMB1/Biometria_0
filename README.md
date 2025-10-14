# Proyecto GTI 3A - Endika Matute

## Descripción del proyecto
Este proyecto implementa un sistema IoT sencillo basado en **beacons BLE**.  
El objetivo es poder **emitir mediciones desde una placa SparkFun (Arduino)** y **recibirlas en una app Android**, que a su vez las envía a una **API REST en PHP** para almacenarlas en una **base de datos MySQL**.

De esta forma se puede comprobar cómo se comunican distintos entornos (C++, Java, PHP y SQL) dentro de un sistema distribuido.

---

## Funcionamiento general

### Emisor (Arduino + SparkFun)
- La placa SparkFun emite beacons BLE usando la librería **Bluefruit**.  
- Cada beacon contiene:
  - Un valor de CO₂ (`major = 11`)  
  - Un valor de temperatura (`major = 12`)  
  - Un identificador de dispositivo (`dispositivo_id`)  
  - Un número de medición (`minor`)

El dispositivo emite alternando las dos mediciones (CO₂ y temperatura) de forma periódica.

### Receptor (Aplicación Android)
- Detecta todos los beacons cercanos o solo el dispositivo configurado (“nuestro beacon”).  
- Si detecta el beacon del proyecto, **envía los datos (major, minor, dispositivo)** al servidor mediante una **petición HTTP POST** con formato **JSON**.  
- También permite detener o iniciar el escaneo con distintos botones.

### Servidor Web (API REST + Base de datos)
- La API REST está hecha en **PHP**, con los siguientes archivos:
  - `post_mediciones.php` → recibe los datos desde Android y los guarda.
  - `get_ultima.php` → devuelve la última medición guardada.
- Las operaciones con la base de datos se gestionan desde `logicaNegocio.php`, que contiene los métodos:
  - `guardarMedicion()`  
  - `dondeUltimaMedicion()`

La base de datos está alojada en **Plesk (UPV)** y gestionada con **phpMyAdmin**.

---

## Estructura del proyecto

GTI3A-Endika-Matute/
│
├── src/ # Código fuente principal
│ ├── arduino/ # Código C++ para la placa SparkFun
│ └── android/ # Proyecto Android (Java)
│
├── api/ # Archivos PHP de la API REST
│ ├── post_mediciones.php
│ ├── get_ultima.php
│ ├── logicaNegocio.php
│ └── config.php
│
├── html/ # Página web para visualizar la última medición
│ └── index.html
│
├── tests/ # Scripts de prueba automáticos
│ ├── test_logica.php
│ └── test_api.php
│
├── doc/ # Documentación, diagramas y diseño
│
└── README.md # Este archivo

Tests automáticos
1️test_logica.php

Comprueba el funcionamiento de la lógica de negocio PHP sin pasar por la API.
Guarda una medición y muestra la última guardada.

2️test_api.php

Simula una petición POST y una GET a la API REST.
Permite verificar que las peticiones funcionan sin usar la app Android.

Tecnologías usadas

Arduino C++

Android Studio (Java)

PHP 8 + MySQL

phpMyAdmin / Plesk (UPV)

HTML + CSS (interfaz web)

BLE (Bluetooth Low Energy)

Cómo probar el sistema completo:

1.Encender la placa SparkFun (emite el beacon prueba21).

2.Abrir la app Android y pulsar “Buscar nuestro dispositivo”.

3.Al detectar el beacon, la app envía la medición al servidor.

4.Entrar en la web:
https://ematbla.upv.edu.es/html/index.html
y comprobar que aparecen los valores actualizados.

