<?php
// Test simplificado del controlador
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simular parámetros GET
$_GET['action'] = 'paginated';
$_GET['page'] = '1';
$_GET['limit'] = '10';
$_GET['search'] = '';

echo "=== TEST CONTROLADOR SIMPLIFICADO ===\n";

try {
    require_once "config/db.php";
    require_once "models/modalidad.php";
    require_once "controllers/modalidadesController.php";
    
    echo "1. Archivos incluidos: OK\n";
    
    if (!$conn) {
        throw new Exception("No hay conexión a la base de datos");
    }
    echo "2. Conexión DB: OK\n";
    
    $controller = new ModalidadesController($conn);
    echo "3. Controlador creado: OK\n";
    
    echo "4. Ejecutando método paginated()...\n";
    
    // Capturar la salida
    ob_start();
    $controller->paginated();
    $output = ob_get_clean();
    
    echo "5. Respuesta del controlador:\n";
    echo $output . "\n";
    
    // Intentar parsear como JSON
    $data = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "6. JSON válido: OK\n";
        if (isset($data['success'])) {
            echo "   Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        }
    } else {
        echo "6. Error en JSON: " . json_last_error_msg() . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
