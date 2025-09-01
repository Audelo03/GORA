<?php
session_start();
require_once "../config/db.php";
require_once "../controllers/alumnoController.php";
require_once "../controllers/asistenciaController.php";

// Validar si es tutor
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id_grupo']) || empty($_GET['id_grupo'])) {
    die("Error: No se ha especificado un grupo.");
}
$id_grupo = $_GET['id_grupo'];

$alumnoController = new AlumnoController($conn);
$alumnos_del_grupo = $alumnoController->getAlumnosByGrupo($id_grupo);
$nombre_grupo = $alumnoController->getNombreGrupo($id_grupo) ?? 'Grupo no encontrado'; 

$page_title = "Toma de Asistencia - " . htmlspecialchars($nombre_grupo);
include 'objects/header.php';
include 'objects/navbar.php';
?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Toma de Asistencia</h1>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
        <div class="card-body">
            <h2 class="card-title text-primary"><?= htmlspecialchars($nombre_grupo) ?></h2>
            <p class="card-subtitle mb-2 text-muted">Fecha: <?= date('d/m/Y') ?></p>
            <hr>

            <?php if (empty($alumnos_del_grupo)): ?>
                <div class="alert alert-warning">No se encontraron alumnos en este grupo.</div>
            <?php else: ?>
                <form id="asistenciaForm">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Matrícula</th>
                                <th>Nombre Completo</th>
                               <th class="text-center">
    Asistencia
    <div class="d-flex justify-content-center mt-1">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="toggleAllCheckbox">
        </div>
    </div>
</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alumnos_del_grupo as $alumno): ?>
                                <tr>
                                    <td><?= htmlspecialchars($alumno['matricula']) ?></td>
                                    <td><?= htmlspecialchars($alumno['nombre_completo']) ?></td>
                                    <td class="text-center">
                                        <input class="form-check-input asistencia-checkbox" type="checkbox" name="asistencias[]" value="<?= htmlspecialchars($alumno['id_alumno']) ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Guardar Asistencia
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Guardar asistencia con fetch
document.getElementById('asistenciaForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const id_grupo = <?= json_encode($id_grupo) ?>;
    formData.append('id_grupo', id_grupo);

    try {
        const response = await fetch('guardar_asistencia.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if(result.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Asistencia guardada correctamente',
                confirmButtonColor: '#3085d6'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar asistencia: ' + result.error,
                confirmButtonColor: '#d33'
            });
        }
    } catch(err) {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor',
            confirmButtonColor: '#d33'
        });
    }
});

// Checkbox "Todos" para marcar/desmarcar todos
const toggleAll = document.getElementById('toggleAllCheckbox');
toggleAll.addEventListener('change', () => {
    const checkboxes = document.querySelectorAll('.asistencia-checkbox');
    checkboxes.forEach(chk => chk.checked = toggleAll.checked);
});

// Actualizar el checkbox "Todos" si se marcan/desmarcan manualmente
const asistenciaCheckboxes = document.querySelectorAll('.asistencia-checkbox');
asistenciaCheckboxes.forEach(chk => {
    chk.addEventListener('change', () => {
        const allChecked = Array.from(asistenciaCheckboxes).every(c => c.checked);
        toggleAll.checked = allChecked;
    });
});
</script>

<?php include 'objects/footer.php'; ?>
