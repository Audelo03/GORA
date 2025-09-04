<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Modalidad.php";

class ModalidadesController {
    private $modalidad;

    public function __construct($conn) {
        $this->modalidad = new Modalidad($conn);
    }

    public function listar() {
        $stmt = $this->modalidad->read();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (isset($_GET['accion']) && $_GET['accion'] == 'listar') {
    header('Content-Type: application/json');
    $controller = new ModalidadesController($conn);
    echo json_encode($controller->listar());
}
?>