<?php
// Forzamos la visualización de errores para la depuración, pero los atraparemos más abajo.
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/alumnoController.php';

try {
    $auth = new AuthController($conn);
    $auth->checkAuth();
    $alumnoController = new AlumnoController($conn);

    $action = $_GET['action'] ?? 'load_all';

    if ($action === 'load_students') {
        $id_grupo = isset($_GET['id_grupo']) ? (int)$_GET['id_grupo'] : 0;
        $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        if ($id_grupo > 0) {
            $html = $alumnoController->renderizarListaAlumnosPaginados($id_grupo, $pagina);
            header('Content-Type: application/json');
            echo json_encode(['html' => $html]);
        } else {
            http_response_code(400); // Bad Request
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID de grupo no válido.']);
        }
        exit; // Termina la ejecución para no procesar el resto del script
    }

    $paginaActual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $terminoBusqueda = $_GET['termino'] ?? '';
    $registrosPorPagina = 5; 
    $offset = ($paginaActual - 1) * $registrosPorPagina;

    $nivelUsuario = $_SESSION['usuario_nivel'] ?? 0;
    $idUsuario = $_SESSION['usuario_id'] ?? 0;


    $totalRegistros = 0;
    $dataParaRenderizar = [];

    switch ($nivelUsuario) {
        case 1: 
        case 4:
            $totalRegistros = $alumnoController->contarTotalCarreras($terminoBusqueda);
            $dataParaRenderizar = $alumnoController->obtenerCarrerasPaginadas($terminoBusqueda, $offset, $registrosPorPagina);
            break;
        case 2:
            $idCarrera = $auth->usuario->getCarrreraDataByUsuarioId($idUsuario)['id_carrera'] ?? 0;
            $totalRegistros = $alumnoController->contarTotalGruposPorCarrera($idCarrera, $terminoBusqueda);
            $dataParaRenderizar = $alumnoController->obtenerGruposPaginadosPorCarrera($idCarrera, $terminoBusqueda, $offset, $registrosPorPagina);
            break;
        case 3:
            $totalRegistros = $alumnoController->contarTotalGruposPorTutor($idUsuario, $terminoBusqueda);
            $dataParaRenderizar = $alumnoController->obtenerGruposPaginadosPorTutor($idUsuario, $terminoBusqueda, $offset, $registrosPorPagina);
            break;
    }

    $totalPages = ceil($totalRegistros / $registrosPorPagina);

    ob_start();
    switch ($nivelUsuario) {
        case 1:
        case 4:
            foreach ($dataParaRenderizar as $carrera) {
                echo $alumnoController->renderizarAcordeonCarrera($carrera, $conn, $auth);
            }
            break;
        case 2:
            $dataCarrera = $auth->usuario->getCarrreraDataByUsuarioId($idUsuario);
            if ($dataCarrera) {
                echo "<div class='alert alert-info'>Mostrando grupos para la carrera: <strong>".htmlspecialchars($dataCarrera['nombre'])."</strong></div>";
            }
            echo $alumnoController->listarAlumnosPorIdsDeGrupos($dataParaRenderizar, $conn);
            break;
        case 3:
            echo $alumnoController->listarAlumnosPorIdsDeGrupos($dataParaRenderizar, $conn);
            break;
    }

    if (empty($dataParaRenderizar) && $totalRegistros === 0) {
        echo '<div class="alert alert-warning">No se encontraron resultados para "'.htmlspecialchars($terminoBusqueda).'".</div>';
    }

    $html = ob_get_clean();

    header('Content-Type: application/json');
    echo json_encode([
        'html' => $html,
        'totalPages' => $totalPages,
        'currentPage' => $paginaActual
    ]);

} catch (Throwable $e) {
    http_response_code(500); // Código de error del servidor
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Ocurrió un error en el servidor.',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}