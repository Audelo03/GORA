<?php
// Simple routing system for ITSADATA
session_start();

// Include database connection
require_once 'config/db.php';

// Include utility functions
require_once 'public/functions_util.php';

// Simple routing
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');

// Remove base path if exists
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $path = substr($path, strlen($basePath));
}

// Remove leading slash again after base path removal
$path = ltrim($path, '/');

// Handle .php files by removing .php extension
if (substr($path, -4) === '.php') {
    $path = substr($path, 0, -4);
}

// If no path, default to login
if (empty($path)) {
    $path = 'login';
}

// Map paths to view files
$viewMap = [
    'login' => 'views/login.php',
    'dashboard' => 'views/dashboard.php',
    'alumnos' => 'views/CRUDS/alumnos.php',
    'alumnos-paginados' => 'views/alumnos_paginados.php',
    'alumnos_paginados' => 'views/alumnos_paginados.php',
    'carreras' => 'views/CRUDS/carreras.php',
    'grupos' => 'views/CRUDS/grupos.php',
    'modalidades' => 'views/CRUDS/modalidades.php',
    'tipo-seguimiento' => 'views/CRUDS/tipo_seguimiento.php',
    'usuarios' => 'views/CRUDS/usuarios.php',
    'asistencia' => 'views/asistencia.php',
    'estadisticas' => 'views/estadisticas.php',
    'listas' => 'views/listas.php',
    'seguimientos' => 'views/seguimientos.php',
    'crear-seguimiento' => 'views/crear_seguimiento.php',
    'crear_seguimiento' => 'views/crear_seguimiento.php',
    'editar-seguimiento' => 'views/editar_seguimiento.php',
    'editar_seguimiento' => 'views/editar_seguimiento.php',
    'ver-seguimientos' => 'views/ver_seguimientos.php',
    'ver_seguimientos' => 'views/ver_seguimientos.php',
    'gestionar-listas' => 'views/gestionar_listas.php',
    'gestionar_listas' => 'views/gestionar_listas.php',
    'guardar_asistencia' => 'views/guardar_asistencia.php',
    'profile' => 'views/profile.php',
    'logout' => 'views/logout.php'
];

// Check if path exists in view map
if (isset($viewMap[$path])) {
    $viewFile = $viewMap[$path];
    if (file_exists($viewFile)) {
        include $viewFile;
        exit;
    }
}

// Try to load view directly
$viewFile = "views/{$path}.php";
if (file_exists($viewFile)) {
    include $viewFile;
    exit;
}

// 404 Not Found
http_response_code(404);
echo "<h1>404 - Page Not Found</h1>";
echo "<p>The page you requested could not be found.</p>";
echo "<a href='/'>Go to Home</a>";
?>
