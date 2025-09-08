<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";
$auth = new AuthController($conn);
$auth->checkAuth();
$modificacion_ruta = "../";
include "../objects/header.php";
?>

    <div class="container">
        <h1 class="mb-4">Tipos de Seguimiento</h1>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#tipoSeguimientoModal" id="btnNuevo">
            <i class="bi bi-plus-circle"></i> Agregar Tipo
        </button>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tiposBody"></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="tipoSeguimientoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Formulario de Tipo de Seguimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formTipo">
                        <input type="hidden" id="id_tipo_seguimiento" name="id">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
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

    <script src="../../node_modules/jquery/dist/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        const modal = new bootstrap.Modal(document.getElementById('tipoSeguimientoModal'));

        function cargarTipos() {
            $.get("../../controllers/tipoSeguimientoController.php?action=index", function(data) {
                const tipos = data;
                let rows = "";
                if (tipos.length === 0) {
                    rows = '<tr><td colspan="3" class="text-center">No hay tipos de seguimiento registrados.</td></tr>';
                } else {
                    tipos.forEach(t => {
                        rows += `<tr>
                            <td>${t.id_tipo_seguimiento}</td>
                            <td>${t.nombre}</td>
                            <td>
                                <button class="btn btn-warning btn-sm btn-editar" data-id="${t.id_tipo_seguimiento}" title="Editar"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-danger btn-sm btn-eliminar" data-id="${t.id_tipo_seguimiento}" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                            </td>
                        </tr>`;
                    });
                }
                $("#tiposBody").html(rows);
            }).fail(function() {
                $("#tiposBody").html('<tr><td colspan="3" class="text-center">Error al cargar los datos. Por favor, intente de nuevo.</td></tr>');
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Carga',
                    text: 'No se pudieron cargar los datos desde el servidor.'
                });
            });
        }

        $("#btnNuevo").on('click', function() {
            $("#formTipo")[0].reset();
            $("#id_tipo_seguimiento").val('');
            $("#modalLabel").text("Agregar Tipo de Seguimiento");
            modal.show();
        });

        $("#btnGuardar").on('click', function() {
            const miModal = document.getElementById('tipoSeguimientoModal');
                
            const id = $("#id_tipo_seguimiento").val();
            const url = id ? `../../controllers/tipoSeguimientoController.php?action=update` : `../../controllers/tipoSeguimientoController.php?action=store`;
            const data = $('#formTipo').serialize();

            $.post(url, data).done(function(response) {
                cargarTipos();
                Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    text: 'El registro ha sido guardado correctamente.',
                    timer: 1500,
                    showConfirmButton: false
                });
                

            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error al guardar. Revise los datos e intente de nuevo.'
                });
            });
        });

        $("#tiposBody").on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            $.get(`../../controllers/tipoSeguimientoController.php?action=show&id=${id}`, function(data) {
                console.log(data);
                const tipo = (data);
                $("#id_tipo_seguimiento").val(tipo.id_tipo_seguimiento);
                $("#nombre").val(tipo.nombre);
                $("#modalLabel").text("Editar Tipo de Seguimiento");
                modal.show();
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo obtener la información del registro.'
                });
            });
        });

        $("#tiposBody").on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, ¡elimínalo!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`../../controllers/tipoSeguimientoController.php?action=delete`, { id: id }, function(response) {
                        Swal.fire(
                            '¡Eliminado!',
                            'El registro ha sido eliminado.',
                            'success'
                        );
                        cargarTipos();
                    }).fail(function() {
                        Swal.fire(
                            'Error',
                            'No se pudo eliminar el registro.',
                            'error'
                        );
                    });
                }
            });
        });

        cargarTipos();
    });
    </script>
<?php include "../objects/footer.php"; ?>