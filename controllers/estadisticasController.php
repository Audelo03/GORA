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
        
        // --- Estadísticas Básicas ---
        $datos['total_alumnos'] = $this->estadisticas->totalAlumnos();
        $datos['total_carreras'] = $this->estadisticas->totalCarreras();
        $datos['total_grupos'] = $this->estadisticas->totalGrupos();
        $datos['alumnos_por_carrera'] = $this->estadisticas->alumnosPorCarrera();
        
        // --- Estadísticas Existentes ---
        $datos['alumnos_por_estatus'] = $this->estadisticas->alumnosPorEstatus();
        $datos['grupos_por_modalidad'] = $this->estadisticas->gruposPorModalidad();
        $datos['tasa_asistencia'] = $this->estadisticas->tasaDeAsistenciaGeneral();
        $datos['seguimientos_por_estatus'] = $this->estadisticas->seguimientosPorEstatus();
        $datos['seguimientos_por_tipo'] = $this->estadisticas->seguimientosPorTipo();
        
        // --- Nuevas Estadísticas Avanzadas ---
        $datos['usuarios_por_nivel'] = $this->estadisticas->usuariosPorNivel();
        $datos['alumnos_por_grupo'] = $this->estadisticas->alumnosPorGrupo();
        $datos['asistencia_por_mes'] = $this->estadisticas->asistenciaPorMes();
        $datos['seguimientos_por_mes'] = $this->estadisticas->seguimientosPorMes();
        $datos['productividad_tutores'] = $this->estadisticas->productividadTutores();
        $datos['alumnos_por_anio_ingreso'] = $this->estadisticas->alumnosPorAnioIngreso();
        $datos['carreras_mas_populares'] = $this->estadisticas->carrerasMasPopulares();
        $datos['modalidades_mas_utilizadas'] = $this->estadisticas->modalidadesMasUtilizadas();
        $datos['estadisticas_generales'] = $this->estadisticas->estadisticasGenerales();

        return $datos;
    }
}

if (isset($_GET['accion']) && $_GET['accion'] == 'obtener_datos') {
    header('Content-Type: application/json');
    $controller = new EstadisticasController($conn);
    $datos = $controller->obtenerEstadisticas();
    echo json_encode($datos);
}
?>