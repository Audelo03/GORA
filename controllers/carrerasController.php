<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Carreras.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class CarrerasController {
    
    public $carrera;

    public function __construct($conn) {
        $this->carrera = new Carrera($conn);
    }

    public function index() {
        echo json_encode($this->carrera->getAll());
    }

    public function listarCarreras() {
        return $this->carrera->getAll();
    }

    public function verCarreras($id) {
        return $this->carrera->getById($id);
    }

    public function store() {
        $data = [
            'nombre' => $_POST['nombre'],
            'fecha_creacion' => $_POST['fecha_creacion'],
            'usuarios_id_usuario_movimiento' => $_POST['usuarios_id_movimiento'] ?? null,
            'usuarios_id_usuario_coordinador' => $_POST['usuario_id'],
        ];
        $this->carrera->create($data);
        echo json_encode(["status" => "ok"]);
    }

    public function update() {
        $id = $_POST['id'];
        error_log("Datos recibidos: " . print_r($_POST, true));
         $data = [
            'nombre' => $_POST['nombre'],
            'usuarios_id_usuario_coordinador' => $_POST['usuario_id'],
        ];
        if($this->carrera->update($id, $data)===true);
            echo json_encode(["status" => "ok"]);
    }

   public function delete() {
    try {
    
        $this->carrera->delete($_POST['id']);

        echo json_encode(['status' => 'success', 'message' => 'La carrera ha sido eliminada correctamente.']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit; 
}
}


if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new CarrerasController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>