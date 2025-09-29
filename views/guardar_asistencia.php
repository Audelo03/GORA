<?php
// Endpoint JSON para guardar asistencia
header('Content-Type: application/json; charset=utf-8');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/alumnoController.php';
require_once __DIR__ . '/../controllers/asistenciaController.php';

// Validar método y autenticación
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$id_grupo = isset($_POST['id_grupo']) ? (int)$_POST['id_grupo'] : 0;
$asistencias = isset($_POST['asistencias']) && is_array($_POST['asistencias']) ? $_POST['asistencias'] : [];
$fecha = !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');

if ($id_grupo <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No se especificó el grupo']);
    exit;
}

$alumnoController = new AlumnoController($conn);
$alumnos_del_grupo = $alumnoController->getAlumnosByGrupo($id_grupo);

$asistenciaController = new AsistenciaController($conn);

try {
    $resultado = $asistenciaController->guardarAsistenciasGrupo($id_grupo, $fecha, $asistencias, $alumnos_del_grupo);
    if ($resultado) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al guardar asistencia']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Excepción: ' . $e->getMessage()]);
}
