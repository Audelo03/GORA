<?php
class Tutor {
    private $conn;
    private $table = "tutores";

    public $id_tutor;
    public $usuarios_id_usuario;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $sql = "SELECT t.*, u.nombre, u.apellido_paterno, u.apellido_materno, u.email 
                FROM " . $this->table . " t
                INNER JOIN usuarios u ON t.usuarios_id_usuario = u.id_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUsuarioId($idUsuario) {
        $sql = "SELECT * FROM " . $this->table . " WHERE usuarios_id_usuario = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function create() {
        $sql = "INSERT INTO " . $this->table . " (usuarios_id_usuario, fecha_creacion) 
                VALUES (:usuario, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario", $this->usuarios_id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }


    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_tutor = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
