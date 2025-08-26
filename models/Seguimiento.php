<?php
class Seguimiento {
    private $conn;
    private $table = "seguimientos";

    public $id_seguimiento;
    public $descripcion;
    public $estatus;
    public $fecha_creacion;
    public $fecha_compromiso;
    public $alumnos_alumno_id;
    public $usuarios_id_usuario_movimiento;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByAlumno($idAlumno) {
        $sql = "SELECT * FROM " . $this->table . " WHERE alumnos_alumno_id = :idAlumno ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":idAlumno", $idAlumno, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create() {
        $sql = "INSERT INTO " . $this->table . " 
                (descripcion, estatus, fecha_creacion, fecha_compromiso, usuarios_id_usuario_movimiento, alumnos_alumno_id)
                VALUES (:descripcion, :estatus, NOW(), :fecha_compromiso, :usuario, :alumno)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":estatus", $this->estatus, PDO::PARAM_INT);
        $stmt->bindParam(":fecha_compromiso", $this->fecha_compromiso);
        $stmt->bindParam(":usuario", $this->usuarios_id_usuario_movimiento, PDO::PARAM_INT);
        $stmt->bindParam(":alumno", $this->alumnos_alumno_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_seguimiento = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_seguimiento = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
}
    public function update() {
        $sql = "UPDATE " . $this->table . " 
                SET descripcion = :descripcion, estatus = :estatus, fecha_compromiso = :fecha_compromiso
                WHERE id_seguimiento = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":estatus", $this->estatus, PDO::PARAM_INT);
        $stmt->bindParam(":fecha_compromiso", $this->fecha_compromiso);
        $stmt->bindParam(":id", $this->id_seguimiento, PDO::PARAM_INT);

        return $stmt->execute();
}

}
?>
