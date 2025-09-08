<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();

$coordinadores = $conn->query("SELECT id_usuario, CONCAT(nombre, ' ', apellido_paterno) as nombre_completo FROM usuarios WHERE niveles_usuarios_id_nivel_usuario = 2 ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC); 
$modificacion_ruta= "../";
include "../objects/header.php";
?>

<div class="container mt-4">
    <h2 class="mb-4">Gestión de Carreras</h2>
    <button class="btn btn-success mb-3" id="btnNuevaCarrera">
        <i class="bi bi-plus-circle"></i> Agregar Carrera
    </button>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="tablaCarreras">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Coordinador</th>
                    <th>Fecha De Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="carreraModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Carrera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCarrera">
                    <input type="hidden" id="id_carrera" name="id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Carrera</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="coordinador" class="form-label">Coordinador</label>
                        <select id="coordinador_id" name="usuario_id" class="form-select" required>
                            <option value="">Seleccione un coordinador</option>
                            <?php foreach ($coordinadores as $coordinador): ?>
                                <option value="<?= $coordinador['id_usuario'] ?>"><?= htmlspecialchars($coordinador['nombre_completo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardar">Guardar</button>
            </div>
        </div>
    </div>
</div>
<?php include "../objects/footer.php";?>

<script>
$(document).ready(function() {
    const carreraModal = new bootstrap.Modal(document.getElementById('carreraModal'));

    function cargarCarreras() {
        $.get("../../controllers/carrerasController.php?action=index", function(data) {
            let carreras = JSON.parse(data);
            let rows = "";
            if(carreras.length > 0) {
                carreras.forEach(c => {
                    const coordinadorNombre = c.coordinador_nombre ? `${c.coordinador_nombre} ${c.coordinador_apellido_paterno}` : 'N/A';
                    rows += `<tr>
                        <td>${c.id_carrera}</td>
                        <td>${c.nombre}</td>
                        <td>${coordinadorNombre}</td>
                        <td>${c.fecha_creacion}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-editar" data-id='${JSON.stringify(c)}'><i class="bi bi-pencil-square"></i> </button>
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="${c.id_carrera}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                });
            } else {
                 rows = `<tr><td colspan="5" class="text-center">No hay carreras registradas.</td></tr>`;
            }
            $("#tablaCarreras tbody").html(rows);
        }).fail(function() {
            // SweetAlert para error al cargar
            Swal.fire({
                icon: 'error',
                title: 'Error de Carga',
                text: 'No se pudieron cargar los datos de las carreras.'
            });
            $("#tablaCarreras tbody").html('<tr><td colspan="5" class="text-center">Error al cargar los datos.</td></tr>');
        });
    }

    $('#btnNuevaCarrera').click(function() {
        $('#formCarrera')[0].reset();
        $('#id_carrera').val('');
        $('#modalLabel').text('Agregar Carrera');
        carreraModal.show();
    });

    $('#btnGuardar').click(function() {
        let id = $("#id_carrera").val();
        let url = id ? "../../controllers/carrerasController.php?action=update" : "../../controllers/carrerasController.php?action=store";
        
        $.post(url, $('#formCarrera').serialize(), function() {
            cargarCarreras();
            carreraModal.hide();
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'La carrera se ha guardado correctamente.',
                timer: 1500,
                showConfirmButton: false
            });
        }).fail(function() {
             Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Hubo un error al guardar la carrera.'
            });
        });
    });

    $("#tablaCarreras").on('click', '.btn-editar', function() {
        const carrera = $(this).data('id');
        $("#id_carrera").val(carrera.id_carrera);
        $("#nombre").val(carrera.nombre);
        $("#coordinador_id").val(carrera.coordinador_id);
        $('#modalLabel').text('Editar Carrera');
        carreraModal.show();
    });

    $("#tablaCarreras").on('click', '.btn-eliminar', function() {
    const idParaEliminar = $(this).data('id');
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, ¡eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../controllers/carrerasController.php?action=delete", { id: idParaEliminar }, function(response) {
                if (response.status === 'success') {
                    Swal.fire(
                        '¡Eliminada!',
                        response.message,
                        'success'
                    );
                    cargarCarreras();
                } else {
                    Swal.fire(
                        'Error',
                        response.message, 
                        'error'
                    );
                }
            }, 'json') 
            .fail(function() {
                Swal.fire(
                    'Error de Conexión',
                    'No se pudo comunicar con el servidor. Inténtalo de nuevo.',
                    'error'
                );
            });
        }
    });
});

    cargarCarreras();
});
</script>

<?php include "../objects/footer.php";?>