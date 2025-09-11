<?php
// Remove session_start() as it's already started in index.php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../controllers/authController.php";
require_once __DIR__ . "/../controllers/alumnoController.php";
require_once __DIR__ . "/../controllers/asistenciaController.php";

$auth = new AuthController($conn);
$auth->checkAuth(); // Verifica que el usuario haya iniciado sesión

if (!isset($_GET['id_grupo']) || empty($_GET['id_grupo'])) {
    die("Error: No se ha especificado un grupo.");
}
$id_grupo = (int)$_GET['id_grupo'];

$fecha_consulta = $_GET['fecha'] ?? date('Y-m-d');

$alumnoController = new AlumnoController($conn);
$asistenciaController = new AsistenciaController($conn);

$alumnos_del_grupo = $alumnoController->getAlumnosByGrupo($id_grupo);
$nombre_grupo = $alumnoController->getNombreGrupo($id_grupo) ?? 'Grupo no encontrado';

$asistencias_guardadas = $asistenciaController->asistencia->getAsistenciaPorFechaYGrupo($fecha_consulta, $id_grupo);

$alumnos_presentes_ids = [];
foreach ($asistencias_guardadas as $id_alumno => $estatus) {
    if ($estatus === 1) {
        $alumnos_presentes_ids[] = $id_alumno;
    }
}


$page_title = "Toma de Asistencia - " . htmlspecialchars($nombre_grupo);
include 'objects/header.php';


?>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Toma de Asistencia</h1>
            <button class="btn btn-secondary" 
                    onclick="window.history.back();" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Volver a la Página Anterior">
                ← 
            </button>
        </div>
        <div class="card-body">
            <h2 class="card-title text-primary"><?= htmlspecialchars($nombre_grupo) ?></h2>
            <p class="card-subtitle mb-2 text-muted">Fecha: <?= htmlspecialchars(date('d/m/Y', strtotime($fecha_consulta))) ?></p>
            <hr>

            <?php if (empty($alumnos_del_grupo)): ?>
                <div class="alert alert-warning">No se encontraron alumnos en este grupo.</div>
            <?php else: ?>
                <form id="asistenciaForm">
                    <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha_consulta) ?>">
                    
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
                            <?php foreach ($alumnos_del_grupo as $alumno): 
                                $asistio = in_array($alumno['id_alumno'], $alumnos_presentes_ids);
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($alumno['matricula']) ?></td>
                                    <td><?= htmlspecialchars($alumno['nombre_completo']) ?></td>
                                    <td class="text-center">
                                        <input class="form-check-input asistencia-checkbox" type="checkbox" name="asistencias[]" value="<?= htmlspecialchars($alumno['id_alumno']) ?>" <?= $asistio ? 'checked' : '' ?>>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="text-end mt-4">
                        <button type="submit" 
                                class="btn btn-primary btn-lg" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="Guardar la Asistencia de los Alumnos">
                            <i class="bi bi-check-circle me-2"></i>
                            Guardar Asistencia
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Sincronizar el checkbox todos al cargar la página
    const toggleAll = document.getElementById('toggleAllCheckbox');
    const asistenciaCheckboxes = document.querySelectorAll('.asistencia-checkbox');

    function updateToggleAll() {
        const allChecked = Array.from(asistenciaCheckboxes).every(c => c.checked);
        toggleAll.checked = allChecked;
    }

    updateToggleAll();

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
    toggleAll.addEventListener('change', () => {
        asistenciaCheckboxes.forEach(chk => chk.checked = toggleAll.checked);
    });

    // Actualizar el checkbox "Todos" si se marcan/desmarcan manualmente
    asistenciaCheckboxes.forEach(chk => {
        chk.addEventListener('change', updateToggleAll);
    });
});
</script>

<?php include 'objects/footer.php'; ?>