<?php
class Modalidad {
    // Conexión a la base de datos y nombre de la tabla
    private $conn;
    private $table_name = "modalidades";

    public $id_modalidad;
    public $nombre;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function readAll() {
        $query = "SELECT id_modalidad, nombre FROM " . $this->table_name . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nombre=:nombre";
        
        $stmt = $this->conn->prepare($query);

        // Limpia los datos para evitar XSS (Cross-Site Scripting)
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));

        $stmt->bindParam(":nombre", $this->nombre);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET nombre = :nombre
                  WHERE id_modalidad = :id_modalidad";
        
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->id_modalidad = htmlspecialchars(strip_tags($this->id_modalidad));

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':id_modalidad', $this->id_modalidad);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_modalidad = :id_modalidad";
        
        $stmt = $this->conn->prepare($query);

        // Limpia el ID
        $this->id_modalidad = htmlspecialchars(strip_tags($this->id_modalidad));

        $stmt->bindParam(':id_modalidad', $this->id_modalidad);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }
}
?>