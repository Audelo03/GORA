<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();

$carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$grupos = $conn->query("SELECT id_grupo, nombre FROM grupos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$modificacion_ruta = "../";
$page_title = "Alumnos";
include "../objects/header.php"
?>


    <div class="container">
        
        <button class="btn btn-success mb-3" id="btnNuevoAlumno">
            <i class="bi bi-plus-circle"></i> Agregar Alumno
        </button>

        <div class="table-responsive">
            <table id="alumnosTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Matrícula</th>
                        <th>Nombre Completo</th>
                        <th>Carrera</th>
                        <th>Grupo</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="alumnosBody"></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="alumnoModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Formulario de Alumno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAlumno">
                        <input type="hidden" id="id_alumno" name="id_alumno">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="matricula" class="form-label">Matrícula</label>
                                <input type="text" id="matricula" name="matricula" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre(s)</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                                <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                <input type="text" id="apellido_materno" name="apellido_materno" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="carrera" class="form-label">Carrera</label>
                                <select id="carreras_id_carrera" name="carreras_id_carrera" class="form-select" required>
                                    <option value="">Seleccione una carrera</option>
                                    <?php foreach ($carreras as $carrera): ?>
                                        <option value="<?= $carrera['id_carrera'] ?>"><?= htmlspecialchars($carrera['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                             <div class="col-md-4">
                                <label for="grupo" class="form-label">Grupo</label>
                                <select id="grupos_id_grupo" name="grupos_id_grupo" class="form-select" required>
                                    <option value="">Seleccione un grupo</option>
                                     <?php foreach ($grupos as $grupo): ?>
                                        <option value="<?= $grupo['id_grupo'] ?>"><?= htmlspecialchars($grupo['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="estatus" class="form-label">Estatus</label>
                                <select id="estatus" name="estatus" class="form-select">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardar">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

   <?php include "../objects/footer.php";?> <script>
    $(document).ready(function() {
        const alumnoModal = new bootstrap.Modal(document.getElementById('alumnoModal'));
        let alumnosTable;

        function cargarAlumnos() {
            $.get("../../controllers/alumnoController.php?action=index", function(data) {
                const alumnos = JSON.parse(data);
                
                if (alumnosTable) {
                    alumnosTable.destroy();
                }

                $('#alumnosBody').empty();

                alumnos.forEach(a => {
                    const nombreCompleto = `${a.nombre} ${a.apellido_paterno} ${a.apellido_materno ?? ''}`;
                    const row = `<tr>
                        <td>${a.id_alumno}</td>
                        <td>${a.matricula}</td>
                        <td>${nombreCompleto.trim()}</td>
                        <td>${a.carrera ?? 'N/A'}</td>
                        <td>${a.grupo ?? 'N/A'}</td>
                        <td>${a.estatus == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-editar" data-id='${a.id_alumno}' title="Editar"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-danger btn-sm btn-eliminar" data-id="${a.id_alumno}" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                        </td>
                    </tr>`;
                    $('#alumnosBody').append(row);
                });

                alumnosTable = $('#alumnosTable').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
                    }
                });

            }).fail(function() {
                 $("#alumnosBody").html('<tr><td colspan="7" class="text-center">Error al cargar los datos. Por favor, intente de nuevo.</td></tr>');
                 Swal.fire({
                     icon: 'error',
                     title: 'Error de Carga',
                     text: 'No se pudieron cargar los datos de los alumnos desde el servidor.'
                 });
            });
        }

        $('#btnNuevoAlumno').on('click', function() {
            $('#formAlumno')[0].reset();
            $('#id_alumno').val('');
            $('#modalLabel').text('Agregar Alumno');
            alumnoModal.show();
        });

        $('#alumnosTable tbody').on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            $.get(`../../controllers/alumnoController.php?action=show&id=${id}`, function(data) {
                const alumno = JSON.parse(data);
                $('#id_alumno').val(alumno.id_alumno);
                $('#matricula').val(alumno.matricula);
                $('#nombre').val(alumno.nombre);
                $('#apellido_paterno').val(alumno.apellido_paterno);
                $('#apellido_materno').val(alumno.apellido_materno);
                $('#carreras_id_carrera').val(alumno.carreras_id_carrera);
                $('#grupos_id_grupo').val(alumno.grupos_id_grupo);
                $('#estatus').val(alumno.estatus);
                $('#modalLabel').text('Editar Alumno');
                alumnoModal.show();
            }).fail(function() {
                 Swal.fire({
                     icon: 'error',
                     title: 'Error',
                     text: 'No se pudo obtener la información del alumno.'
                 });
            });
        });

        $('#btnGuardar').on('click', function() {
            const id = $('#id_alumno').val();
            const url = id ? `../../controllers/alumnoController.php?action=update` : `../../controllers/alumnoController.php?action=store`;
            const data = $('#formAlumno').serialize();
            
            $.post(url, data, function(response) {
                alumnoModal.hide();
                cargarAlumnos();
                Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    text: 'El alumno ha sido guardado correctamente.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error al guardar el alumno. Revise los datos e intente de nuevo.'
                });
            });
        });

        $('#alumnosTable tbody').on('click', '.btn-eliminar', function() {
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
                    $.post(`../../controllers/alumnoController.php?action=delete`, { id: id }, function() {
                        Swal.fire(
                            '¡Eliminado!',
                            'El alumno ha sido eliminado.',
                            'success'
                        );
                        cargarAlumnos();
                    }).fail(function() {
                        Swal.fire(
                            'Error',
                            'No se pudo eliminar el alumno.',
                            'error'
                        );
                    });
                }
            });
        });

        cargarAlumnos();
    });
    </script>