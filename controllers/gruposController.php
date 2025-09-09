<?php
/**
 * CONTROLADOR DE GRUPOS - ITSADATA
 * 
 * Maneja todas las operaciones CRUD relacionadas con los grupos
 * de estudiantes en el sistema.
 */

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Grupo.php";

// Iniciar sesión si no está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class GruposController {
    private $grupo;

    /**
     * Constructor del controlador de grupos
     * @param PDO $conn - Conexión a la base de datos
     */
    public function __construct($conn) {
        $this->grupo = new Grupo($conn);
    }

    /**
     * Obtiene todos los grupos y los devuelve como JSON
     * @return void - Imprime JSON con todos los grupos
     */
    public function index() {
        echo json_encode($this->grupo->getAll());
    }

    /**
     * Obtiene grupos paginados con búsqueda
     * @return void - Imprime JSON con datos paginados
     */
    public function paginated() {
        try {
            // Obtener parámetros de paginación
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            
            // Validar parámetros
            if ($page < 1) $page = 1;
            if ($limit < 1 || $limit > 100) $limit = 10;
            
            $offset = ($page - 1) * $limit;
            
            // Obtener total de registros
            $total = $this->grupo->countAll($search);
            $totalPages = ceil($total / $limit);
            
            // Obtener grupos paginados
            $grupos = $this->grupo->getAllPaginated($offset, $limit, $search);
            
            echo json_encode([
                'success' => true,
                'grupos' => $grupos,
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

    /**
     * Crea un nuevo grupo
     * @return array - Respuesta con estado y mensaje
     */
    public function store() {
        // Asignar datos del formulario al objeto
        error_log("Entrando al metodo store del controlador!!!!".print_r($_POST,true));
        $this->grupo->nombre = $_POST['nombre'];
        $this->grupo->estatus = $_POST['estatus'];
        $this->grupo->usuarios_id_usuario_tutor = $_POST['usuarios_id_usuario_tutor'];
        $this->grupo->carreras_id_carrera = $_POST['carreras_id_carrera'];
        $this->grupo->modalidades_id_modalidad = $_POST['modalidades_id_modalidad'];
        $this->grupo->usuarios_id_usuario_movimiento = $_SESSION['usuario_id'] ?? null;

        // Intentar crear el grupo y devolver una respuesta JSON
        try {
            $this->grupo->create();
            return ["status" => "success", "message" => "Grupo creado exitosamente."];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
    
    /**
     * Actualiza un grupo existente
     * @return array - Respuesta con estado y mensaje
     */
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

        // Intentar actualizar y devolver una respuesta JSON
        try {
            $this->grupo->update();
            return ["status" => "success", "message" => "Grupo actualizado exitosamente."];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Elimina un grupo
     * @param int $id - ID del grupo a eliminar
     * @return array - Respuesta con estado y mensaje
     */
    public function delete($id) {
        $this->grupo->id_grupo = $id;
        // Intentar eliminar y devolver una respuesta JSON
        try {
            $this->grupo->delete();
            return ["status" => "success", "message" => "Grupo eliminado exitosamente."];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}

// ========================================
// ENRUTADOR DE ACCIONES
// ========================================

// Definir el tipo de contenido para todas las respuestas
header('Content-Type: application/json');

// Obtener la acción solicitada y crear instancia del controlador
$action = $_GET['action'] ?? null;
$controller = new GruposController($conn);
$response = [];

// Obtener el método HTTP de la petición
$method = $_SERVER['REQUEST_METHOD'];

// Procesar solicitudes GET
if ($method === 'GET' && $action === 'index') {
    // La función index ya hace 'echo', no necesita ser capturada
    $controller->index();
    exit;
} elseif ($method === 'GET' && $action === 'paginated') {
    // La función paginated ya hace 'echo', no necesita ser capturada
    $controller->paginated();
    exit;
} 
// Procesar solicitudes POST
elseif ($method === 'POST') {
    switch ($action) {
        case 'store':
            $response = $controller->store($_POST);
            break;
        case 'update':
            $response = $controller->update($_POST);
            break;
        case 'delete':
            // Verificar que el ID se esté enviando
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
    // Manejar métodos HTTP no soportados
    if($action){
         $response = ["status" => "error", "message" => "Método HTTP no soportado para esta acción."];
    }
    // Si no hay acción, no hacer nada para evitar errores innecesarios
}

// Imprimir la respuesta solo si no está vacía
if (!empty($response)) {
    echo json_encode($response);
}
?>
