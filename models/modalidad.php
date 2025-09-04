<?php
class Modalidad {
    private $conn;
    private $table_name = "modalidades";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT id_modalidad, nombre FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>