
<?php
// Archivo de prueba para diagnosticar el error 500
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Iniciando prueba...\n";

try {
    // Incluir archivos necesarios
    require_once "config/db.php";
    require_once "models/modalidad.php";
    
    echo "Archivos incluidos correctamente\n";
    
    // Verificar conexión
    if (!$conn) {
        throw new Exception("No hay conexión a la base de datos");
    }
    echo "Conexión a base de datos: OK\n";
    
    // Crear instancia del modelo
    $modalidad = new Modalidad($conn);
    echo "Modelo Modalidad creado: OK\n";
    
    // Probar método countAll
    $total = $modalidad->countAll('');
    echo "Total de modalidades: " . $total . "\n";
    
    // Probar método getAllPaginated
    $modalidades = $modalidad->getAllPaginated(0, 10, '');
    echo "Modalidades obtenidas: " . count($modalidades) . "\n";
    
    // Mostrar resultado
    echo "Resultado JSON:\n";
    echo json_encode([
        'success' => true,
        'modalidades' => $modalidades,
        'total' => $total,
        'totalPages' => ceil($total / 10),
        'currentPage' => 1,
        'limit' => 10
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
