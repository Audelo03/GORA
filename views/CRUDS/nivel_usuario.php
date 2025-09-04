<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";
$auth = new AuthController($conn);
$auth->checkAuth();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Niveles de Usuario</title>
    <link rel="stylesheet" href="../../vendor/bootstrap/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h1 class="mb-4">Niveles de Usuario</h1>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#nivelModal" id="btnNuevo">
            Agregar Nivel
        </button>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="nivelesBody"></tbody>
        </table>
    </div>

    <div class="modal fade" id="nivelModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Formulario de Nivel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNivel">
                        <input type="hidden" id="id_nivel_usuario" name="id">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Nivel</label>
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
        const modal = new bootstrap.Modal(document.getElementById('nivelModal'));

        function cargarNiveles() {
            // NOTA: Tu controlador necesita una acción 'listar'.
            $.get("../../controllers/nivelesusuariosController.php?accion=listar", function(data) {
                let rows = "";
                data.forEach(n => {
                    rows += `<tr>
                        <td>${n.id_nivel_usuario}</td>
                        <td>${n.nombre}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-editar" data-id="${n.id_nivel_usuario}" data-nombre="${n.nombre}">Editar</button>
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="${n.id_nivel_usuario}">Eliminar</button>
                        </td>
                    </tr>`;
                });
                $("#nivelesBody").html(rows);
            });
        }

        $("#btnNuevo").on('click', function() {
            $("#formNivel")[0].reset();
            $("#id_nivel_usuario").val('');
            $("#modalLabel").text("Agregar Nivel de Usuario");
        });

        $("#btnGuardar").on('click', function() {
            const id = $("#id_nivel_usuario").val();
            const data = { id: id, nombre: $("#nombre").val() };
            // NOTA: Necesitarás acciones 'crear' y 'actualizar' en tu controlador de niveles.
            const url = id ? `../../controllers/nivelesusuariosController.php?accion=actualizar` : `../../controllers/nivelesusuariosController.php?accion=crear`;
            
            $.post(url, data, function(response) {
                modal.hide();
                cargarNiveles();
            }).fail(function() {
                alert("Error al guardar. Asegúrate de que el controlador tenga las acciones necesarias.");
            });
        });

        $("#nivelesBody").on('click', '.btn-editar', function() {
            $("#id_nivel_usuario").val($(this).data('id'));
            $("#nombre").val($(this).data('nombre'));
            $("#modalLabel").text("Editar Nivel de Usuario");
            modal.show();
        });

        $("#nivelesBody").on('click', '.btn-eliminar', function() {
            if (confirm("¿Seguro que deseas eliminar este nivel?")) {
                // NOTA: Necesitarás una acción 'eliminar' en tu controlador de niveles.
                $.post(`../../controllers/nivelesusuariosController.php?accion=eliminar`, { id: $(this).data('id') }, function() {
                    cargarNiveles();
                }).fail(function() {
                     alert("Error al eliminar. Asegúrate de que el controlador tenga la acción 'eliminar'.");
                });
            }
        });

        cargarNiveles();
    });
    </script>
</body>
</html>