<?php
class Usuario {
    private $conn;
    private $table = "usuarios";

    public $id_usuario;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $email;
    public $password;
    public $estatus;
    public $fecha_creacion;
    public $niveles_usuarios_id_nivel_usuario;


    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByEmail($email) {
        $sql = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $sql = "INSERT INTO " . $this->table . " 
                (nombre, apellido_paterno, apellido_materno, email, password, estatus, fecha_creacion, niveles_usuarios_id_nivel_usuario)
                VALUES (:nombre, :apellido_paterno, :apellido_materno, :email, :password, :estatus, NOW(), :nivel)";
        
        $stmt = $this->conn->prepare($sql);

        // Encriptar contraseña antes de guardar
        $hash = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellido_paterno", $this->apellido_paterno);
        $stmt->bindParam(":apellido_materno", $this->apellido_materno);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $hash);
        $stmt->bindParam(":estatus", $this->estatus, PDO::PARAM_INT);
        $stmt->bindParam(":nivel", $this->niveles_usuarios_id_nivel_usuario, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getAll() {
        $sql = "SELECT u.*, n.nombre AS nivel_nombre
                FROM " . $this->table . " u
                LEFT JOIN niveles_usuarios n ON u.niveles_usuarios_id_nivel_usuario = n.id_nivel_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_usuario = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $sql = "UPDATE " . $this->table . " 
                SET nombre = :nombre, apellido_paterno = :apellido_paterno, apellido_materno = :apellido_materno,
                    email = :email, estatus = :estatus, niveles_usuarios_id_nivel_usuario = :nivel, fecha_modificacion = NOW(),
                WHERE id_usuario = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellido_paterno", $this->apellido_paterno);
        $stmt->bindParam(":apellido_materno", $this->apellido_materno);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":estatus", $this->estatus, PDO::PARAM_INT);
        $stmt->bindParam(":nivel", $this->niveles_usuarios_id_nivel_usuario, PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->id_usuario, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    //solo para coordinadores
    public function getCarrreraIdByUsuarioId($usuario_id) {
        $sql = "SELECT c.id_carrera
                FROM carreras c
                JOIN usuarios u ON c.usuarios_id_usuario_coordinador = u.id_usuario
                WHERE u.id_usuario = :usuario_id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id_carrera'] : null;
    }
    // solo para tutores
    public function getGruposIdByUsuarioId($usuario_id) {
        $sql = "SELECT g.id_grupo
                FROM grupos g
                JOIN usuarios u ON g.usuarios_id_usuario_tutor = u.id_usuario
                WHERE u.id_usuario = :usuario_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $grupos = array_map(function($row) { return $row['id_grupo']; }, $result);
        return $grupos; 
}
}
?>
