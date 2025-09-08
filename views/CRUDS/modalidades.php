<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";
$auth = new AuthController($conn);
$auth->checkAuth();
$modificacion_ruta = "../";
$page_title = "Modalidades";
include "../objects/header.php";
?>

<div class="container mt-4">
    <button class="btn btn-success mb-3" id="btnNuevaModalidad">
        <i class="bi bi-plus-circle"></i> Agregar Modalidad
    </button>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="modalidadesBody">
                </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalidadModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Modalidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formModalidad">
                    <input type="hidden" id="id_modalidad" name="id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Modalidad</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
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

<?php
include "../objects/footer.php";

?>
<script>
$(document).ready(function() {
    const modalidadModal = new bootstrap.Modal(document.getElementById('modalidadModal'));

    function cargarModalidades() {
        $.get("../../controllers/modalidadesController.php?action=index", function(data) {
            let rows = "";
            if (data && data.length > 0) {
                data.forEach(m => {
                    rows += `<tr>
                        <td>${m.id_modalidad}</td>
                        <td>${m.nombre}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-editar" data-modalidad='${JSON.stringify(m)}' title="Editar"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="${m.id_modalidad}" title="Eliminar"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                });
            } else {
                rows = `<tr><td colspan="3" class="text-center">No hay modalidades registradas.</td></tr>`;
            }
            $("#modalidadesBody").html(rows);
        }).fail(function() {
            // ** MEJORA: Manejo de errores si la petición AJAX falla **
            $("#modalidadesBody").html('<tr><td colspan="3" class="text-center">Error al cargar los datos.</td></tr>');
            Swal.fire({
                icon: 'error',
                title: 'Error de Carga',
                text: 'No se pudieron cargar los datos de las modalidades.'
            });
        });
    }

    $('#btnNuevaModalidad').click(function() {
        $('#formModalidad')[0].reset();
        $('#id_modalidad').val(''); // Asegurarse que el ID esté vacío
        $('#modalLabel').text('Agregar Modalidad');
        modalidadModal.show();
    });

    $('#btnGuardar').click(function() {
        let id = $("#id_modalidad").val();
        let url = id ? "../../controllers/modalidadesController.php?action=update" : "../../controllers/modalidadesController.php?action=store";

        $.post(url, $('#formModalidad').serialize())
            .done(function(response) {
                 if (response.status === 'success') {
                    modalidadModal.hide();
                    cargarModalidades();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudo guardar la modalidad.'
                    });
                }
            })
            .fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Hubo un error de comunicación con el servidor.'
                });
            });
    });

    $("#modalidadesBody").on('click', '.btn-editar', function() {
        const modalidad = $(this).data('modalidad');
        
        $("#id_modalidad").val(modalidad.id_modalidad);
        $("#nombre").val(modalidad.nombre);
        
        $('#modalLabel').text('Editar Modalidad');
        modalidadModal.show();
    });

    $("#modalidadesBody").on('click', '.btn-eliminar', function() {
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
                // ** MEJORA: Se usa 'action=delete' **
                $.post("../../controllers/modalidadesController.php?action=delete", { id: idParaEliminar }, function(response) {
                    if (response.status === 'success') {
                        Swal.fire('¡Eliminado!', response.message, 'success');
                        cargarModalidades();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }, 'json')
                .fail(function() {
                    Swal.fire('Error', 'Verifica tus realciones con otras tablas.', 'error');
                });
            }
        });
    });
    cargarModalidades();
});
</script>

