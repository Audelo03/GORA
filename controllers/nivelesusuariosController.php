<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/NivelUsuario.php";

class NivelesUsuariosController {
    private $nivel;

    public function __construct($conn) {
        $this->nivel = new NivelUsuario($conn);
    }

    public function listar() {
        $stmt = $this->nivel->read();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (isset($_GET['accion']) && $_GET['accion'] == 'listar') {
    header('Content-Type: application/json');
    $controller = new NivelesUsuariosController($conn);
    echo json_encode($controller->listar());
}
?>