<?php
class TipoSeguimiento {
    private $conn;
    private $table_name = "tipo_seguimiento";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT id_tipo_seguimiento, nombre FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>