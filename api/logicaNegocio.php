<?php
// ----------------------------------------------------------
// Archivo: logicaNegocio.php
// Contiene la clase que maneja las operaciones de la base de datos
// ----------------------------------------------------------

require_once "config.php";  

class LogicaNegocio {

    private $pdo;  // Conexión a la base de datos

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ------------------------------------------------------
    // Método 1: guardarMedicion()
    // Guarda una nueva medición en la tabla Mediciones
    // ------------------------------------------------------
    public function guardarMedicion($tipo_medicion, $medicion, $numero_medicion, $dispositivo_id) {
        try {
            $sql = "INSERT INTO mediciones (tipo_medicion, medicion, numero_medicion, dispositivo_id)
                    VALUES (:tipo_medicion, :medicion, :numero_medicion, :dispositivo_id)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':tipo_medicion' => $tipo_medicion,
                ':medicion' => $medicion,
                ':numero_medicion' => $numero_medicion,
                ':dispositivo_id' => $dispositivo_id
            ]);

            return [
                "status" => "ok",
                "insert_id" => $this->pdo->lastInsertId()
            ];

        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Error al guardar medición: " . $e->getMessage()
            ];
        }
    }

    // ------------------------------------------------------
    // Método 2: dondeUltimaMedicion()
    // Devuelve la última medición registrada en la BD
    // ------------------------------------------------------
    public function dondeUltimaMedicion() {
        try {
            $sql = "SELECT * FROM mediciones ORDER BY id DESC LIMIT 1";
            $stmt = $this->pdo->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                return [
                    "status" => "ok",
                    "ultima_medicion" => $resultado
                ];
            } else {
                return [
                    "status" => "ok",
                    "message" => "No hay mediciones registradas"
                ];
            }

        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Error al consultar la última medición: " . $e->getMessage()
            ];
        }
    }
}
?>
