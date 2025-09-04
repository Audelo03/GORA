<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";
$auth = new AuthController($conn);
$auth->checkAuth();
$modificacion_ruta= "../";
include "../objects/header.php";
?>

    <div class="container">
        <h1 class="mb-4">Modalidades</h1>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalidadModal" id="btnNuevo">
            Agregar Modalidad
        </button>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="modalidadesBody"></tbody>
        </table>
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
        const modal = new bootstrap.Modal(document.getElementById('modalidadModal'));

        function cargarModalidades() {
            $.get("../../controllers/modalidadesController.php?accion=listar", function(data) {
                let rows = "";
                data.forEach(m => {
                    rows += `<tr>
                        <td>${m.id_modalidad}</td>
                        <td>${m.nombre}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-editar" data-id="${m.id_modalidad}" data-nombre="${m.nombre}">Editar</button>
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="${m.id_modalidad}">Eliminar</button>
                        </td>
                    </tr>`;
                });
                $("#modalidadesBody").html(rows);
            });
        }

        $("#btnNuevo").on('click', function() {
            $("#formModalidad")[0].reset();
            $("#id_modalidad").val('');
            $("#modalLabel").text("Agregar Modalidad");
        });

        $("#btnGuardar").on('click', function() {
            const id = $("#id_modalidad").val();
            const data = { id: id, nombre: $("#nombre").val() };
            const url = id ? `../../controllers/modalidadesController.php?accion=actualizar` : `../../controllers/modalidadesController.php?accion=crear`;
            
            $.post(url, data, function(response) {
                modal.hide();
                cargarModalidades();
            }).fail(function() {
                alert("Error al guardar. Asegúrate de que el controlador tenga las acciones 'crear' y 'actualizar'.");
            });
        });

        $("#modalidadesBody").on('click', '.btn-editar', function() {
            $("#id_modalidad").val($(this).data('id'));
            $("#nombre").val($(this).data('nombre'));
            $("#modalLabel").text("Editar Modalidad");
            modal.show();
        });

        $("#modalidadesBody").on('click', '.btn-eliminar', function() {
            if (confirm("¿Seguro que deseas eliminar?")) {
                $.post(`../../controllers/modalidadesController.php?accion=eliminar`, { id: $(this).data('id') }, function(response) {
                    cargarModalidades();
                }).fail(function() {
                    alert("Error al eliminar. Asegúrate de que el controlador tenga la acción 'eliminar'.");
                });
            }
        });

        cargarModalidades();
    });
    </script>
<?php include "../objects/footer.php";?>