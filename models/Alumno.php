
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
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $sql = "INSERT INTO " . $this->table . " (" . implode(", ", $columns) . ") 
                VALUES (" . implode(", ", $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            $clean_value = htmlspecialchars(strip_tags((string)$value));
            $stmt->bindValue(":$key", $clean_value);
        }

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
       
    }

    public function update($id, $data) {

         $set_parts = [];

         foreach ($data as $key => $value) {
            $set_parts[] = "$key = :$key";
        }

        $set_clause = implode(", ", $set_parts);

        $query = "UPDATE " . $this->table . " SET " . $set_clause . " WHERE id_alumno = :id_alumno";
        $stmt = $this->conn->prepare($query);

            foreach ($data as $key => $value) {
                $clean_value = htmlspecialchars(strip_tags((string)$value));
                $stmt->bindValue(":$key", $clean_value);
                
            }
            
            $id = htmlspecialchars(strip_tags($id));
            $stmt->bindParam(":id_alumno", $id);
        

            if ($stmt->execute()) {
                return true;
            }

            printf("Error: %s.\n", $stmt->error);
            return false;
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
