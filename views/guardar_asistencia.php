<?php
session_start();
require_once "../config/db.php";
require_once "../controllers/alumnoController.php";
require_once "../controllers/asistenciaController.php";

header('Content-Type: application/json');

// validar si es tutor
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$id_grupo = $_POST['id_grupo'] ?? null;
$asistencias = $_POST['asistencias'] ?? [];
$fecha = date('Y-m-d');

if(!$id_grupo) {
    echo json_encode(['success' => false, 'error' => 'No se especificó el grupo']);
    exit();
}

$alumnoController = new AlumnoController($conn);
$alumnos_del_grupo = $alumnoController->getAlumnosByGrupo($id_grupo);

$asistenciaController = new AsistenciaController($conn);

try {
    $resultado = $asistenciaController->guardarAsistenciasGrupo($id_grupo, $fecha, $asistencias, $alumnos_del_grupo);
    if($resultado) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al guardar asistencia']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
