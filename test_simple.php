<?php
// Test simple para diagnosticar el error
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TEST SIMPLE MODALIDADES ===\n";

try {
    // Incluir archivos
    require_once "config/db.php";
    require_once "models/modalidad.php";
    
    echo "1. Archivos incluidos: OK\n";
    
    // Verificar conexión
    if (!$conn) {
        throw new Exception("No hay conexión");
    }
    echo "2. Conexión DB: OK\n";
    
    // Probar consulta directa
    $stmt = $conn->query("SELECT COUNT(*) as total FROM modalidades WHERE estatus = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "3. Consulta directa COUNT: " . $result['total'] . "\n";
    
    // Probar consulta con LIMIT
    $stmt = $conn->query("SELECT id_modalidad, nombre FROM modalidades WHERE estatus = 1 ORDER BY nombre ASC LIMIT 10 OFFSET 0");
    $modalidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "4. Consulta con LIMIT: " . count($modalidades) . " registros\n";
    
    // Probar modelo
    $modalidad = new Modalidad($conn);
    echo "5. Modelo creado: OK\n";
    
    $total = $modalidad->countAll('');
    echo "6. countAll(): " . $total . "\n";
    
    $modalidades = $modalidad->getAllPaginated(0, 10, '');
    echo "7. getAllPaginated(): " . count($modalidades) . " registros\n";
    
    echo "\n=== RESULTADO FINAL ===\n";
    echo json_encode([
        'success' => true,
        'modalidades' => $modalidades,
        'total' => $total
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}
?>
