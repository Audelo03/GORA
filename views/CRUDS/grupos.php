<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php"; // Para obtener datos para los selects

$auth = new AuthController($conn);
$auth->checkAuth();

// Cargar datos para los selects del formulario
$carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$tutores = $conn->query("SELECT id_usuario, CONCAT(nombre, ' ', apellido_paterno) as nombre_completo FROM usuarios WHERE niveles_usuarios_id_nivel_usuario = 3 ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC); // Asumiendo que 3 es el ID para tutores
$modalidades = $conn->query("SELECT id_modalidad, nombre FROM modalidades ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$modificacion_ruta= "../";
include "../objects/header.php";
?>

    <div class="container">
        <h1 class="mb-4">Gestión de Grupos</h1>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#grupoModal" id="btnNuevoGrupo">
            <i class="bi bi-plus-circle"></i> Agregar Grupo
        </button>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tutor</th>
                    <th>Carrera</th>
                    <th>Modalidad</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="gruposBody"></tbody>
        </table>
    </div>

    <div class="modal fade" id="grupoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Formulario de Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formGrupo">
                        <input type="hidden" id="id_grupo" name="id_grupo">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Grupo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <label for="tutor" class="form-label">Tutor Asignado</label>
                            <select class="form-select" id="tutor" name="tutor" required>
                                <option value="">Seleccione un tutor</option>
                                <?php foreach ($tutores as $tutor): ?>
                                    <option value="<?= $tutor['id_usuario'] ?>"><?= htmlspecialchars($tutor['nombre_completo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="carrera" class="form-label">Carrera</label>
                            <select class="form-select" id="carrera" name="carrera" required>
                                 <option value="">Seleccione una carrera</option>
                                <?php foreach ($carreras as $carrera): ?>
                                    <option value="<?= $carrera['id_carrera'] ?>"><?= htmlspecialchars($carrera['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="modalidad" class="form-label">Modalidad</label>
                            <select class="form-select" id="modalidad" name="modalidad" required>
                                 <option value="">Seleccione una modalidad</option>
                                <?php foreach ($modalidades as $modalidad): ?>
                                    <option value="<?= $modalidad['id_modalidad'] ?>"><?= htmlspecialchars($modalidad['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="estatus" class="form-label">Estatus</label>
                            <select class="form-select" id="estatus" name="estatus">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
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

    <script src="../../node_modules/jquery/dist/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            const modal = new bootstrap.Modal(document.getElementById('grupoModal'));

            function cargarGrupos() {
                // Asumiendo que tu controlador responde en la acción 'listar'
                $.get("../../controllers/gruposController.php?accion=listar", function(data) {
                    const grupos = data; // El controlador ya debería devolver JSON
                    let rows = "";
                    grupos.forEach(g => {
                        rows += `<tr>
                            <td>${g.id_grupo}</td>
                            <td>${g.nombre}</td>
                            <td>${g.tutor ?? 'N/A'}</td>
                            <td>${g.carrera ?? 'N/A'}</td>
                            <td>${g.modalidad ?? 'N/A'}</td>
                            <td>${g.estatus == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}</td>
                            <td>
                                <button class="btn btn-warning btn-sm btn-editar" data-id="${g.id_grupo}">Editar</button>
                                <button class="btn btn-danger btn-sm btn-eliminar" data-id="${g.id_grupo}">Eliminar</button>
                            </td>
                        </tr>`;
                    });
                    $("#gruposBody").html(rows);
                });
            }

            // Limpiar modal al abrir para 'Nuevo'
            $("#btnNuevoGrupo").on('click', function() {
                $("#formGrupo")[0].reset();
                $("#id_grupo").val('');
                $("#modalLabel").text("Agregar Grupo");
            });

            // Guardar (Crear o Actualizar)
            $("#btnGuardar").on('click', function() {
                const id = $("#id_grupo").val();
                const url = id ? `../../controllers/gruposController.php?accion=actualizar` : `../../controllers/gruposController.php?accion=crear`;
                const data = {
                    id_grupo: id,
                    nombre: $("#nombre").val(),
                    tutor: $("#tutor").val(),
                    carrera: $("#carrera").val(),
                    modalidad: $("#modalidad").val(),
                    estatus: $("#estatus").val()
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function(response) {
                        if(response.success){
                            modal.hide();
                            cargarGrupos();
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            });

            // Cargar datos para editar
            $("#gruposBody").on('click', '.btn-editar', function() {
                const id = $(this).data('id');
                 $.get("../../controllers/gruposController.php?accion=listar", function(data) {
                     const grupo = data.find(g => g.id_grupo == id);
                     if(grupo){
                        $("#id_grupo").val(grupo.id_grupo);
                        $("#nombre").val(grupo.nombre);
                        $("#tutor").val(grupo.usuarios_id_usuario_tutor);
                        $("#carrera").val(grupo.carreras_id_carrera);
                        $("#modalidad").val(grupo.modalidades_id_modalidad);
                        $("#estatus").val(grupo.estatus);
                        $("#modalLabel").text("Editar Grupo");
                        modal.show();
                     }
                 });
            });

            
            $("#gruposBody").on('click', '.btn-eliminar', function() {
                const id = $(this).data('id');
                if (confirm("¿Estás seguro de que deseas eliminar este grupo?")) {
                    $.ajax({
                        url: `../../controllers/gruposController.php?accion=eliminar&id=${id}`,
                        type: 'GET', // o POST si lo prefieres
                        success: function(response) {
                             if(response.success){
                                cargarGrupos();
                                alert(response.message);
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                }
            });

            cargarGrupos();
        });
    </script>
<?php include "../objects/footer.php";?>