<?php
session_start();
require_once "../config/db.php";
require_once "../controllers/usuarioController.php";
require_once "../controllers/alumnoController.php";

$auth = new UsuarioController();
$alumnoController = new AlumnoController();

// Identificar tipo de usuario
$usuario = $_SESSION['usuario'];
$nivel = $usuario['nivel'];

// Obtener datos según nivel
// (Tu lógica de switch para obtener $stats va aquí...)
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


// Establece el título de la página
$page_title = "Dashboard de Asistencia";

// Incluye el header
include 'objects/header.php';

// Incluye la navbar
include 'objects/navbar.php';
?>

<div class="container-fluid">
    <h1 class="mb-4 text-primary">Resumen de Asistencia</h1>

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

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Asistencia por grupo</h5>
            <canvas id="asistenciaChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Tu script de Chart.js va aquí...
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

<?php
// Incluye el footer
include 'objects/footer.php';
?>