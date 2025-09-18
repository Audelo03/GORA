<?php
// Debug específico para servidor Linux
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "=== DEBUG SERVIDOR LINUX ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Sistema Operativo: " . php_uname() . "\n\n";

try {
    // 1. Verificar archivos
    echo "1. Verificando archivos...\n";
    $files = [
        'config/db.php',
        'models/modalidad.php',
        'controllers/modalidadesController.php'
    ];
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            echo "   ✓ $file existe\n";
        } else {
            echo "   ✗ $file NO existe\n";
        }
    }
    
    // 2. Verificar permisos
    echo "\n2. Verificando permisos...\n";
    foreach ($files as $file) {
        if (file_exists($file)) {
            $perms = fileperms($file);
            echo "   $file: " . substr(sprintf('%o', $perms), -4) . "\n";
        }
    }
    
    // 3. Verificar conexión a base de datos
    echo "\n3. Verificando conexión a base de datos...\n";
    require_once "config/db.php";
    
    if ($conn) {
        echo "   ✓ Conexión establecida\n";
        
        // Verificar tabla modalidades
        $stmt = $conn->query("SHOW TABLES LIKE 'modalidades'");
        if ($stmt->rowCount() > 0) {
            echo "   ✓ Tabla 'modalidades' existe\n";
            
            // Verificar estructura
            $stmt = $conn->query("DESCRIBE modalidades");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "   Columnas: ";
            foreach ($columns as $col) {
                echo $col['Field'] . " ";
            }
            echo "\n";
            
            // Verificar datos
            $stmt = $conn->query("SELECT COUNT(*) as total FROM modalidades");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "   Total registros: " . $result['total'] . "\n";
            
            // Verificar si hay registros con estatus = 1
            $stmt = $conn->query("SELECT COUNT(*) as total FROM modalidades WHERE estatus = 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "   Registros activos (estatus=1): " . $result['total'] . "\n";
            
        } else {
            echo "   ✗ Tabla 'modalidades' NO existe\n";
        }
    } else {
        echo "   ✗ No se pudo conectar a la base de datos\n";
    }
    
    // 4. Probar modelo
    echo "\n4. Probando modelo Modalidad...\n";
    require_once "models/modalidad.php";
    
    $modalidad = new Modalidad($conn);
    echo "   ✓ Modelo creado\n";
    
    try {
        $total = $modalidad->countAll('');
        echo "   ✓ countAll(): $total\n";
    } catch (Exception $e) {
        echo "   ✗ Error en countAll(): " . $e->getMessage() . "\n";
    }
    
    try {
        $modalidades = $modalidad->getAllPaginated(0, 10, '');
        echo "   ✓ getAllPaginated(): " . count($modalidades) . " registros\n";
    } catch (Exception $e) {
        echo "   ✗ Error en getAllPaginated(): " . $e->getMessage() . "\n";
    }
    
    // 5. Probar controlador
    echo "\n5. Probando controlador...\n";
    require_once "controllers/modalidadesController.php";
    
    $controller = new ModalidadesController($conn);
    echo "   ✓ Controlador creado\n";
    
    // Simular parámetros GET
    $_GET['action'] = 'paginated';
    $_GET['page'] = '1';
    $_GET['limit'] = '10';
    $_GET['search'] = '';
    
    echo "   Probando método paginated()...\n";
    ob_start();
    $controller->paginated();
    $output = ob_get_clean();
    
    echo "   Respuesta: " . substr($output, 0, 200) . "...\n";
    
    // 6. Verificar logs de error
    echo "\n6. Verificando logs de error...\n";
    $error_log = ini_get('error_log');
    if ($error_log && file_exists($error_log)) {
        echo "   Log de errores: $error_log\n";
        $last_errors = file_get_contents($error_log);
        $lines = explode("\n", $last_errors);
        $recent_lines = array_slice($lines, -10);
        echo "   Últimas 10 líneas del log:\n";
        foreach ($recent_lines as $line) {
            if (trim($line)) {
                echo "   " . $line . "\n";
            }
        }
    } else {
        echo "   No se encontró log de errores\n";
    }
    
} catch (Exception $e) {
    echo "\nERROR GENERAL: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DEBUG ===\n";
?>
