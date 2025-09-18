<?php
header('Content-Type: application/json');

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/modalidad.php";

class ModalidadesController {
    private $modalidad;
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->modalidad = new Modalidad($db);
    }
    
    // Acción para listar todas las modalidades
    public function index() {
        try {
            $modalidades = $this->modalidad->readAll();
            // Para 'index', el frontend solo espera el array de datos
            echo json_encode($modalidades);
        } catch (Exception $e) {
            // En caso de error, devolvemos un array vacío
            echo json_encode([]);
        }
    }

    public function paginated() {
        try {
            // Habilitar reporte de errores para debugging
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            
            // Validar parámetros
            if ($page < 1) $page = 1;
            if ($limit < 1 || $limit > 100) $limit = 10;
            
            $offset = ($page - 1) * $limit;
            
            // Verificar conexión a base de datos
            if (!$this->conn) {
                throw new Exception("No hay conexión a la base de datos");
            }
            
            // Obtener total de registros
            $total = $this->modalidad->countAll($search);
            $totalPages = ceil($total / $limit);
            
            // Obtener modalidades paginadas
            $modalidades = $this->modalidad->getAllPaginated($offset, $limit, $search);
            
            echo json_encode([
                'success' => true,
                'modalidades' => $modalidades,
                'total' => $total,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'limit' => $limit
            ]);
            
        } catch (Exception $e) {
            error_log("Error en paginated: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'message' => 'Error al cargar los datos',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    // Acción para guardar (crear) una nueva modalidad
    public function store() {
        // Obtiene los datos enviados por POST
        $data = $_POST;
        
        // Validación simple
        if (empty($data['nombre'])) {
            echo json_encode(['status' => 'error', 'message' => 'El nombre es obligatorio.']);
            return;
        }
        
        $this->modalidad->nombre = $data['nombre'];
        
        if ($this->modalidad->create()) {
            echo json_encode(['status' => 'success', 'message' => 'Modalidad creada exitosamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo crear la modalidad.']);
        }
    }

    // Acción para actualizar una modalidad existente
    public function update() {
        $data = $_POST;

        // Validación
        if (empty($data['id']) || empty($data['nombre'])) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos para actualizar.']);
            return;
        }

        $this->modalidad->id_modalidad = $data['id'];
        $this->modalidad->nombre = $data['nombre'];

        if ($this->modalidad->update()) {
            echo json_encode(['status' => 'success', 'message' => 'Modalidad actualizada exitosamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar la modalidad o no hubo cambios.']);
        }
    }

    // Acción para eliminar una modalidad
    public function delete() {
        $data = $_POST;

        if (empty($data['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'No se proporcionó un ID para eliminar.']);
            return;
        }
        
        $this->modalidad->id_modalidad = $data['id'];

        if ($this->modalidad->delete()) {
            echo json_encode(['status' => 'success', 'message' => 'Modalidad eliminada exitosamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar la modalidad.']);
        }
    }
}

// --- Enrutador Simple ---
// Esta parte del código actúa como un "enrutador": lee la 'action'
// y decide qué método del controlador ejecutar.

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new ModalidadesController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}

?>