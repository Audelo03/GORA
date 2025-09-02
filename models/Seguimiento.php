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
    public $tipo_seguimiento_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByAlumno($idAlumno) {
        $sql = "SELECT s.*, ts.nombre as tipo_seguimiento_nombre 
                FROM " . $this->table . " s
                LEFT JOIN tipo_seguimiento ts ON s.tipo_seguimiento_id = ts.id_tipo_seguimiento
                WHERE s.alumnos_id_alumno = :idAlumno 
                ORDER BY s.fecha_creacion DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":idAlumno", $idAlumno, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function create() {
        $sql = "INSERT INTO " . $this->table . " 
                (descripcion, estatus, fecha_creacion, fecha_compromiso, usuarios_id_usuario_movimiento, alumnos_id_alumno, tipo_seguimiento_id)
                VALUES (:descripcion, :estatus, NOW(), :fecha_compromiso, :usuario, :alumno, :tipo_seguimiento_id)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":estatus", $this->estatus, PDO::PARAM_INT);
        $stmt->bindParam(":fecha_compromiso", $this->fecha_compromiso);
        $stmt->bindParam(":usuario", $this->usuarios_id_usuario_movimiento, PDO::PARAM_INT);
        $stmt->bindParam(":alumno", $this->alumnos_alumno_id, PDO::PARAM_INT);
        $stmt->bindParam(":tipo_seguimiento_id", $this->tipo_seguimiento_id, PDO::PARAM_INT);

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
                SET descripcion = :descripcion, estatus = :estatus, fecha_compromiso = :fecha_compromiso, tipo_seguimiento_id = :tipo_seguimiento_id
                WHERE id_seguimiento = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":estatus", $this->estatus, PDO::PARAM_INT);
        $stmt->bindParam(":fecha_compromiso", $this->fecha_compromiso);
        $stmt->bindParam(":id", $this->id_seguimiento, PDO::PARAM_INT);
        $stmt->bindParam(":tipo_seguimiento_id", $this->tipo_seguimiento_id, PDO::PARAM_INT);

        return $stmt->execute();
}




}
?>
