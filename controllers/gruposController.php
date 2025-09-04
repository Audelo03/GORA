<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Grupo.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class GruposController {
    private $grupo;

    public function __construct($conn) {
        $this->grupo = new Grupo($conn);
    }

    public function listar() {
        $stmt = $this->grupo->read();
        $grupos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $grupos;
    }

    public function crear($data) {
        $this->grupo->nombre = $data['nombre'];
        $this->grupo->estatus = $data['estatus'];
        $this->grupo->usuarios_id_usuario_tutor = $data['tutor'];
        $this->grupo->carreras_id_carrera = $data['carrera'];
        $this->grupo->modalidades_id_modalidad = $data['modalidad'];
        $this->grupo->usuarios_id_usuario_movimiento = $_SESSION['id_usuario']; // Asumiendo que el ID del usuario está en la sesión

        if ($this->grupo->create()) {
            return ["success" => true, "message" => "Grupo creado exitosamente."];
        } else {
            return ["success" => false, "message" => "No se pudo crear el grupo."];
        }
    }
    
    public function actualizar($data) {
        $this->grupo->id_grupo = $data['id_grupo'];
        $this->grupo->nombre = $data['nombre'];
        $this->grupo->estatus = $data['estatus'];
        $this->grupo->usuarios_id_usuario_tutor = $data['tutor'];
        $this->grupo->carreras_id_carrera = $data['carrera'];
        $this->grupo->modalidades_id_modalidad = $data['modalidad'];
        $this->grupo->usuarios_id_usuario_movimiento = $_SESSION['id_usuario'];

        if ($this->grupo->update()) {
            return ["success" => true, "message" => "Grupo actualizado exitosamente."];
        } else {
            return ["success" => false, "message" => "No se pudo actualizar el grupo."];
        }
    }

    public function eliminar($id) {
        $this->grupo->id_grupo = $id;
        if ($this->grupo->delete()) {
            return ["success" => true, "message" => "Grupo eliminado exitosamente."];
        } else {
            return ["success" => false, "message" => "No se pudo eliminar el grupo."];
        }
    }
}

// Manejo de la solicitud
if (isset($_GET['accion'])) {
    header('Content-Type: application/json');
    $controller = new GruposController($conn);

    switch ($_GET['accion']) {
        case 'listar':
            echo json_encode($controller->listar());
            break;
        case 'crear':
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->crear($data));
            break;
        case 'actualizar':
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->actualizar($data));
            break;
        case 'eliminar':
            if (isset($_GET['id'])) {
                echo json_encode($controller->eliminar($_GET['id']));
            } else {
                echo json_encode(["success" => false, "message" => "ID no proporcionado."]);
            }
            break;
    }
}
?>