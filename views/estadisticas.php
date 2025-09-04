<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/estadisticasController.php';

$is_component_mode = isset($_GET['modo']) && $_GET['modo'] === 'componente';

if (!$is_component_mode) {
    $auth = new AuthController($conn);
    $auth->checkAuth();
    $page_title = 'Estadísticas';
    include 'objects/header.php';
    
}

$estadisticasController = new EstadisticasController($conn);
$datos = $estadisticasController->obtenerEstadisticas();
?>

<div class="container mt-4">

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total de Alumnos</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $datos['total_alumnos']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total de Carreras</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $datos['total_carreras']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Total de Grupos</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $datos['total_grupos']; ?></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
             <div class="card text-white bg-dark mb-3">
                <div class="card-header">Tasa de Asistencia General</div>
                <div class="card-body">
                    <h4 class="card-title"><?php echo $datos['tasa_asistencia']; ?>%</h4>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $datos['tasa_asistencia']; ?>%;" aria-valuenow="<?php echo $datos['tasa_asistencia']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Seguimientos por Estatus</div>
                <div class="card-body">
                    <canvas id="seguimientosPorEstatusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Seguimientos por Tipo</div>
                <div class="card-body">
                    <canvas id="seguimientosPorTipoChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Distribución de Alumnos por Estatus</div>
                <div class="card-body">
                    <canvas id="alumnosPorEstatusChart"></canvas>
                </div>
            </div>
        </div>
 
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script id="estadisticas-script">
const alumnosPorCarrera = <?php echo json_encode($datos['alumnos_por_carrera']); ?>;
const alumnosPorEstatus = <?php echo json_encode($datos['alumnos_por_estatus']); ?>;
const gruposPorModalidad = <?php echo json_encode($datos['grupos_por_modalidad']); ?>;
const seguimientosPorEstatus = <?php echo json_encode($datos['seguimientos_por_estatus']); ?>;
const seguimientosPorTipo = <?php echo json_encode($datos['seguimientos_por_tipo']); ?>;



// --- GRÁFICA 2: Alumnos por Estatus (Nueva - Pastel) ---
new Chart(document.getElementById('alumnosPorEstatusChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: alumnosPorEstatus.map(item => item.estatus_nombre),
        datasets: [{
            data: alumnosPorEstatus.map(item => item.total),
            backgroundColor: ['rgba(40, 167, 69, 0.7)', 'rgba(220, 53, 69, 0.7)', 'rgba(255, 193, 7, 0.7)', 'rgba(108, 117, 125, 0.7)'],
            borderColor: ['#fff'],
            borderWidth: 2
        }]
    }
});



// --- GRÁFICA 4: Seguimientos por Estatus (Dona) ---
new Chart(document.getElementById('seguimientosPorEstatusChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: seguimientosPorEstatus.map(item => item.estatus_nombre),
        datasets: [{
            data: seguimientosPorEstatus.map(item => item.total),
            backgroundColor: ['rgba(255, 99, 132, 0.7)', 'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)'],
            borderColor: ['#fff'],
            borderWidth: 2
        }]
    }
});

// --- GRÁFICA 5: Seguimientos por Tipo (Barra polar) ---
new Chart(document.getElementById('seguimientosPorTipoChart').getContext('2d'), {
    type: 'polarArea',
    data: {
        labels: seguimientosPorTipo.map(item => item.nombre),
        datasets: [{
            label: 'Número de Seguimientos',
            data: seguimientosPorTipo.map(item => item.total),
            backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)'],
        }]
    }
});



</script>

<?php
if (!$is_component_mode) {
    require_once 'objects/footer.php';
}
?>