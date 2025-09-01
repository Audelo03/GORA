<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Estadisticas.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class EstadisticasController {
    public $estadisticas;

    public function __construct($conn) {
        $this->estadisticas = new Estadisticas($conn);
    }

    public function obtenerEstadisticas() {
        $datos = [];
        $datos['total_alumnos'] = $this->estadisticas->totalAlumnos();
        $datos['total_carreras'] = $this->estadisticas->totalCarreras();
        $datos['total_grupos'] = $this->estadisticas->totalGrupos();
        $datos['alumnos_por_carrera'] = $this->estadisticas->alumnosPorCarrera();
        
        // --- Nuevas Estadísticas ---
        $datos['alumnos_por_estatus'] = $this->estadisticas->alumnosPorEstatus();
        $datos['grupos_por_modalidad'] = $this->estadisticas->gruposPorModalidad();
        $datos['tasa_asistencia'] = $this->estadisticas->tasaDeAsistenciaGeneral();
        
        return $datos;
    }
}

// Este bloque se puede usar para probar el controlador directamente
// o para una API si se necesita.
if (isset($_GET['accion']) && $_GET['accion'] == 'obtener_datos') {
    header('Content-Type: application/json');
    $controller = new EstadisticasController($conn);
    $datos = $controller->obtenerEstadisticas();
    echo json_encode($datos);
}
?>