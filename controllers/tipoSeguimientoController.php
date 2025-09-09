<?php
// controllers/tipoSeguimientoController.php

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/TipoSeguimiento.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class TipoSeguimientoController {
    
    public $tipoSeguimiento;

    public function __construct($conn) {
        $this->tipoSeguimiento = new TipoSeguimiento($conn);
    }

    // Acción para listar todos los registros (GET)
    public function index() {
        echo json_encode($this->tipoSeguimiento->getAll());
    }

    public function paginated() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            
            // Validar parámetros
            if ($page < 1) $page = 1;
            if ($limit < 1 || $limit > 100) $limit = 10;
            
            $offset = ($page - 1) * $limit;
            
            // Obtener total de registros
            $total = $this->tipoSeguimiento->countAll($search);
            $totalPages = ceil($total / $limit);
            
            // Obtener tipos de seguimiento paginados
            $tiposSeguimiento = $this->tipoSeguimiento->getAllPaginated($offset, $limit, $search);
            
            echo json_encode([
                'success' => true,
                'tiposSeguimiento' => $tiposSeguimiento,
                'total' => $total,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'limit' => $limit
            ]);
            
        } catch (Exception $e) {
            error_log("Error en paginated: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al cargar los datos',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Acción para mostrar un registro específico (GET)
    public function show() {
        if (isset($_GET['id'])) {
            echo json_encode($this->tipoSeguimiento->getById($_GET['id']));
        } else {
            echo json_encode(["error" => "ID no proporcionado"]);
        }
    }
    
    // Acción para guardar un nuevo registro (POST)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nombre'])) {
            $data = ['nombre' => $_POST['nombre']];
            
            if ($this->tipoSeguimiento->create($data)) {
                echo json_encode(["success" => "Registro creado correctamente"]);
            } else {
                echo json_encode(["error" => "Error al crear el registro"]);
            }
        } else {
            echo json_encode(["error" => "Datos incompletos o método no permitido"]);
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !empty($_POST['nombre'])) {
            $id = $_POST['id'];
            $data = ['nombre' => $_POST['nombre']];
            
            if ($this->tipoSeguimiento->update($id, $data)) {
                echo json_encode(["success" => "Registro actualizado correctamente"]);
            } else {
                echo json_encode(["error" => "Error al actualizar el registro"]);
            }
        } else {
            echo json_encode(["error" => "Datos incompletos o método no permitido"]);
        }
    }

    // Acción para eliminar un registro (POST)
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            if ($this->tipoSeguimiento->delete($id)) {
                echo json_encode(["success" => "Registro eliminado correctamente"]);
            } else {
                echo json_encode(["error" => "Error al eliminar el registro"]);
            }
        } else {
            echo json_encode(["error" => "ID no proporcionado o método no permitido"]);
        }
    }
}

if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];
    $controller = new TipoSeguimientoController($conn);

    if (method_exists($controller, $action)) {
        error_log("Ejecutando acción: $action");
        $controller->$action();
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Acción no encontrada: $action"]);
    }
}
?>