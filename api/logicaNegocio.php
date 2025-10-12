<?php
// ----------------------------------------------------------
// Archivo: logicaNegocio.php
// Contiene la clase que maneja las operaciones de la base de datos
// ----------------------------------------------------------

require_once "config.php";  

class LogicaNegocio {

    private $pdo;  // Conexión a la base de datos

    // Constructor: recibe la conexión PDO desde config.php
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ------------------------------------------------------
    // Método 1: guardarMedicion()
    // Guarda una nueva medición en la tabla Mediciones
    // ------------------------------------------------------
    public function guardarMedicion($Vgas, $Vtemp, $dispositivo_id) {
        try {
            $sql = "INSERT INTO Mediciones (Vgas, Vtemp, dispositivo_id)
                    VALUES (:Vgas, :Vtemp, :dispositivo_id)";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                ':Vgas' => $Vgas,
                ':Vtemp' => $Vtemp,
                ':dispositivo_id' => $dispositivo_id
            ]);

            // Devuelve el ID de la nueva fila insertada
            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            // En caso de error, devuelve el mensaje
            return "Error al guardar medición: " . $e->getMessage();
        }
    }

    // ------------------------------------------------------
    // Método 2: dondeUltimaMedicion()
    // Devuelve la última medición registrada en la BD
    // ------------------------------------------------------
    public function dondeUltimaMedicion() {
        try {
            $sql = "SELECT * FROM Mediciones ORDER BY id DESC LIMIT 1";
            $stmt = $this->pdo->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                return $resultado;
            } else {
                return ["message" => "No hay mediciones registradas"];
            }

        } catch (PDOException $e) {
            return ["error" => "Error al consultar la última medición: " . $e->getMessage()];
        }
    }
}
?>
