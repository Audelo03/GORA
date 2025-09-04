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
     public $usuarios_id_usuario_movimiento;


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

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (nombre, apellido_paterno, apellido_materno, email, password, estatus, niveles_usuarios_id_nivel_usuario, usuarios_id_usuario_movimiento) VALUES (:nombre, :apellido_paterno, :apellido_materno, :email, :password, :estatus, :niveles_usuarios_id_nivel_usuario, :usuarios_id_usuario_movimiento)";
        $stmt = $this->conn->prepare($query);

        $nombre = htmlspecialchars(strip_tags($data['nombre']));
        $apellido_paterno = htmlspecialchars(strip_tags($data['apellido_paterno']));
        $apellido_materno = isset($data['apellido_materno']) ? htmlspecialchars(strip_tags($data['apellido_materno'])) : null;
        $email = htmlspecialchars(strip_tags($data['email']));
        $password = $data['password'];
        $estatus = htmlspecialchars(strip_tags($data['estatus']));
        $nivel_usuario = htmlspecialchars(strip_tags($data['niveles_usuarios_id_nivel_usuario']));
        $usuario_movimiento = isset($data['usuarios_id_usuario_movimiento']) ? htmlspecialchars(strip_tags($data['usuarios_id_usuario_movimiento'])) : null;

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":apellido_paterno", $apellido_paterno);
        $stmt->bindParam(":apellido_materno", $apellido_materno);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":estatus", $estatus);
        $stmt->bindParam(":niveles_usuarios_id_nivel_usuario", $nivel_usuario);
        $stmt->bindParam(":usuarios_id_usuario_movimiento", $usuario_movimiento);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAll() {
        $query = "SELECT 
                    u.id_usuario, 
                    u.nombre, 
                    u.apellido_paterno, 
                    u.apellido_materno, 
                    u.email, 
                    u.estatus, 
                    u.niveles_usuarios_id_nivel_usuario,
                    nu.nombre as nivel_usuario 
                  FROM " . $this->table . " u
                  LEFT JOIN niveles_usuarios nu ON u.niveles_usuarios_id_nivel_usuario = nu.id_nivel_usuario
                  ORDER BY u.id_usuario DESC";

        $stmt = $this->conn->prepare($query);
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

    public function fetchCoordinadores($role) {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido_paterno, u.apellido_materno 
                FROM " . $this->table . " u WHERE u.niveles_usuarios_id_nivel_usuario = :role";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":role", $role, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $set_parts = [];
        foreach ($data as $key => $value) {
            $set_parts[] = "$key = :$key";
        }
        $set_clause = implode(", ", $set_parts);

        $query = "UPDATE " . $this->table . " SET " . $set_clause . " WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        foreach ($data as $key => &$value) {
            $clean_value = htmlspecialchars(strip_tags($value));
            $stmt->bindParam(":$key", $clean_value);
        }
        
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(":id_usuario", $id);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    public function getGruposIdByCarreraId($carrera_id){
         $sql = "SELECT id_grupo FROM grupos WHERE carreras_id_carrera = :carrera";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":carrera", $carrera_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


     public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(":id_usuario", $id);

        if ($stmt->execute()) {
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
    //solo para coordinadores
    public function getCarrreraIdByUsuarioId($usuario_id) {
        $sql = "SELECT c.id_carrera AS id_carrera, c.nombre AS nombre
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

    public function getCarrreraDataByUsuarioId($usuario_id) {
        $sql = "SELECT c.id_carrera AS id_carrera, c.nombre AS nombre
                FROM carreras c
                JOIN usuarios u ON c.usuarios_id_usuario_coordinador = u.id_usuario
                WHERE u.id_usuario = :usuario_id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ;
    }
    // solo para tutores
    public function getGruposIdByUsuarioId($usuario_id) {
        $sql = "SELECT g.id_grupo, g.nombre
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

public function getGruposDataByUsuarioId($usuario_id) {
        $sql = "SELECT g.id_grupo, g.nombre
                FROM grupos g
                JOIN usuarios u ON g.usuarios_id_usuario_tutor = u.id_usuario
                WHERE u.id_usuario = :usuario_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
        return $result; 
}
    //ADMIN
    public function obtenerAlumnosParaAdminLv1($alumnoController, $conn) {
    $lista = $alumnoController->listarAlumnos();
    $data = [];
    foreach ($lista as $row) $data[$row['carrera']][$row['id_tutor']][$row['grupo']][] = $row;
    ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 mb-3 text-primary">Todos los Alumnos</h2>
            <?php foreach ($data as $carrera => $tutores): ?>
                <h5 class="mt-4 text-secondary">Carrera: <?= $carrera ?></h5>
                <?php foreach ($tutores as $tutor_id => $grupos): 
                    $tutor = $conn->prepare("SELECT nombre, apellido_paterno, apellido_materno FROM usuarios WHERE id_usuario = :id");
                    $tutor->bindParam(':id', $tutor_id, PDO::PARAM_INT);
                    $tutor->execute();
                    $tutorData = $tutor->fetch(PDO::FETCH_ASSOC);
                    $tutorNombre = $tutorData ? "{$tutorData['nombre']} {$tutorData['apellido_paterno']} {$tutorData['apellido_materno']}" : "Desconocido";
                ?>
                    <h6 class="mt-3">Tutor: <?= $tutorNombre ?></h6>
                    <?php foreach ($grupos as $grupo => $alumnos): ?>
                        <div class="mb-2">
                            <span class="badge bg-info text-dark">Grupo: <?= $grupo ?></span>
                            <ul class="list-group list-group-flush mt-2">
                                <?php foreach ($alumnos as $a): ?>
                                    <li class="list-group-item"><?= $a['nombre'] ?> <?= $a['apellido_paterno'] ?> <?= $a['apellido_materno'] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

    //TUTOR
    public function obtenerAlumnosParaTutorLvl3($alumnoController, $conn, $usuario_id, $auth) {
    //obtener grupos del tutor
    $grupos_ids = $auth->usuario->getGruposIdByUsuarioId($usuario_id);
    if (!$grupos_ids) {
        echo "<div class='alert alert-info'>No tiene grupos asignados.</div>";
        return;
    }
   
    ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 mb-3 text-primary">Mis Alumnos</h2>
            <?php foreach ($grupos_ids as $id_grupo): 
                $stmt = $conn->prepare("SELECT nombre FROM grupos WHERE id_grupo = :id_grupo");
                $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
                $stmt->execute();
                $grupo = $stmt->fetch(PDO::FETCH_ASSOC)['nombre'] ?? "Desconocido";
                $alumnos = $alumnoController->alumno->listByGroupId($id_grupo);
                if (empty($alumnos)) continue;
            ?>
            <div class="mb-2">
                <span class="badge bg-info text-dark">Grupo: <?= $grupo ?></span>
                    <ul class="list-group list-group-flush mt-2">
                    <?php foreach ($alumnos as $a): ?>
                        <li class="list-group-item"><?= $a['nombre'] ?> <?= $a['apellido_paterno'] ?> <?= $a['apellido_materno'] ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

//COORDINADOR
public function obtenerAlumnosParaCoordinadorLvl2($alumnoController, $conn, $auth, $usuario_id) {
    $carreraid = $auth->usuario->getCarrreraIdByUsuarioId($usuario_id);
    $alumnos = $alumnoController->alumno->listByCarreraId($carreraid);
    $nombre_carrera = $conn->prepare("SELECT nombre FROM carreras WHERE id_carrera = :id");
    $nombre_carrera->bindParam(':id', $carreraid, PDO::PARAM_INT);
    $nombre_carrera->execute();
    $nombre_carrera = $nombre_carrera->fetch(PDO::FETCH_ASSOC)['nombre'] ?? "Desconocido";
    
    $data = [];
    foreach ($alumnos as $a) {
        $grupo = $conn->prepare("SELECT nombre, usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = :id");
        $grupo->bindParam(':id', $a['grupos_id_grupo'], PDO::PARAM_INT);
        $grupo->execute();
        $grupoData = $grupo->fetch(PDO::FETCH_ASSOC);
        $grupoNombre = $grupoData ? $grupoData['nombre'] : "Desconocido";
        $grupoTutorId = $grupoData ? $grupoData['usuarios_id_usuario_tutor'] : null;

        $tutor  = $conn->prepare("SELECT nombre, apellido_paterno, apellido_materno FROM usuarios WHERE id_usuario = :id");
        $tutor->bindParam(':id', $grupoTutorId, PDO::PARAM_INT);
        $tutor->execute();
        $tutorData = $tutor->fetch(PDO::FETCH_ASSOC);
        $tutorNombre = $tutorData ? "{$tutorData['nombre']} {$tutorData['apellido_paterno']} {$tutorData['apellido_materno']}" : "Desconocido";
        $data[$grupoNombre][$tutorNombre][] = $a;
    }

    if (empty($data)) {
        echo "<div class='alert alert-info'>No hay alumnos registrados en su carrera.</div>";
        return;
    }
    ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 mb-3 text-primary">Alumnos de la carrera: <?= $nombre_carrera ?></h2>
            <?php foreach ($data as $grupo => $tutores): ?>
               <div class="mb-2">
                <span class="badge bg-info text-dark">Grupo: <?= $grupo ?></span>
                    <ul class="list-group list-group-flush mt-2">
                <?php foreach ($tutores as $tutor => $alumnos): ?>
                    <h6 class="mt-2">Tutor: <?= $tutor ?></h6>
                    <ul class="list-group list-group-flush mb-3">
                        <?php foreach ($alumnos as $a): ?>
                            <li class="list-group-item"><?= $a['nombre'] ?> <?= $a['apellido_paterno'] ?> <?= $a['apellido_materno'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>

    </div>
    </div>
    <?php
}

public function obtenerAlumnosParaDirLvl4($alumnoController, $conn) {
    $this->obtenerAlumnosParaAdminLv1($alumnoController, $conn);
}

public function getGruposIdByCarreraIdFiltered($carrera_id, $terminoBusqueda) {
        if (empty($terminoBusqueda)) {
             $sql = "SELECT id_grupo FROM grupos WHERE carreras_id_carrera = :carrera_id";
             $stmt = $this->conn->prepare($sql);
             $stmt->bindParam(":carrera_id", $carrera_id, PDO::PARAM_INT);
             $stmt->execute();
             return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $sql = "SELECT DISTINCT g.id_grupo FROM grupos g
                LEFT JOIN alumnos a ON a.grupos_id_grupo = g.id_grupo
                WHERE g.carreras_id_carrera = :carrera_id
                AND (
                    LOWER(g.nombre) LIKE LOWER(:termino)
                    OR LOWER(a.nombre) LIKE LOWER(:termino)
                    OR LOWER(a.apellido_paterno) LIKE LOWER(:termino)
                    OR LOWER(a.apellido_materno) LIKE LOWER(:termino)
                    OR LOWER(a.matricula) LIKE LOWER(:termino)
                )";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":carrera_id", $carrera_id, PDO::PARAM_INT);
        $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
?>
