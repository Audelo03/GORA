<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Grupo.php";

// Iniciar sesión si no está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class GruposController {
    private $grupo;

    public function __construct($conn) {
        $this->grupo = new Grupo($conn);
    }

    // Obtiene todos los grupos y los devuelve como JSON.
    public function index() {
        echo json_encode($this->grupo->getAll());
    }

    public function store() {
        // Asignar datos del formulario al objeto
        error_log("Entrando al metodo store del controlador!!!!".print_r($_POST,true));
        $this->grupo->nombre = $_POST['nombre'];
        $this->grupo->estatus = $_POST['estatus'];
        $this->grupo->usuarios_id_usuario_tutor = $_POST['usuarios_id_usuario_tutor'];
        $this->grupo->carreras_id_carrera = $_POST['carreras_id_carrera'];
        $this->grupo->modalidades_id_modalidad = $_POST['modalidades_id_modalidad'];
        $this->grupo->usuarios_id_usuario_movimiento = $_SESSION['usuario_id'] ?? null;

        // Intentar crear el grupo y devolver una respuesta JSON.
        try {
            $this->grupo->create();
            return ["status" => "success", "message" => "Grupo creado exitosamente."];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    public function update() {
        error_log("Entrando al metodo update del controlador!!!!".print_r($_POST,true));
        // Asignar datos del formulario al objeto
        $this->grupo->id_grupo = $_POST['id_grupo'];
        $this->grupo->nombre = $_POST['nombre'];
        $this->grupo->estatus = $_POST['estatus'];
        $this->grupo->usuarios_id_usuario_tutor = $_POST['usuarios_id_usuario_tutor'];
        $this->grupo->carreras_id_carrera = $_POST['carreras_id_carrera'];
        $this->grupo->modalidades_id_modalidad = $_POST['modalidades_id_modalidad'];
        $this->grupo->usuarios_id_usuario_movimiento = $_SESSION['usuario_id'] ?? null;

        // Intentar actualizar y devolver una respuesta JSON.
        try {
            $this->grupo->update();
            return ["status" => "success", "message" => "Grupo actualizado exitosamente."];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // Elimina un grupo.
    public function delete($id) {
        $this->grupo->id_grupo = $id;
        // Intentar eliminar y devolver una respuesta JSON.
        try {
            $this->grupo->delete();
            return ["status" => "success", "message" => "Grupo eliminado exitosamente."];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}

// --- Enrutador de Acciones ---
// Define el tipo de contenido para todas las respuestas
header('Content-Type: application/json');
$action = $_GET['action'] ?? null;
$controller = new GruposController($conn);
$response = [];

// Usamos el método de la petición para decidir qué hacer
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && $action === 'index') {
    // La función index ya hace 'echo', por lo que no necesita ser capturada.
    $controller->index();
    exit;
} elseif ($method === 'POST') {
    switch ($action) {
        case 'store':
            $response = $controller->store($_POST);
            break;
        case 'update':
            $response = $controller->update($_POST);
            break;
        case 'delete':
            // Asegurarse de que el ID se está enviando
            if (isset($_POST['id'])) {
                $response = $controller->delete($_POST['id']);
            } else {
                $response = ["status" => "error", "message" => "ID no proporcionado para eliminar."];
            }
            break;
        default:
            $response = ["status" => "error", "message" => "Acción POST no válida."];
            break;
    }
} else {
    if($action){
         $response = ["status" => "error", "message" => "Método HTTP no soportado para esta acción."];
    }
    // Si no hay acción, no hacer nada para evitar errores innecesarios.
}

// Solo imprimir la respuesta si no está vacía
if (!empty($response)) {
    echo json_encode($response);
}
?>
