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
        $password = $data["password"];
        $estatus = htmlspecialchars(strip_tags($data['estatus']));
        $nivel_usuario = htmlspecialchars(strip_tags($data['niveles_usuarios_id_nivel_usuario']));
        $usuario_movimiento = isset($data['usuarios_id_usuario_movimiento']) ? htmlspecialchars(strip_tags($data['usuarios_id_usuario_movimiento'])) : null;

        $stmt->bindValue(":nombre", $nombre);
        $stmt->bindValue(":apellido_paterno", $apellido_paterno);
        $stmt->bindValue(":apellido_materno", $apellido_materno);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":password", $password);
        $stmt->bindValue(":estatus", $estatus);
        $stmt->bindValue(":niveles_usuarios_id_nivel_usuario", $nivel_usuario);
        $stmt->bindValue(":usuarios_id_usuario_movimiento", $usuario_movimiento);

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

        foreach ($data as $key => $value) {
            $clean_value = htmlspecialchars(strip_tags((string)$value));
            $stmt->bindValue(":$key", $clean_value);
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
        $stmt->bindValue(":id_usuario", $id);

        if ($stmt->execute()) {
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // --- MÉTODOS PARA ROLES (SIN CAMBIOS) ---
    public function getCarrreraIdByUsuarioId($usuario_id) {
        $sql = "SELECT c.id_carrera AS id_carrera
                FROM carreras c
                WHERE c.usuarios_id_usuario_coordinador = :usuario_id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id_carrera'] : null;
    }

    public function getCarrreraDataByUsuarioId($usuario_id) {
        $sql = "SELECT c.id_carrera, c.nombre
                FROM carreras c
                WHERE c.usuarios_id_usuario_coordinador = :usuario_id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getGruposIdByUsuarioId($usuario_id) {
        $sql = "SELECT g.id_grupo FROM grupos g WHERE g.usuarios_id_usuario_tutor = :usuario_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Devuelve un array plano de IDs
    }

    public function getGruposDataByUsuarioId($usuario_id) {
        $sql = "SELECT g.id_grupo, g.nombre
                FROM grupos g
                WHERE g.usuarios_id_usuario_tutor = :usuario_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- NUEVO MÉTODO AUXILIAR PARA OBTENER NOMBRES EN LOTE ---
    /**
     * Obtiene los nombres de múltiples usuarios en una sola consulta.
     * @param array $ids Array de id_usuario.
     * @return array Mapa de [id_usuario => 'Nombre Completo'].
     */
    public function getNombresByIds(array $ids) {
        if (empty($ids)) {
            return [];
        }
        // Prepara los placeholders para la cláusula IN (?, ?, ?)
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id_usuario, nombre, apellido_paterno, apellido_materno FROM usuarios WHERE id_usuario IN ($placeholders)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($ids);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $nombres = [];
        foreach ($results as $row) {
            $nombres[$row['id_usuario']] = "{$row['nombre']} {$row['apellido_paterno']} {$row['apellido_materno']}";
        }
        return $nombres;
    }

    // --- MÉTODOS DE VISTA REFACTORIZADOS ---

    //ADMIN (CORREGIDO)
    public function obtenerAlumnosParaAdminLv1($alumnoController, $conn) {
        // 1. La consulta en listarAlumnos() debería ser optimizada para incluir el nombre del tutor.
        // Asumiendo que ahora `listarAlumnos` devuelve `tutor_nombre`, `tutor_apellido_paterno`, etc.
        // Si no se puede cambiar `listarAlumnos`, usamos el método `getNombresByIds`.
        
        $lista = $alumnoController->listarAlumnos(); // Esta es la UNICA consulta principal
        
        // Agrupamos los datos en PHP sin más consultas
        $data = [];
        foreach ($lista as $row) {
            $data[$row['carrera']][$row['id_tutor']][$row['grupo']][] = $row;
        }

        // Obtenemos los nombres de todos los tutores necesarios en UNA SOLA consulta
        $tutor_ids = array_keys($data[$row['carrera']]);
        $nombresTutores = $this->getNombresByIds($tutor_ids);
        ?>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h4 mb-3 text-primary">Todos los Alumnos</h2>
                <?php foreach ($data as $carrera => $tutores): ?>
                    <h5 class="mt-4 text-secondary">Carrera: <?= htmlspecialchars($carrera) ?></h5>
                    <?php foreach ($tutores as $tutor_id => $grupos): 
                        // Ya no hay consulta aquí. Obtenemos el nombre del array pre-cargado.
                        $tutorNombre = $nombresTutores[$tutor_id] ?? "Desconocido";
                    ?>
                        <h6 class="mt-3">Tutor: <?= htmlspecialchars($tutorNombre) ?></h6>
                        <?php foreach ($grupos as $grupo => $alumnos): ?>
                            <div class="mb-2">
                                <span class="badge bg-info text-dark">Grupo: <?= htmlspecialchars($grupo) ?></span>
                                <ul class="list-group list-group-flush mt-2">
                                    <?php foreach ($alumnos as $a): ?>
                                        <li class="list-group-item"><?= htmlspecialchars("{$a['nombre']} {$a['apellido_paterno']} {$a['apellido_materno']}") ?></li>
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

    //TUTOR (CORREGIDO)
    public function obtenerAlumnosParaTutorLvl3($alumnoController, $conn, $usuario_id) {
        // 1. Obtener los datos de los grupos (ID y Nombre) en una consulta
        $grupos = $this->getGruposDataByUsuarioId($usuario_id);
        if (empty($grupos)) {
            echo "<div class='alert alert-info'>No tiene grupos asignados.</div>";
            return;
        }
    
        // 2. Extraer solo los IDs para la consulta de alumnos
        $grupos_ids = array_column($grupos, 'id_grupo');
    
        // 3. Obtener TODOS los alumnos de TODOS los grupos en una sola consulta
        // Se asume que existe un método `listByGroupIds` en la clase Alumno.
        $alumnos_todos = $alumnoController->alumno->listByGroupIds($grupos_ids);

        // 4. Agrupar alumnos por grupo en PHP
        $alumnos_por_grupo = [];
        foreach ($alumnos_todos as $alumno) {
            $alumnos_por_grupo[$alumno['grupos_id_grupo']][] = $alumno;
        }

        ?>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h4 mb-3 text-primary">Mis Alumnos</h2>
                <?php foreach ($grupos as $grupo): 
                    $id_grupo = $grupo['id_grupo'];
                    $nombre_grupo = $grupo['nombre'];
                    // Buscamos los alumnos para este grupo en el array ya cargado
                    $alumnos = $alumnos_por_grupo[$id_grupo] ?? [];
                    if (empty($alumnos)) continue;
                ?>
                <div class="mb-2">
                    <span class="badge bg-info text-dark">Grupo: <?= htmlspecialchars($nombre_grupo) ?></span>
                    <ul class="list-group list-group-flush mt-2">
                        <?php foreach ($alumnos as $a): ?>
                            <li class="list-group-item"><?= htmlspecialchars("{$a['nombre']} {$a['apellido_paterno']} {$a['apellido_materno']}") ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    //COORDINADOR (CORREGIDO)
    public function obtenerAlumnosParaCoordinadorLvl2($alumnoController, $conn, $auth, $usuario_id) {
        $carreraData = $auth->usuario->getCarrreraDataByUsuarioId($usuario_id);
        if (!$carreraData) {
            echo "<div class='alert alert-info'>No está asignado a ninguna carrera.</div>";
            return;
        }

        $carreraid = $carreraData['id_carrera'];
        $nombre_carrera = $carreraData['nombre'];
        
        // ÚNICA CONSULTA A LA BASE DE DATOS
        // Se asume que existe este método que trae todo con JOINs.
        $alumnos = $alumnoController->alumno->listByCarreraIdConTutorYGrupo($carreraid);

        if (empty($alumnos)) {
            echo "<div class='alert alert-info'>No hay alumnos registrados en su carrera.</div>";
            return;
        }
        
        // Agrupar los resultados en PHP, sin más consultas
        $data = [];
        foreach ($alumnos as $a) {
            $grupoNombre = $a['nombre_grupo'] ?? 'Sin Grupo';
            $tutorNombre = $a['nombre_tutor'] ?? 'Sin Tutor Asignado';
            $data[$grupoNombre][$tutorNombre][] = $a;
        }
        ?>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h4 mb-3 text-primary">Alumnos de la carrera: <?= htmlspecialchars($nombre_carrera) ?></h2>
                <?php foreach ($data as $grupo => $tutores): ?>
                   <div class="mb-2">
                    <span class="badge bg-info text-dark">Grupo: <?= htmlspecialchars($grupo) ?></span>
                        <?php foreach ($tutores as $tutor => $alumnos_grupo): ?>
                            <h6 class="mt-3">Tutor: <?= htmlspecialchars($tutor) ?></h6>
                            <ul class="list-group list-group-flush mb-3">
                                <?php foreach ($alumnos_grupo as $a): ?>
                                    <li class="list-group-item"><?= htmlspecialchars("{$a['nombre']} {$a['apellido_paterno']} {$a['apellido_materno']}") ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                   </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    public function obtenerAlumnosParaDirLvl4($alumnoController, $conn) {
        $this->obtenerAlumnosParaAdminLv1($alumnoController, $conn);
    }

    // --- OTROS MÉTODOS (SIN CAMBIOS) ---
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

    public function isActive($id_usuario) {
        $sql = "SELECT estatus FROM " . $this->table . " WHERE id_usuario = :id_usuario LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($result['estatus']) && $result['estatus'] == 1; // Usar == 1 para comparación no estricta
    }
}
?>