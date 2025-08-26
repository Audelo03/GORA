<?php
require_once __DIR__ . '/../controllers/alumnoController.php';
require_once __DIR__ . '/../controllers/authController.php';

$auth = new AuthController($conn);
$auth->checkAuth();

$usuario_id = $_SESSION['usuario_id'];
$nivel = $_SESSION['usuario_nivel'];

$alumnoController = new AlumnoController($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Alumnos</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Tabla de Alumnos</h1>
        <a href="login.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>

    <?php
    if ($nivel == 4) {
        $stmt = $conn->prepare("SELECT id_tutor FROM tutores WHERE usuarios_id_usuario = :usuario_id");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tutor) {
            $alumnos = $alumnoController->alumno->getByTutorId($tutor['id_tutor']);
            $grupos = [];
            foreach ($alumnos as $a) $grupos[$a['grupos_id_grupo']][] = $a;

            echo "<h2>Mis Alumnos</h2>";
            foreach ($grupos as $id_grupo => $alumnos) {
                $stmt = $conn->prepare("SELECT nombre FROM grupos WHERE id_grupo = :id_grupo");
                $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
                $stmt->execute();
                $grupo = $stmt->fetch(PDO::FETCH_ASSOC)['nombre'] ?? "Desconocido";

                echo "<h3 class='mt-3'>Grupo: {$grupo}</h3>";
                echo "<ul class='list-group mb-3'>";
                foreach ($alumnos as $a) {
                    echo "<li class='list-group-item'>{$a['nombre']} {$a['apellido_paterno']} {$a['apellido_materno']}</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<div class='alert alert-warning'>No se encontró un perfil de tutor asociado.</div>";
        }
    }

    if ($nivel == 1 || $nivel == 9) {
        $lista = $alumnoController->listarAlumnos();
        $data = [];
        foreach ($lista as $row) $data[$row['carrera']][$row['tutor']][$row['grupo']][] = $row;

        echo "<h2>Todos los Alumnos</h2>";
        foreach ($data as $carrera => $tutores) {
            echo "<h3 class='mt-4'>Carrera: {$carrera}</h3>";
            foreach ($tutores as $tutor_id => $grupos) {
                $tutor = $conn->prepare("SELECT nombre, apellido_paterno, apellido_materno FROM usuarios WHERE id_usuario = :id");
                $tutor->bindParam(':id', $tutor_id, PDO::PARAM_INT);
                $tutor->execute();
                $tutorData = $tutor->fetch(PDO::FETCH_ASSOC);
                $tutorNombre = $tutorData ? "{$tutorData['nombre']} {$tutorData['apellido_paterno']} {$tutorData['apellido_materno']}" : "Desconocido";

                echo "<h4 class='mt-3'>Tutor: {$tutorNombre}</h4>";
                foreach ($grupos as $grupo => $alumnos) {
                    echo "<h5>Grupo: {$grupo}</h5><ul class='list-group mb-3'>";
                    foreach ($alumnos as $a) echo "<li class='list-group-item'>{$a['nombre']} {$a['apellido_paterno']} {$a['apellido_materno']}</li>";
                    echo "</ul>";
                }
            }
        }
    }

    if ($nivel == 2) {
        $carreraid = $auth->usuario->getCarrreraIdByUsuarioId($usuario_id);
        $alumnos = $alumnoController->alumno->listByCarreraId($carreraid);
        $nombre_carrera = $conn->prepare("SELECT nombre FROM carreras WHERE id_carrera = :id");
        $nombre_carrera->bindParam(':id', $carreraid, PDO::PARAM_INT);
        $nombre_carrera->execute();
        $nombre_carrera = $nombre_carrera->fetch(PDO::FETCH_ASSOC)['nombre'] ?? "Desconocido";
        
        $data = [];
        foreach ($alumnos as $a) {
            $grupo = $conn->prepare("SELECT nombre FROM grupos WHERE id_grupo = :id");
            $grupo->bindParam(':id', $a['grupos_id_grupo'], PDO::PARAM_INT);
            $grupo->execute();
            $grupoNombre = $grupo->fetch(PDO::FETCH_ASSOC)['nombre'] ?? "Desconocido";

            $tutor = $conn->prepare("SELECT u.nombre, u.apellido_paterno, u.apellido_materno 
                                     FROM usuarios u JOIN tutores t ON u.id_usuario = t.usuarios_id_usuario
                                     WHERE t.id_tutor = :id");
            $tutor->bindParam(':id', $a['tutores_id_tutor'], PDO::PARAM_INT);
            $tutor->execute();
            $tutorData = $tutor->fetch(PDO::FETCH_ASSOC);
            $tutorNombre = $tutorData ? "{$tutorData['nombre']} {$tutorData['apellido_paterno']} {$tutorData['apellido_materno']}" : "Desconocido";

            $data[$grupoNombre][$tutorNombre][] = $a;
        }

        echo "<h2>Alumnos de la carrera: {$nombre_carrera}</h2>";
        foreach ($data as $grupo => $tutores) {
            echo "<h3 class='mt-4'>Grupo: {$grupo}</h3>";
            foreach ($tutores as $tutor => $alumnos) {
                echo "<h4>Tutor: {$tutor}</h4><ul class='list-group mb-3'>";
                foreach ($alumnos as $a) echo "<li class='list-group-item'>{$a['nombre']} {$a['apellido_paterno']} {$a['apellido_materno']}</li>";
                echo "</ul>";
            }
        }
    }

    if (!in_array($nivel, [1, 2, 4, 9])) echo "<div class='alert alert-danger'>No tiene permisos para ver esta página.</div>";
    ?>
</div>

<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
