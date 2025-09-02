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
<style>

#tabla-seguimientos {
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

/* Encabezado con estilo */
#tabla-seguimientos thead th {
    background: #f8f9fa;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
}


#tabla-seguimientos tbody tr:hover {
    background: #f1f3f5;
    cursor: pointer;
    transition: background 0.2s ease-in-out;
}


#tabla-seguimientos td:last-child,
#tabla-seguimientos td .badge {
    text-align: center;
    vertical-align: middle;
}

</style>
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
        dom: `
            <'row mb-3'
                <'col-sm-12 col-md-6'l>
                <'col-sm-12 col-md-6 dt-action-buttons d-flex justify-content-md-end gap-2'Bf>
            >
            <'row'
                <'col-sm-12 table-responsive'tr>
            >
            <'row mt-3'
                <'col-sm-12 col-md-5'i>
                <'col-sm-12 col-md-7'p>
            >
        `,
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-excel-fill"></i> Excel',
                className: 'btn btn-success btn-sm shadow-sm',
                titleAttr: 'Exportar a Excel'
            }
        ],
        language: {
            search: "",
            searchPlaceholder: "Buscar seguimiento...",
            lengthMenu: "Mostrar _MENU_",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            paginate: {
                first: "«",
                last: "»",
                next: "›",
                previous: "‹"
            }
        }
    });

    // Input de búsqueda más compacto y moderno
    $('.dataTables_filter input')
        .addClass('form-control form-control-sm shadow-sm')
        .css('width', '250px');
});

</script>