<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";
$auth = new AuthController($conn);
$auth->checkAuth();
$modificacion_ruta= "../";
include "../objects/header.php";
?>

    <div class="container">
        <h1 class="mb-4">Tipos de Seguimiento</h1>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#tipoSeguimientoModal" id="btnNuevo">
            Agregar Tipo
        </button>

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
    <script>
    $(document).ready(function() {
        const modal = new bootstrap.Modal(document.getElementById('tipoSeguimientoModal'));

        function cargarTipos() {
            // NOTA: Tu controlador necesita una acción 'listar'.
            $.get("../../controllers/tipoSeguimientoController.php?accion=listar", function(data) {
                let rows = "";
                data.forEach(t => {
                    rows += `<tr>
                        <td>${t.id_tipo_seguimiento}</td>
                        <td>${t.nombre}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-editar" data-id="${t.id_tipo_seguimiento}" data-nombre="${t.nombre}">Editar</button>
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="${t.id_tipo_seguimiento}">Eliminar</button>
                        </td>
                    </tr>`;
                });
                $("#tiposBody").html(rows);
            });
        }

        $("#btnNuevo").on('click', function() {
            $("#formTipo")[0].reset();
            $("#id_tipo_seguimiento").val('');
            $("#modalLabel").text("Agregar Tipo de Seguimiento");
        });

        $("#btnGuardar").on('click', function() {
            const id = $("#id_tipo_seguimiento").val();
            const data = { id: id, nombre: $("#nombre").val() };
            // NOTA: Necesitarás acciones 'crear' y 'actualizar' en tu controlador.
            const url = id ? `../../controllers/tipoSeguimientoController.php?accion=actualizar` : `../../controllers/tipoSeguimientoController.php?accion=crear`;
            
            $.post(url, data, function(response) {
                modal.hide();
                cargarTipos();
            }).fail(function() {
                alert("Error al guardar. Asegúrate de que el controlador tenga las acciones 'crear' y 'actualizar'.");
            });
        });

        $("#tiposBody").on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');
            
            $("#id_tipo_seguimiento").val(id);
            $("#nombre").val(nombre);
            $("#modalLabel").text("Editar Tipo de Seguimiento");
            modal.show();
        });

        $("#tiposBody").on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');
            if (confirm("¿Seguro que deseas eliminar este registro?")) {
                // NOTA: Necesitarás una acción 'eliminar' en tu controlador.
                $.post(`../../controllers/tipoSeguimientoController.php?accion=eliminar`, { id: id }, function(response) {
                    cargarTipos();
                }).fail(function() {
                    alert("Error al eliminar. Asegúrate de que el controlador tenga la acción 'eliminar'.");
                });
            }
        });

        cargarTipos();
    });
    </script>
<?php include "../objects/footer.php";?>