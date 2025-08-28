<?php
require_once __DIR__ . '/../controllers/alumnoController.php';
require_once __DIR__ . '/../controllers/authController.php';

$auth = new AuthController($conn);
$auth->checkAuth();

$niveles_autorizados = [1 => "Admin", 2 => "Coordinador", 4 => "Tutor", 9 => "Director"];

$usuario_id = $_SESSION['usuario_id'];
$nivel = $_SESSION['usuario_nivel'];
$nivel_nombre  = $niveles_autorizados[$nivel] ?? "Desconocido"; 

$nombre = $_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido_paterno'] . ' ' . $_SESSION['usuario_apellido_materno'];

$alumnoController = new AlumnoController($conn);

function obtenerAlumnosParaTutorLvl4($alumnoController, $conn, $usuario_id, $auth) {
    //obtener grupos del tutor
    $grupos_ids = $auth->usuario->getGruposIdByUsuarioId($usuario_id);
    if (!$grupos_ids) {
        echo "<div class='alert alert-info'>No tiene grupos asignados.</div>";
        return;
    }
    
    $alumnos = $alumnoController->alumno->getByTutorId($usuario_id);
    $grupos = [];
    foreach ($alumnos as $a) $grupos[$a['grupos_id_grupo']][] = $a;
    ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 mb-3 text-primary">👨‍🏫 Mis Alumnos</h2>
            <?php foreach ($grupos as $id_grupo => $alumnos): 
                $stmt = $conn->prepare("SELECT nombre FROM grupos WHERE id_grupo = :id_grupo");
                $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
                $stmt->execute();
                $grupo = $stmt->fetch(PDO::FETCH_ASSOC)['nombre'] ?? "Desconocido";
            ?>
                <h5 class="mt-3 text-secondary">Grupo: <?= $grupo ?></h5>
                <ul class="list-group list-group-flush mb-3">
                    <?php foreach ($alumnos as $a): ?>
                        <li class="list-group-item"><?= $a['nombre'] ?> <?= $a['apellido_paterno'] ?> <?= $a['apellido_materno'] ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function obtenerAlumnosParaAdminLv1($alumnoController, $conn) {
    $lista = $alumnoController->listarAlumnos();
    $data = [];
    foreach ($lista as $row) $data[$row['carrera']][$row['tutor']][$row['grupo']][] = $row;
    ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 mb-3 text-primary">📚 Todos los Alumnos</h2>
            <?php foreach ($data as $carrera => $tutores): ?>
                <h5 class="mt-4 text-secondary">Carrera: <?= $carrera ?></h5>
                <?php foreach ($tutores as $tutor_id => $grupos): 
                    $tutor = $conn->prepare("SELECT nombre, apellido_paterno, apellido_materno FROM usuarios WHERE id_usuario = :id");
                    $tutor->bindParam(':id', $tutor_id, PDO::PARAM_INT);
                    $tutor->execute();
                    $tutorData = $tutor->fetch(PDO::FETCH_ASSOC);
                    $tutorNombre = $tutorData ? "{$tutorData['nombre']} {$tutorData['apellido_paterno']} {$tutorData['apellido_materno']}" : "Desconocido";
                ?>
                    <h6 class="mt-3">Tutor: <?= $tutorNombre ?></h6>
                    <?php foreach ($grupos as $grupo => $alumnos): ?>
                        <div class="mb-2">
                            <span class="badge bg-info text-dark">Grupo: <?= $grupo ?></span>
                            <ul class="list-group list-group-flush mt-2">
                                <?php foreach ($alumnos as $a): ?>
                                    <li class="list-group-item"><?= $a['nombre'] ?> <?= $a['apellido_paterno'] ?> <?= $a['apellido_materno'] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
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

    if (empty($data)) {
        echo "<div class='alert alert-info'>No hay alumnos registrados en su carrera.</div>";
        return;
    }
    ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 mb-3 text-primary">🎓 Alumnos de la carrera: <?= $nombre_carrera ?></h2>
            <?php foreach ($data as $grupo => $tutores): ?>
                <h5 class="mt-4 text-secondary">Grupo: <?= $grupo ?></h5>
                <?php foreach ($tutores as $tutor => $alumnos): ?>
                    <h6 class="mt-2">Tutor: <?= $tutor ?></h6>
                    <ul class="list-group list-group-flush mb-3">
                        <?php foreach ($alumnos as $a): ?>
                            <li class="list-group-item"><?= $a['nombre'] ?> <?= $a['apellido_paterno'] ?> <?= $a['apellido_materno'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
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
                <h1 class="h3 mb-1">📊 Tabla de Alumnos</h1>
                <p class="mb-0 text-muted">Nivel: <strong><?= $nivel_nombre ?></strong></p>
                <p class="mb-0 text-muted">Usuario: <strong><?= $nombre ?></strong></p>
            </div>
            <a href="login.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </div>

    <?php
    if ($nivel == 4) {
        obtenerAlumnosParaTutorLvl4($alumnoController, $conn, $usuario_id, $auth); 
    }

    if ($nivel == 1 || $nivel == 9) {
        obtenerAlumnosParaAdminLv1($alumnoController, $conn);
    }

    if ($nivel == 2) {
        obtenerAlumnosParaCoordinadorLvl2($alumnoController, $conn, $auth, $usuario_id);
    }

    if (!in_array($nivel, [1, 2, 4, 9])): ?>
        <div class="alert alert-danger">No tiene permisos para ver esta página.</div>
    <?php endif; ?>
</div>

<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
