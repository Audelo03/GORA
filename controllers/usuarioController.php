<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Usuario.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class UsuarioController {
    
    public $usuario;

    public function __construct($conn) {
        $this->usuario = new Usuario($conn);
    }

    public function index() {
        echo json_encode($this->usuario->getAll());
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
            $total = $this->usuario->countAll($search);
            $totalPages = ceil($total / $limit);
            
            // Obtener usuarios paginados
            $usuarios = $this->usuario->getAllPaginated($offset, $limit, $search);
            
            echo json_encode([
                'success' => true,
                'usuarios' => $usuarios,
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

    public function listarUsuarios() {
        return $this->usuario->getAll();
    }

    public function listarCoordinadores() {
        return $this->usuario->fetchCoordinadores('coordinador');
    }

    public function verUsuario($id) {
        return $this->usuario->getById($id);
    }

    public function store() {
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $data = [
            'nombre' => $_POST['nombre'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'] ?? null,
            'email' => $_POST['email'],
            'password' => $hashedPassword,
            'estatus' => $_POST['estatus'],
            'niveles_usuarios_id_nivel_usuario' => $_POST['niveles_usuarios_id_nivel_usuario'],
            'usuarios_id_usuario_movimiento' => $_SESSION['usuario_id'] ?? null 
        ];
        
        $this->usuario->create($data);
        echo json_encode(["status" => "ok", "message" => "Usuario creado exitosamente"]);
    }

    public function update() {

        $id = $_POST['id_usuario'];
    
        $data = [
            'nombre' => $_POST['nombre'],
            'apellido_paterno' => $_POST['apellido_paterno'],
            'apellido_materno' => $_POST['apellido_materno'] ?? null,
            'email' => $_POST['email'],
            'estatus' => $_POST['estatus'],
            'niveles_usuarios_id_nivel_usuario' => $_POST['niveles_usuarios_id_nivel_usuario'],
            'usuarios_id_usuario_movimiento' => $_SESSION['usuario_id'] ?? null
        ];
        if (!empty($_POST['password'])) {
            $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
        $this->usuario->update($id, $data);
        echo json_encode(["status" => "ok", "message" => "Usuario actualizado exitosamente"]);
    }

    public function delete() {
        $id = $_POST['id_usuario'];
        $this->usuario->delete($id);
        echo json_encode(["status" => "ok", "message" => "Usuario eliminado exitosamente"]);
    }
    public function obtenerUsuarioPorId($user_id) {
        
        return $this->usuario->getById($user_id);
        
    }
}


if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $controller = new UsuarioController($conn);

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        echo json_encode(["error" => "Método $action no encontrado"]);
    }
}
?>