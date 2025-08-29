<?php
    class Carrera{
        private $conn;
        private $table = "carreras";

        public function __construct($db) {
            $this->conn = $db;
        }

        public function getAll() {
            $sql = "SELECT c.id_carrera, c.nombre, c.fecha_creacion, c.fecha_movimiento, 
                           u.nombre AS coordinador_nombre, u.apellido_paterno AS coordinador_apellido_paterno, 
                           u.apellido_materno AS coordinador_apellido_materno, u.id_usuarios AS coordinador_id
                    FROM " . $this->table . " c
                    LEFT JOIN usuarios u ON c.usuarios_id_usuario_coordinador = u.id_usuario";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById($id) {
            $sql = "SELECT * FROM " . $this->table . " WHERE id_carrera = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function create($data) {
            $sql = "INSERT INTO " . $this->table . " (nombre, usuarios_id_usuario_coordinador, fecha_creacion) 
                    VALUES (:nombre, :coordinador_id, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":nombre", $data['nombre']);
            $stmt->bindParam(":coordinador_id", $data['coordinador_id'], PDO::PARAM_INT);
            return $stmt->execute();
        }

        public function update($id, $data) {
            $usuarioActual = $_SESSION["usuario_id"];
            $sql = "UPDATE " . $this->table . " 
                    SET nombre = :nombre, usuarios_id_usuario_coordinador = :coordinador_id, fecha_actualizacion = NOW(), usuarios_id_usuario_movimiento = :actualuser 
                    WHERE id_carrera = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":nombre", $data['nombre']);
            $stmt->bindParam(":coordinador_id", $data['coordinador_id'], PDO::PARAM_INT);
            $stmt->bindParam(":actualuser",$usuarioActual,PDO::PARAM_INT);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        }

        public function delete($id) {
            $sql = "DELETE FROM " . $this->table . " WHERE id_carrera = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();    


    }
}






?>