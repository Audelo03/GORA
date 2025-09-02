<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../controllers/authController.php";
require_once __DIR__ . "/../controllers/seguimientoController.php";

$page_title = "Seguimientos";
include 'objects/header.php';
include 'objects/navbar.php';

$auth = new AuthController($conn);
$auth->checkAuth();

$id_usuario_actual = $_SESSION['usuario_id'];
$id_nivel_usuario = $_SESSION['usuario_nivel'];

$seguimientoController = new SeguimientoController($conn);
$seguimientos = $seguimientoController->obtenerSeguimientosPorRol($id_usuario_actual, $id_nivel_usuario);

function getEstatusBadge(int $estatus): string {
    switch ($estatus) {
        case 1: return '<span class="badge bg-success">Abierto</span>';
        case 2: return '<span class="badge bg-warning text-dark">En Progreso</span>';
        case 3: return '<span class="badge bg-secondary">Cerrado</span>';
        default: return '<span class="badge bg-light text-dark">Desconocido</span>';
    }
}

function formatFecha(?string $fecha): string {
    if (empty($fecha)) return '<span class="text-muted">No definida</span>';
    return date("d/m/Y", strtotime($fecha));
}
?>

<div class="container py-5">


    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($seguimientos)): ?>
                <div class="text-center p-4">
                    <p class="mb-0">No hay seguimientos para mostrar.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="tabla-seguimientos" class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Alumno</th>
                                <th>Matrícula</th>
                                <th>Carrera</th>
                                <th>Tipo</th>
                                <th>Estatus</th>
                                <th>Fecha Creación</th>
                                <th>Tutor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($seguimientos as $s): ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['nombre_alumno']) ?></td>
                                    <td><?= htmlspecialchars($s['matricula']) ?></td>
                                    <td><?= htmlspecialchars($s['nombre_carrera']) ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?= htmlspecialchars($s['tipo_seguimiento'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td><?= getEstatusBadge((int)$s['estatus']) ?></td>
                                    <td><?= formatFecha($s['fecha_creacion']) ?></td>
                                    <td><?= htmlspecialchars($s['nombre_tutor']) ?></td>
                                    <td>
                                        <a href="editar_seguimiento.php?id_seguimiento=<?= $s['id_seguimiento'] ?>" 
                                           class="btn btn-sm btn-outline-warning" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Editar este Seguimiento">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="ver_seguimientos.php?id_alumno=<?= $s['id_alumno'] ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Ver Seguimientos del Alumno">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'objects/footer.php'; ?>

<script>
     $(document).ready(function() {
 $('#tabla-seguimientos').DataTable({
         dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>' +
                 '<"row"<"col-sm-12"B>>',
            
            // Definición de los botones que se mostrarán
            buttons: [
                { extend: 'copyHtml5', text: 'Copiar', className: 'btn-secondary' },
                { extend: 'csvHtml5', text: 'CSV', className: 'btn-secondary' },
                { extend: 'excelHtml5', text: 'Excel', className: 'btn-secondary' },
                { extend: 'pdfHtml5', text: 'PDF', className: 'btn-secondary' },
                { extend: 'print', text: 'Imprimir', className: 'btn-secondary' }
            ],
        });});
</script>