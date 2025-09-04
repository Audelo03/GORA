<?php
class NivelUsuario {
    private $conn;
    private $table_name = "niveles_usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT id_nivel_usuario, nombre FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>