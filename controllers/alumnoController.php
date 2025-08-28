<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Alumno.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class AlumnoController {
    public $alumno;

    public function __construct($conn) {
        $this->alumno = new Alumno($conn);
    }

    public function index() {
        echo json_encode($this->alumno->getAll());
    }

    public function listarAlumnos() {
        return $this->alumno->getAll();
    }

    public function verAlumno($id) {
        return $this->alumno->getById($id);
    }

    public function store() {
        $data = [
            'matricula' => $_POST['matricula'],
            'nombre' => $_POST['nombre'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'] ?? null,
            'estatus' => $_POST['estatus'],
            'usuarios_id_usuario_movimiento' => $_SESSION['usuario_id'] ?? null,
            'carreras_id_carrera' => $_POST['carreras_id_carrera'],
            'grupos_id_grupo' => $_POST['grupos_id_grupo'] ?? null
        ];
        $this->alumno->create($data);
        echo json_encode(["status" => "ok"]);
    }

    public function update() {
        $id = $_POST['id'];
        $data = [
            'matricula' => $_POST['matricula'],
            'nombre' => $_POST['nombre'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'] ?? null,
            'estatus' => $_POST['estatus'],
            'usuarios_id_usuario_movimiento' => $_SESSION['usuario_id'] ?? null,
            'carreras_id_carrera' => $_POST['carreras_id_carrera'],
            'grupos_id_grupo' => $_POST['grupos_id_grupo'] ?? null
        ];
        $this->alumno->update($id, $data);
        echo json_encode(["status" => "ok"]);
    }

    public function delete() {
        $this->alumno->delete($_POST['id']);
        echo json_encode(["status" => "ok"]);
    }

    public function obtenerAlumnosParaTutorLvl4($usuario_id) {
        $alumnos = $this->alumno->getByTutorId($usuario_id);
        $grupos = [];
        foreach ($alumnos as $a) $grupos[$a['grupos_id_grupo']][] = $a;

        echo "<h2>Mis Alumnos</h2>";
        foreach ($grupos as $id_grupo => $alumnos) {
            global $conn;
            $stmt = $conn->prepare("SELECT nombre FROM grupos WHERE id_grupo = :id_grupo");
            $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
            $stmt->execute();
            $grupo = $stmt->fetch(PDO::FETCH_ASSOC)['nombre'] ?? "Desconocido";

            echo "<h3 class='mt-3'>Grupo: {$grupo}</h3>";
            echo "<ul class='list-group mb-3'>";
            foreach ($alumnos as $a) {
                echo "<li class='list-group-item'>{$a['nombre']} {$a['apellido_paterno']} {$a['apellido_materno']}</li>";
            }
            echo "</ul>";
        }
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new AlumnoController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>
