<?php
class Alumno {
    private $conn;
    private $table = "alumnos";

    public $id_alumno;
    public $matricula;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $estatus;
    public $carreras_id_carrera;
    public $grupos_id_grupo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $sql = "SELECT a.*, c.nombre AS carrera, g.nombre AS grupo, t.id_usuario AS tutor
                FROM " . $this->table . " a
                LEFT JOIN carreras c ON a.carreras_id_carrera = c.id_carrera
                LEFT JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                LEFT JOIN usuarios t ON g.usuarios_id_usuario_tutor = t.id_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_alumno = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear alumno
    public function create() {
        $sql = "INSERT INTO " . $this->table . " 
                (matricula, nombre, apellido_paterno, apellido_materno, estatus, carreras_id_carrera, grupos_id_grupo, fecha_creacion)
                VALUES (:matricula, :nombre, :apellido_paterno, :apellido_materno, :estatus, :carrera, :grupo, NOW())";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":matricula", $this->matricula);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellido_paterno", $this->apellido_paterno);
        $stmt->bindParam(":apellido_materno", $this->apellido_materno);
        $stmt->bindParam(":estatus", $this->estatus, PDO::PARAM_INT);
        $stmt->bindParam(":carrera", $this->carreras_id_carrera, PDO::PARAM_INT);
        $stmt->bindParam(":grupo", $this->grupos_id_grupo, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Actualizar alumno
    public function update() {
        $sql = "UPDATE " . $this->table . " 
                SET matricula = :matricula, nombre = :nombre, apellido_paterno = :apellido_paterno, apellido_materno = :apellido_materno,
                    estatus = :estatus, carreras_id_carrera = :carrera, grupos_id_grupo = :grupo
                WHERE id_alumno = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":matricula", $this->matricula);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellido_paterno", $this->apellido_paterno);
        $stmt->bindParam(":apellido_materno", $this->apellido_materno);
        $stmt->bindParam(":estatus", $this->estatus, PDO::PARAM_INT);
        $stmt->bindParam(":carrera", $this->carreras_id_carrera, PDO::PARAM_INT);
        $stmt->bindParam(":grupo", $this->grupos_id_grupo, PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->id_alumno, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_alumno = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getByMatricula($matricula) {
        $sql = "SELECT * FROM " . $this->table . " WHERE matricula = :matricula LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":matricula", $matricula);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);  }


    public function getByTutorId($tutor_id) {
        $sltutorid = "select g.id_grupo from grupos g where g.usuarios_id_usuario_tutor = :tutor_id";
        $stmt = $this->conn->prepare($sltutorid);     
        $stmt->bindParam(":tutor_id", $tutor_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $id_grupo = $stmt->fetch(PDO::FETCH_ASSOC)['id_grupo' ] ?? null;

        $sql = "SELECT * FROM " . $this->table . " WHERE grupos_id_grupo = :grupo_id";
        $stmt = $this->conn->prepare($sql);     
        $stmt->bindParam(":grupo_id", $id_grupo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listByGroupId($group_id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE grupos_id_grupo = :group_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":group_id", $group_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listByCarreraId($career_id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE carreras_id_carrera = :career_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":career_id", $career_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
