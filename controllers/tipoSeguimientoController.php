<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/TipoSeguimiento.php";

class TipoSeguimientoController {
    private $tipo;

    public function __construct($conn) {
        $this->tipo = new TipoSeguimiento($conn);
    }

    public function listar() {
        $stmt = $this->tipo->read();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (isset($_GET['accion']) && $_GET['accion'] == 'listar') {
    header('Content-Type: application/json');
    $controller = new TipoSeguimientoController($conn);
    echo json_encode($controller->listar());
}
?>