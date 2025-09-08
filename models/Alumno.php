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

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
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

       printf("Error: %s.\n", $stmt->errorInfo()[2]);
       return false;
    }

    public function delete($id) {
        $sql = "DELETE FROM $this->table WHERE id_alumno = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getByMatricula($matricula) {
        $sql = "SELECT * FROM " . $this->table . " WHERE matricula = :matricula LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":matricula", $matricula);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);   
    }

    /**
     * [MEJORADO] Obtiene todos los alumnos de todos los grupos asignados a un tutor.
     * Utiliza un JOIN para ser más eficiente y correcto.
     */
    public function getByTutorId($tutor_id) {
        $sql = "SELECT a.* FROM " . $this->table . " a
                JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                WHERE g.usuarios_id_usuario_tutor = :tutor_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":tutor_id", $tutor_id, PDO::PARAM_INT);
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

    // --- NUEVOS MÉTODOS PARA ELIMINAR CONSULTAS N+1 ---

    /**
     * [NUEVO] Obtiene los alumnos de un arreglo de IDs de grupo.
     * Ideal para la vista de Tutor, para traer todos sus alumnos en una sola consulta.
     * @param array $groupIds Arreglo con los IDs de los grupos.
     * @return array Lista de alumnos.
     */
    public function listByGroupIds(array $groupIds) {
        if (empty($groupIds)) {
            return [];
        }
        // Crea los placeholders (?, ?, ?) para la cláusula IN
        $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
        $sql = "SELECT * FROM " . $this->table . " WHERE grupos_id_grupo IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($groupIds);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listByCarreraIdConTutorYGrupo($carrera_id) {
        $sql = "SELECT 
                    a.nombre, a.apellido_paterno, a.apellido_materno,
                    g.nombre AS nombre_grupo,
                    CONCAT(t.nombre, ' ', t.apellido_paterno, ' ', t.apellido_materno) AS nombre_tutor
                FROM " . $this->table . " a
                LEFT JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
                LEFT JOIN usuarios t ON g.usuarios_id_usuario_tutor = t.id_usuario
                WHERE a.carreras_id_carrera = :carrera_id
                ORDER BY g.nombre, nombre_tutor, a.apellido_paterno";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':carrera_id', $carrera_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>