<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Carreras.php";
include "../public/functions_util.php";

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
         $data = [
            'nombre' => $_POST['nombre'],
            'fecha_creacion' => $_POST['fecha_creacion'],
            'usuarios_id_usuario_movimiento' => $_POST['usuarios_id_movimiento'] ?? null,
            'usuarios_id_usuario_coordinador' => $_POST['usuario_id'],
        ];
        $this->carrera->update($id, $data);
        echo json_encode(["status" => "ok"]);
    }

    public function delete() {
        $this->carrera->delete($_POST['id']);
        echo json_encode(["status" => "ok"]);
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