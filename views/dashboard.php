<?php
session_start();
require_once "../config/db.php"; // Conexión PDO
require_once "../controllers/usuarioController.php";
require_once "../controllers/alumnoController.php";
$auth = new UsuarioController();
$alumnoController = new AlumnoController();

// Identificar tipo de usuario
$usuario = $_SESSION['usuario']; // Ejemplo: array con ['id','nivel','nombre']
$nivel = $usuario['nivel'];

// Obtener datos según nivel
switch($nivel){
    case 1: // Admin
        $stats = $alumnoController->getStatsAll();
        break;
    case 2: // Coordinador
        $stats = $alumnoController->getStatsByCarrera($usuario['id_carrera']);
        break;
    case 3: // Tutor
        $stats = $alumnoController->getStatsByTutor($usuario['id']);
        break;
    case 4: // Director
        $stats = $alumnoController->getStatsAllSummary();
        break;
    default:
        $stats = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container py-4">
    <h1 class="mb-4 text-primary">Dashboard de Asistencia</h1>

    <!-- Cards con métricas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Alumnos</h5>
                    <p class="card-text fs-3"><?= $stats['total_alumnos'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Asistencia Promedio</h5>
                    <p class="card-text fs-3"><?= $stats['promedio_asistencia'] ?? 0 ?>%</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Grupos Activos</h5>
                    <p class="card-text fs-3"><?= $stats['total_grupos'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Baja Asistencia</h5>
                    <p class="card-text fs-3"><?= $stats['grupos_baja_asistencia'] ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de asistencia -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Asistencia por grupo</h5>
            <canvas id="asistenciaChart"></canvas>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('asistenciaChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($stats['grupos_nombres'] ?? []) ?>,
        datasets: [{
            label: 'Asistencia (%)',
            data: <?= json_encode($stats['grupos_asistencia'] ?? []) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true, max: 100 }
        }
    }
});
</script>
</body>
</html>
