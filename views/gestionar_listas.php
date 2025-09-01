<?php
session_start();
require_once __DIR__ . '/../controllers/authController.php';
require_once __DIR__ . '/../controllers/alumnoController.php';
require_once __DIR__ . '/../controllers/asistenciaController.php';
require_once __DIR__ . '/../config/db.php';

$auth = new AuthController($conn);
$auth->checkAuth();

$id_grupo = isset($_GET['id_grupo']) ? (int)$_GET['id_grupo'] : 0;
if ($id_grupo === 0) {
    header('Location: dashboard.php');
    exit();
}
$fecha = date('Y-m-d'); 
$alumnoController = new AlumnoController($conn);
$asistenciaController = new AsistenciaController($conn);

$nombre_grupo = $alumnoController->getNombreGrupo($id_grupo);
$historial = $asistenciaController->asistencia->getHistorialAsistenciaPorGrupo($id_grupo);

$page_title = "Gestionar Asistencias: " . htmlspecialchars($nombre_grupo);
include 'objects/header.php';
include "objects/navbar.php";
?>

<div class="container mt-5">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3 mb-1">Gestionar Asistencias del Grupo</h1>
            <h2 class="h5 text-muted"><?= htmlspecialchars($nombre_grupo) ?></h2>
        </div>
    </div>
    <div class="card shadow-sm mb-4">
    <div class="card-body text-center">
        <a href="asistencia.php?id_grupo=<?= htmlspecialchars($id_grupo) ?>&fecha=<?= urlencode($fecha) ?>" 
           class="btn btn-primary btn-lg">
            <i class="bi bi-calendar-plus-fill me-2"></i>
            Pasar lista / Editar la de hoy (<?= date('d/m/Y') ?>)
        </a>
    </div>
</div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="h5 mb-0"><i class="bi bi-clock-history me-2"></i> Historial de Asistencias</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($historial)): ?>
                <div class="accordion" id="accordionHistorial">
                    <?php foreach ($historial as $fecha => $asistencias): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-<?= $fecha ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $fecha ?>" aria-expanded="false" aria-controls="collapse-<?= $fecha ?>">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    <strong>Fecha:</strong>&nbsp;<?= htmlspecialchars(date("d/m/Y", strtotime($fecha))) ?>
                                </button>
                            </h2>
                           <div id="collapse-<?= $fecha ?>" 
     class="accordion-collapse collapse" 
     aria-labelledby="heading-<?= $fecha ?>" 
     data-bs-parent="#accordionHistorial">

    <div class="accordion-body">

        <!-- Botón arriba a la derecha -->
        <div class="d-flex justify-content-end mb-3">
            <a href="asistencia.php?id_grupo=<?= $id_grupo ?>&fecha=<?= $fecha ?>" 
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-pencil-fill me-1"></i> Editar esta lista
            </a>
        </div>

        <!-- Lista de alumnos -->
        <ul class="list-group">
            <?php foreach ($asistencias as $asistencia): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($asistencia['nombre_completo']) ?>
                    <?php
                        $estatus = $asistencia['estatus'];
                        $badge_class = 'bg-secondary';
                        if ($estatus === 1) $badge_class = 'bg-success';
                        if ($estatus === 0) $badge_class = 'bg-danger';
                    ?>
                    <span class="badge <?= $badge_class ?>"><?= "  " ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>
</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No hay registros de asistencia para este grupo.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include 'objects/footer.php';
?>