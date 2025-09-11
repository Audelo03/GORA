<?php
// models/TipoSeguimiento.php

class TipoSeguimiento {
    // Conexión a la base de datos y nombre de la tabla
    public $conn;
    private $table = "tipo_seguimiento";

    // Propiedades del objeto
    public $id_tipo_seguimiento;
    public $nombre;

    // Constructor con la conexión a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtener todos los tipos de seguimiento.
     */
    public function getAll() {
        $sql = "SELECT id_tipo_seguimiento, nombre FROM " . $this->table . " WHERE estatus = 1 ORDER BY nombre";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPaginated($offset, $limit, $search = '') {
        $sql = "SELECT id_tipo_seguimiento, nombre FROM " . $this->table . " WHERE estatus = 1";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " AND LOWER(nombre) LIKE LOWER(:search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        $sql .= " ORDER BY nombre ASC LIMIT :limit OFFSET :offset";
        
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
        $sql = "SELECT COUNT(*) FROM " . $this->table . " WHERE estatus = 1";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " AND LOWER(nombre) LIKE LOWER(:search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener un solo tipo de seguimiento por su ID.
     */
    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_tipo_seguimiento = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear un nuevo tipo de seguimiento.
     * $data es un array asociativo, ej: ['nombre' => 'Mi Nuevo Tipo']
     */
    public function create($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        
        $sql = "INSERT INTO " . $this->table . " (" . $columns . ") VALUES (" . $placeholders . ")";
        $stmt = $this->conn->prepare($sql);

        // Sanitizar y enlazar valores
        foreach ($data as $key => &$value) {
            $stmt->bindValue(":$key", htmlspecialchars(strip_tags($value)));
        }

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        // Imprimir error si algo falla
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    /**
     * Actualizar un tipo de seguimiento existente.
     * $data es un array asociativo, ej: ['nombre' => 'Nombre Actualizado']
     */
    public function update($id, $data) {
        $set_parts = [];
        foreach (array_keys($data) as $key) {
            $set_parts[] = "$key = :$key";
        }
        $set_clause = implode(", ", $set_parts);

        $sql = "UPDATE " . $this->table . " SET " . $set_clause . " WHERE id_tipo_seguimiento = :id_tipo_seguimiento";
        $stmt = $this->conn->prepare($sql);

        // Sanitizar y enlazar valores de los datos
        foreach ($data as $key => &$value) {
            $stmt->bindValue(":$key", htmlspecialchars(strip_tags($value)));
        }

        // Enlazar el ID
        $stmt->bindValue(":id_tipo_seguimiento", htmlspecialchars(strip_tags($id)));

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    /**
     * Eliminar un tipo de seguimiento.
     */
    public function delete($id) {
        $sql = "UPDATE " . $this->table . " SET estatus = 0 WHERE id_tipo_seguimiento = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", htmlspecialchars(strip_tags($id)));

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>