<?php
require_once __DIR__ . '/../controllers/alumnoController.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/nivelController.php';

$auth = new AuthController($conn);
$auth->checkAuth();

$nivelController = new nivelController($conn);

$niveles_autorizados = [1 => "Admin", 2 => "Coordinador", 3 => "Tutor", 4 => "Director"];

$usuario_id = $_SESSION['usuario_id'];
$nivel = $_SESSION['usuario_nivel'];
$nivel_nombre  = $niveles_autorizados[$nivel] ?? "Desconocido"; 

$nombre = $_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido_paterno'] . ' ' . $_SESSION['usuario_apellido_materno'];

$alumnoController = new AlumnoController($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Alumnos</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/sidebar.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">Tabla de Alumnos</h1>
                <p class="mb-0 text-muted">Nivel: <strong><?= $nivel_nombre ?></strong></p>
                <p class="mb-0 text-muted">Usuario: <strong><?= $nombre ?></strong></p>
            </div>
            <a href="login.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </div>

    <?php
    switch ($nivel) {
        case 4:
            $nivelController->fetchLvl4($alumnoController, $conn); 
            break;
        case 3:
            $nivelController->fetchLvl3($alumnoController, $conn, $usuario_id, $auth); 
            break;
        case 1:
            $nivelController->fetchLvl1($alumnoController, $conn);
            break;
        case 2:
            $nivelController->fetchLvl2($alumnoController, $conn, $auth, $usuario_id);
            break;
        default:
            // Nivel no autorizado
            echo '<div class="alert alert-danger">No tiene permisos para ver esta página.</div>';
            break;
    }
    ?>
</div>

<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
