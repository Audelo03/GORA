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
                           u.apellido_materno AS coordinador_apellido_materno, u.id_usuario AS coordinador_id
                    FROM " . $this->table . " c
                    LEFT JOIN usuarios u ON c.usuarios_id_usuario_coordinador = u.id_usuario";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getAllPaginated($offset, $limit, $search = '') {
            $sql = "SELECT c.id_carrera, c.nombre, c.fecha_creacion, c.fecha_movimiento, 
                           u.nombre AS coordinador_nombre, u.apellido_paterno AS coordinador_apellido_paterno, 
                           u.apellido_materno AS coordinador_apellido_materno, u.id_usuario AS coordinador_id
                    FROM " . $this->table . " c
                    LEFT JOIN usuarios u ON c.usuarios_id_usuario_coordinador = u.id_usuario
                    WHERE c.estatus = 1";
            
            $params = [];
            if (!empty($search)) {
                $sql .= " AND (LOWER(c.nombre) LIKE LOWER(:search) 
                         OR LOWER(u.nombre) LIKE LOWER(:search) 
                         OR LOWER(u.apellido_paterno) LIKE LOWER(:search) 
                         OR LOWER(u.apellido_materno) LIKE LOWER(:search))";
                $params[':search'] = '%' . $search . '%';
            }
            
            $sql .= " ORDER BY c.id_carrera DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function countAll($search = '') {
            $sql = "SELECT COUNT(*) FROM " . $this->table . " c
                    LEFT JOIN usuarios u ON c.usuarios_id_usuario_coordinador = u.id_usuario
                    WHERE c.estatus = 1";
            
            $params = [];
            if (!empty($search)) {
                $sql .= " AND (LOWER(c.nombre) LIKE LOWER(:search) 
                         OR LOWER(u.nombre) LIKE LOWER(:search) 
                         OR LOWER(u.apellido_paterno) LIKE LOWER(:search) 
                         OR LOWER(u.apellido_materno) LIKE LOWER(:search))";
                $params[':search'] = '%' . $search . '%';
            }
            
            $stmt = $this->conn->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return (int)$stmt->fetchColumn();
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
                    SET nombre = :nombre, usuarios_id_usuario_coordinador = :coordinador_id, fecha_movimiento = NOW(), usuarios_id_usuario_movimiento = :actualuser 
                    WHERE id_carrera = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":nombre", $data['nombre']);
            $stmt->bindParam(":coordinador_id", $data['usuarios_id_usuario_coordinador'], PDO::PARAM_INT);
            $stmt->bindParam(":actualuser",$usuarioActual,PDO::PARAM_INT);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        }

        public function delete($id) {
    try {
        $sql = "UPDATE " . $this->table . " SET estatus = 0 WHERE id_carrera = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            throw new Exception("No se encontró la carrera con el ID proporcionado o ya había sido eliminada.");
        }
    } catch (PDOException $e) {
         if ($e->getCode() == '23000') {
            throw new Exception("Error: No se puede eliminar la carrera porque tiene alumnos u otros registros asociados.");
        }
        throw new Exception("Error en la base de datos al intentar eliminar: " . $e->getMessage());
    }
}
}






?>