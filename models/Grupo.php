<?php
class Grupo {
    private $conn;
    private $table_name = "grupos";

    public $id_grupo;
    public $nombre;
    public $estatus;
    public $usuarios_id_usuario_tutor;
    public $carreras_id_carrera;
    public $modalidades_id_modalidad;
    public $usuarios_id_usuario_movimiento;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Leer todos los grupos
    public function read() {
        $query = "SELECT g.id_grupo, g.nombre, g.estatus, u.nombre as tutor, c.nombre as carrera, m.nombre as modalidad FROM " . $this->table_name . " g
                  LEFT JOIN usuarios u ON g.usuarios_id_usuario_tutor = u.id_usuario
                  LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                  LEFT JOIN modalidades m ON g.modalidades_id_modalidad = m.id_modalidad";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Leer un solo grupo por ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_grupo = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_grupo);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->nombre = $row['nombre'];
        $this->estatus = $row['estatus'];
        $this->usuarios_id_usuario_tutor = $row['usuarios_id_usuario_tutor'];
        $this->carreras_id_carrera = $row['carreras_id_carrera'];
        $this->modalidades_id_modalidad = $row['modalidades_id_modalidad'];
    }

    // Crear un nuevo grupo
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nombre=:nombre, estatus=:estatus, usuarios_id_usuario_tutor=:tutor, carreras_id_carrera=:carrera, modalidades_id_modalidad=:modalidad, usuarios_id_usuario_movimiento=:usuario_movimiento";
        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->nombre=htmlspecialchars(strip_tags($this->nombre));
        $this->estatus=htmlspecialchars(strip_tags($this->estatus));
        $this->usuarios_id_usuario_tutor=htmlspecialchars(strip_tags($this->usuarios_id_usuario_tutor));
        $this->carreras_id_carrera=htmlspecialchars(strip_tags($this->carreras_id_carrera));
        $this->modalidades_id_modalidad=htmlspecialchars(strip_tags($this->modalidades_id_modalidad));
        $this->usuarios_id_usuario_movimiento=htmlspecialchars(strip_tags($this->usuarios_id_usuario_movimiento));

        // Enlazar parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":estatus", $this->estatus);
        $stmt->bindParam(":tutor", $this->usuarios_id_usuario_tutor);
        $stmt->bindParam(":carrera", $this->carreras_id_carrera);
        $stmt->bindParam(":modalidad", $this->modalidades_id_modalidad);
        $stmt->bindParam(":usuario_movimiento", $this->usuarios_id_usuario_movimiento);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // Actualizar un grupo
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nombre=:nombre, estatus=:estatus, usuarios_id_usuario_tutor=:tutor, carreras_id_carrera=:carrera, modalidades_id_modalidad=:modalidad, usuarios_id_usuario_movimiento=:usuario_movimiento WHERE id_grupo = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $this->nombre=htmlspecialchars(strip_tags($this->nombre));
        $this->estatus=htmlspecialchars(strip_tags($this->estatus));
        $this->usuarios_id_usuario_tutor=htmlspecialchars(strip_tags($this->usuarios_id_usuario_tutor));
        $this->carreras_id_carrera=htmlspecialchars(strip_tags($this->carreras_id_carrera));
        $this->modalidades_id_modalidad=htmlspecialchars(strip_tags($this->modalidades_id_modalidad));
        $this->usuarios_id_usuario_movimiento=htmlspecialchars(strip_tags($this->usuarios_id_usuario_movimiento));
        $this->id_grupo=htmlspecialchars(strip_tags($this->id_grupo));

        // Enlazar parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":estatus", $this->estatus);
        $stmt->bindParam(":tutor", $this->usuarios_id_usuario_tutor);
        $stmt->bindParam(":carrera", $this->carreras_id_carrera);
        $stmt->bindParam(":modalidad", $this->modalidades_id_modalidad);
        $stmt->bindParam(":usuario_movimiento", $this->usuarios_id_usuario_movimiento);
        $stmt->bindParam(":id", $this->id_grupo);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // Eliminar un grupo
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_grupo = ?";
        $stmt = $this->conn->prepare($query);
        $this->id_grupo=htmlspecialchars(strip_tags($this->id_grupo));
        $stmt->bindParam(1, $this->id_grupo);

        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>