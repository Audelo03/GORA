
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . "/../config/db.php";
class Alumno {
    public $conn;
    private $table = "alumnos";

    public $id_alumno;
    public $matricula;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $estatus;
    public $carreras_id_carrera;
    public $grupos_id_grupo;

    public function __construct($db = null) {
        $this->conn = $db;
    }

    public function getAll() {
        $sql = "SELECT a.*, c.nombre AS carrera, g.nombre AS grupo, g.usuarios_id_usuario_tutor AS id_tutor
                FROM alumnos a
                LEFT JOIN carreras c ON a.carreras_id_carrera = c.id_carrera
                LEFT JOIN grupos g ON a.grupos_id_grupo = g.id_grupo";
          return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_alumno = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear alumno
      public function create($data) {
        $sql = "INSERT INTO $this->table 
                (matricula, nombre, apellido_paterno, apellido_materno, estatus, usuarios_id_usuario_movimiento, carreras_id_carrera, grupos_id_grupo)
                VALUES (:matricula, :nombre, :apellido_paterno, :apellido_materno, :estatus, :usuarios_id_usuario_movimiento, :carreras_id_carrera, :grupos_id_grupo)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        echo $data;
        $sql = "UPDATE $this->table SET 
                    matricula=:matricula, 
                    nombre=:nombre, 
                    apellido_paterno=:apellido_paterno, 
                    apellido_materno=:apellido_materno, 
                    estatus=:estatus, 
                    usuarios_id_usuario_movimiento=:usuarios_id_usuario_movimiento, 
                    carreras_id_carrera=:carreras_id_carrera, 
                    grupos_id_grupo=:grupos_id_grupo
                WHERE id_alumno=:id";
        $stmt = $this->conn->prepare($sql);
        $data['id_alumno'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $sql = "DELETE FROM $this->table WHERE id_alumno=:id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
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
