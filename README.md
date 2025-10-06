# Proyecto GTI 3A Endika Matute

## Descripción del proyecto
Este proyecto consiste en un sistema de comunicación basado en beacons:

- Arduino + Sparkfun: emite un beacon mediante un código en C++.  
- Aplicación Android (Java - Android Studio): recibe señales beacon, permitiendo:  
  - Detectar un beacon en concreto.  
  - Detectar cualquier beacon disponible.  
  - Detener la búsqueda de beacons.  


## Estructura del proyecto
mi-proyecto/
│
├── src/ # Código fuente (Arduino C++ y Android Java)
│ ├── arduino/ # Código para Arduino + Sparkfun
│ └── android/ # Código para Android (Java)
│
├── doc/ # Documentación, diagramas, ingeniería inversa
│
├── test/ # Pruebas automáticas y manuales
│
└── README.md # Este archivo

---

### Requisitos
- Arduino IDE
- Android Studio
- placa SparkFun