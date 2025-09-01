<?php

class SeguimientoController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }


    public function crear(
        int $id_alumno, 
        int $id_usuario_movimiento, 
        string $descripcion, 
        int $estatus, 
        string $fecha_movimiento, 
        ?string $fecha_compromiso // Este parámetro puede ser null
    ): bool {
        
        $sql = "INSERT INTO seguimientos 
                    (descripcion, estatus, fecha_movimiento, fecha_compromiso, usuarios_id_usuario_movimiento, alumnos_id_alumno, fecha_creacion) 
                VALUES 
                    (:descripcion, :estatus, :fecha_movimiento, :fecha_compromiso, :id_usuario_movimiento, :id_alumno, NOW())";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':estatus', $estatus, PDO::PARAM_INT);
            $stmt->bindParam(':fecha_movimiento', $fecha_movimiento, PDO::PARAM_STR);
            $stmt->bindParam(':id_usuario_movimiento', $id_usuario_movimiento, PDO::PARAM_INT);
            $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);

            // Manejo especial para el campo que puede ser NULL
            if (empty($fecha_compromiso)) {
                $stmt->bindValue(':fecha_compromiso', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':fecha_compromiso', $fecha_compromiso, PDO::PARAM_STR);
            }

            return $stmt->execute();

        } catch (PDOException $e) {

            return false;
        }
    }


}