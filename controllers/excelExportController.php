<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../controllers/estadisticasController.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class ExcelExportController {
    private $conn;
    private $estadisticasController;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->estadisticasController = new EstadisticasController($conn);
    }

    public function exportarEstadisticasCompletas() {
        try {
            // Obtener todos los datos
            $datos = $this->estadisticasController->obtenerEstadisticas();
            
            // Configurar headers para descarga de Excel
            $filename = 'estadisticas_completas_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // Generar datos optimizados para exportación
            $this->exportarDatosParaJS($datos);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al exportar: ' . $e->getMessage()]);
        }
    }

    private function exportarDatosParaJS($datos) {
        // Estructura optimizada para JavaScript
        $exportData = [
            'metadata' => [
                'fecha_generacion' => date('Y-m-d H:i:s'),
                'usuario' => $_SESSION['usuario_nombre'] ?? 'Sistema',
                'total_registros' => count($datos)
            ],
            'resumen_general' => [
                'total_alumnos' => $datos['total_alumnos'],
                'total_carreras' => $datos['total_carreras'],
                'total_grupos' => $datos['total_grupos'],
                'tasa_asistencia' => $datos['tasa_asistencia'],
                'estadisticas_generales' => $datos['estadisticas_generales']
            ],
            'datasets' => [
                'alumnos_por_estatus' => $this->formatearDataset($datos['alumnos_por_estatus']),
                'usuarios_por_nivel' => $this->formatearDataset($datos['usuarios_por_nivel']),
                'seguimientos_por_estatus' => $this->formatearDataset($datos['seguimientos_por_estatus']),
                'seguimientos_por_tipo' => $this->formatearDataset($datos['seguimientos_por_tipo']),
                'alumnos_por_carrera' => $this->formatearDataset($datos['alumnos_por_carrera']),
                'grupos_por_modalidad' => $this->formatearDataset($datos['grupos_por_modalidad']),
                'alumnos_por_grupo' => $this->formatearDataset($datos['alumnos_por_grupo']),
                'asistencia_por_mes' => $this->formatearDataset($datos['asistencia_por_mes']),
                'seguimientos_por_mes' => $this->formatearDataset($datos['seguimientos_por_mes']),
                'productividad_tutores' => $this->formatearDataset($datos['productividad_tutores']),
                'alumnos_por_anio_ingreso' => $this->formatearDataset($datos['alumnos_por_anio_ingreso']),
                'carreras_mas_populares' => $this->formatearDataset($datos['carreras_mas_populares']),
                'modalidades_mas_utilizadas' => $this->formatearDataset($datos['modalidades_mas_utilizadas'])
            ]
        ];

        header('Content-Type: application/json');
        echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function formatearDataset($data) {
        if (!is_array($data) || empty($data)) {
            return [];
        }

        $formatted = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $formatted[] = $item;
            }
        }
        
        return $formatted;
    }

    public function exportarDatasetEspecifico($dataset) {
        try {
            $datos = $this->estadisticasController->obtenerEstadisticas();
            
            if (!isset($datos[$dataset])) {
                throw new Exception("Dataset no encontrado: $dataset");
            }

            $filename = $dataset . '_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // BOM para UTF-8
            echo "\xEF\xBB\xBF";
            
            $output = fopen('php://output', 'w');
            
            $dataArray = $datos[$dataset];
            if (!empty($dataArray)) {
                // Escribir headers
                $headers = array_keys($dataArray[0]);
                fputcsv($output, $headers);
                
                // Escribir datos
                foreach ($dataArray as $row) {
                    fputcsv($output, array_values($row));
                }
            }
            
            fclose($output);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al exportar dataset: ' . $e->getMessage()]);
        }
    }
}

// Manejar requests
if (isset($_GET['accion'])) {
    $controller = new ExcelExportController($conn);
    
    switch ($_GET['accion']) {
        case 'exportar_completo':
            $controller->exportarEstadisticasCompletas();
            break;
            
        case 'exportar_dataset':
            $dataset = $_GET['dataset'] ?? '';
            if ($dataset) {
                $controller->exportarDatasetEspecifico($dataset);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Dataset no especificado']);
            }
            break;
            
        case 'obtener_datos_json':
            header('Content-Type: application/json');
            $estadisticasController = new EstadisticasController($conn);
            $datos = $estadisticasController->obtenerEstadisticas();
            
            $response = [
                'metadata' => [
                    'fecha_generacion' => date('Y-m-d H:i:s'),
                    'usuario' => $_SESSION['usuario_nombre'] ?? 'Sistema',
                    'sistema' => 'GORA - Sistema de Gestión Académica'
                ],
                'success' => true,
                'data' => $datos
            ];
            
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida']);
    }
}
?>
