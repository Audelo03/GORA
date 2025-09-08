<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php"; // Para obtener datos para los selects

$auth = new AuthController($conn);
$auth->checkAuth();

$carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$tutores = $conn->query("SELECT id_usuario, CONCAT(nombre, ' ', apellido_paterno) as nombre_completo FROM usuarios WHERE niveles_usuarios_id_nivel_usuario = 3 ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC); // Asumiendo que 3 es el ID para tutores
$modalidades = $conn->query("SELECT id_modalidad, nombre FROM modalidades ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$modificacion_ruta = "../";
include "../objects/header.php";
?>

<div class="container mt-4">
    <h1 class="mb-4">Gestión de Grupos</h1>
    <button class="btn btn-success mb-3" id="btnNuevoGrupo">
        <i class="bi bi-plus-circle"></i> Agregar Grupo
    </button>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
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
                        <select class="form-select" id="usuarios_id_usuario_tutor" name="usuarios_id_usuario_tutor" required>
                            <option value="">Seleccione un tutor</option>
                            <?php foreach ($tutores as $tutor) : ?>
                                <option value="<?= $tutor['id_usuario'] ?>"><?= htmlspecialchars($tutor['nombre_completo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="carrera" class="form-label">Carrera</label>
                        <select class="form-select" id="carreras_id_carrera" name="carreras_id_carrera" required>
                            <option value="">Seleccione una carrera</option>
                            <?php foreach ($carreras as $carrera) : ?>
                                <option value="<?= $carrera['id_carrera'] ?>"><?= htmlspecialchars($carrera['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="modalidad" class="form-label">Modalidad</label>
                        <select class="form-select" id="modalidades_id_modalidad" name="modalidades_id_modalidad" required>
                            <option value="">Seleccione una modalidad</option>
                            <?php foreach ($modalidades as $modalidad) : ?>
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
        const grupoModal = new bootstrap.Modal(document.getElementById('grupoModal'));

        function cargarGrupos() {

            $.get("../../controllers/gruposController.php?action=index", function(data) {
                console.log(data);
                const grupos = data;
                 // Depuración
                let rows = "";
                if (grupos.length > 0) {
                    grupos.forEach(g => {
                        // Se almacena el objeto completo en el data-attribute del botón editar
                        rows += `<tr>
                            <td>${g.id_grupo}</td>
                            <td>${g.nombre}</td>
                            <td>${g.tutor_nombre ?? 'N/A'}</td>
                            <td>${g.carrera_nombre ?? 'N/A'}</td>
                            <td>${g.modalidad_nombre ?? 'N/A'}</td>
                            <td>${g.estatus == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}</td>
                            <td>
                                <button class="btn btn-warning btn-sm btn-editar" data-grupo='${JSON.stringify(g)}' title="Editar"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-danger btn-sm btn-eliminar" data-id="${g.id_grupo}" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>`;
                    });
                } else {
                    rows = `<tr><td colspan="7" class="text-center">No hay grupos registrados.</td></tr>`;
                }
                $("#gruposBody").html(rows);
            }).fail(function() {
                $("#gruposBody").html('<tr><td colspan="7" class="text-center">Error al cargar los datos.</td></tr>');
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Carga',
                    text: 'No se pudieron cargar los datos de los grupos.'
                });
            });
        }

        $('#btnNuevoGrupo').click(function() {
            $('#formGrupo')[0].reset();
            $('#id_grupo').val('');
            $('#modalLabel').text('Agregar Grupo');
            grupoModal.show();
        });

        $('#btnGuardar').click(function() {
            let id = $("#id_grupo").val();
            if (id) {
                console.log("Actualizando grupo con ID:", id);
            } else {
                console.log("Creando nuevo grupo");
            }
            let url = id ? "../../controllers/gruposController.php?action=update" : "../../controllers/gruposController.php?action=store";
    

            $.post(url, $('#formGrupo').serialize())
                .done(function(response) {
                    if (response.status === 'success') {
                        grupoModal.hide();
                        cargarGrupos();
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
                            text: response.message || 'No se pudo guardar el grupo.'
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

        $("#gruposBody").on('click', '.btn-editar', function() {
            const grupo = $(this).data('grupo');
            
            $("#id_grupo").val(grupo.id_grupo);
            $("#nombre").val(grupo.nombre);
            $("#usuarios_id_usuario_tutor").val(grupo.usuarios_id_usuario_tutor);
            $("#carreras_id_carrera").val(grupo.carreras_id_carrera);
            $("#modalidades_id_modalidad").val(grupo.modalidades_id_modalidad);
            $("#estatus").val(grupo.estatus);
            $('#modalLabel').text('Editar Grupo');
            grupoModal.show();
        });

        $("#gruposBody").on('click', '.btn-eliminar', function() {
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
                    $.post("../../controllers/gruposController.php?action=delete", { id: idParaEliminar }, function(response) {
                        if (response.status === 'success') {
                            Swal.fire(
                                '¡Eliminado!',
                                response.message,
                                'success'
                            );
                            cargarGrupos();
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
                            'No se pudo comunicar con el servidor.',
                            'error'
                        );
                    });
                }
            });
        });

        cargarGrupos();
    });
</script>

<?php include "../objects/footer.php"; ?>