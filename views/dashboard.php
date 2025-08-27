<?php
require_once __DIR__ . '/../controllers/alumnoController.php';
require_once __DIR__ . '/../controllers/authController.php';

$auth = new AuthController($conn);
$auth->checkAuth();

$usuario_id = $_SESSION['usuario_id'];
$nivel = $_SESSION['usuario_nivel'];

$alumnoController = new AlumnoController($conn);

function obtenerAlumnosParaTutorLvl4($alumnoController, $conn, $usuario_id) {
    
            $alumnos = $alumnoController->alumno->getByTutorId($usuario_id);
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
        
}

function obtenerAlumnosParaAdminLv1($alumnoController, $conn) {
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

    function obtenerAlumnosParaCoordinadorLvl2($alumnoController, $conn, $auth, $usuario_id) {
        $carreraid = $auth->usuario->getCarrreraIdByUsuarioId($usuario_id);
        $alumnos = $alumnoController->alumno->listByCarreraId($carreraid);
        $nombre_carrera = $conn->prepare("SELECT nombre FROM carreras WHERE id_carrera = :id");
        $nombre_carrera->bindParam(':id', $carreraid, PDO::PARAM_INT);
        $nombre_carrera->execute();
        $nombre_carrera = $nombre_carrera->fetch(PDO::FETCH_ASSOC)['nombre'] ?? "Desconocido";
        
        $data = [];
        foreach ($alumnos as $a) {
            $grupo = $conn->prepare("SELECT nombre, usuarios_id_usuario_tutor FROM grupos WHERE id_grupo = :id");
            $grupo->bindParam(':id', $a['grupos_id_grupo'], PDO::PARAM_INT);
            $grupo->execute();
            $grupoData = $grupo->fetch(PDO::FETCH_ASSOC);
            $grupoNombre = $grupoData ? $grupoData['nombre'] : "Desconocido";
            $grupoTutorId = $grupoData ? $grupoData['usuarios_id_usuario_tutor'] : null;

            $tutor  = $conn->prepare("SELECT nombre, apellido_paterno, apellido_materno FROM usuarios WHERE id_usuario = :id");
            $tutor->bindParam(':id', $grupoTutorId, PDO::PARAM_INT);
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
        obtenerAlumnosParaTutorLvl4($alumnoController, $conn, $usuario_id); 
    }

    if ($nivel == 1 || $nivel == 9) {
        obtenerAlumnosParaAdminLv1($alumnoController, $conn);
    }

    if ($nivel == 2) {
        obtenerAlumnosParaCoordinadorLvl2($alumnoController, $conn, $auth, $usuario_id);
    }

    if (!in_array($nivel, [1, 2, 4, 9])) echo "<div class='alert alert-danger'>No tiene permisos para ver esta página.</div>";
    ?>
</div>

<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
