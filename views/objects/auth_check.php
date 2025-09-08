<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    
    header("Location: ".$modificacion_ruta."../views/login.php");
    exit(); 
}

$currentPage = basename($_SERVER['PHP_SELF']);

$userLevel = $_SESSION['usuario_nivel'] ?? null;


$page_permissions = [
    // --- Páginas de Acceso General (para todos) ---
    'profile.php'   => [1, 2, 3, 4],
    'logout.php'    => [1, 2, 3, 4],
    'listas.php'    => [1, 2, 3, 4],
    // --- Páginas de Administración (Solo Admin) ---
    'dashboard.php'        => [1,4],
    'usuarios.php'        => [1,4],
    'carreras.php'        => [1,4],
    'nivel_usuario.php'   => [1,4],
    'modalidades.php'     => [1,4], 
    'tipo_seguimiento.php'=> [1,4], 
   

    // --- Páginas de Coordinación (Admin y Coordinador) ---
    'grupos.php'    => [1, 2],
    'alumnos.php'   => [1, 2],

    // --- Páginas de Tutoría (Admin, Coordinador y Tutor) ---
    'seguimientos.php'       => [1, 2, 3,4],
    'ver_seguimientos.php'   => [1, 2, 3,4],
    'crear_seguimiento.php'  => [1, 2, 3,4],
    'editar_seguimiento.php' => [1, 2, 3,4],
    'asistencia.php'         => [1, 2, 3,4],
    'gestionar_listas.php'   => [1, 2, 3,4],

    'estadisticas.php' => [1, 4],
];


if (isset($page_permissions[$currentPage])) {
    $allowedLevels = $page_permissions[$currentPage];

    if (!in_array($userLevel, $allowedLevels)) {
        $_SESSION['error_message'] = "No tienes los permisos necesarios para acceder a esta página.";
    header("Location: ".$modificacion_ruta."../views/login.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Acceso denegado a recurso no definido.";
    header("Location: ".$modificacion_ruta."../views/login.php");
    exit();
}

?>