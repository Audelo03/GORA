<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();

$carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$grupos = $conn->query("SELECT id_grupo, nombre FROM grupos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$modificacion_ruta = "../";
$page_title = "Alumnos";
include __DIR__ . "/../objects/header.php"
?>


    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <button class="btn btn-success" id="btnNuevoAlumno">
                    <i class="bi bi-plus-circle"></i> Agregar Alumno
                </button>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar alumnos...">
                    <button class="btn btn-outline-secondary" type="button" id="btnSearch">
                        <i class="bi bi-search"></i>
                    </button>
                    <button class="btn btn-outline-secondary" type="button" id="btnClear">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="alumnosTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Matrícula</th>
                        <th>Nombre Completo</th>
                        <th>Carrera</th>
                        <th>Grupo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="alumnosBody"></tbody>
            </table>
        </div>

        <!-- Controles de paginación -->
        <nav aria-label="Paginación de alumnos" class="mt-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <label for="itemsPerPage" class="form-label me-2 mb-0">Mostrar:</label>
                        <select class="form-select form-select-sm" id="itemsPerPage" style="width: auto;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="ms-2 text-muted" id="paginationInfo">Mostrando 0 de 0 registros</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <ul class="pagination justify-content-end mb-0" id="paginationControls">
                        <!-- Los controles se generarán dinámicamente -->
                    </ul>
                </div>
            </div>
        </nav>
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

   <?php include __DIR__ . "/../objects/footer.php";?> <script>
    window.addEventListener('load', function() {
        const alumnoModal = new bootstrap.Modal(document.getElementById('alumnoModal'));
        
        // Variables de paginación
        let currentPage = 1;
        let itemsPerPage = 10;
        let totalItems = 0;
        let totalPages = 0;
        let searchTerm = '';
        let isLoading = false;

        // Función para cargar alumnos con paginación
        function cargarAlumnos(page = 1, search = '') {
            if (isLoading) return;
            
            isLoading = true;
            currentPage = page;
            searchTerm = search;
            
            // Mostrar loading
            $('#alumnosBody').html('<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>');
            
            const params = new URLSearchParams({
                action: 'paginated',
                page: page,
                limit: itemsPerPage,
                search: search
            });
            
            $.get(`/GORA/controllers/alumnoController.php?${params}`, function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (data.success) {
                    totalItems = data.total;
                    totalPages = data.totalPages;
                    currentPage = data.currentPage;
                    
                    renderAlumnos(data.alumnos);
                    updatePaginationInfo();
                    renderPaginationControls();
                } else {
                    showError('Error al cargar los datos: ' + (data.message || 'Error desconocido'));
                }
            }).fail(function(xhr) {
                showError('Error de conexión: ' + xhr.statusText);
            }).always(function() {
                isLoading = false;
            });
        }

        // Función para renderizar la tabla de alumnos
        function renderAlumnos(alumnos) {
            $('#alumnosBody').empty();
            
            if (alumnos.length === 0) {
                $('#alumnosBody').html('<tr><td colspan="6" class="text-center text-muted">No se encontraron alumnos</td></tr>');
                return;
            }
            
            alumnos.forEach(a => {
                const nombreCompleto = `${a.nombre} ${a.apellido_paterno} ${a.apellido_materno ?? ''}`.trim();
                const row = `<tr>
                    <td>${a.id_alumno}</td>
                    <td>${a.matricula}</td>
                    <td>${nombreCompleto}</td>
                    <td>${a.carrera ?? 'N/A'}</td>
                    <td>${a.grupo ?? 'N/A'}</td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar" data-id='${a.id_alumno}' title="Editar"><i class="bi bi-pencil-square"></i></button>
                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="${a.id_alumno}" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                    </td>
                </tr>`;
                $('#alumnosBody').append(row);
            });
        }

        // Función para actualizar la información de paginación
        function updatePaginationInfo() {
            const start = ((currentPage - 1) * itemsPerPage) + 1;
            const end = Math.min(currentPage * itemsPerPage, totalItems);
            $('#paginationInfo').text(`Mostrando ${start}-${end} de ${totalItems} registros`);
        }

        // Función para renderizar los controles de paginación
        function renderPaginationControls() {
            const controls = $('#paginationControls');
            controls.empty();
            
            if (totalPages <= 1) return;
            
            // Botón Anterior
            const prevDisabled = currentPage === 1 ? 'disabled' : '';
            controls.append(`<li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">&laquo; Anterior</a>
            </li>`);
            
            // Números de página
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);
            
            if (startPage > 1) {
                controls.append(`<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`);
                if (startPage > 2) {
                    controls.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                const active = i === currentPage ? 'active' : '';
                controls.append(`<li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`);
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    controls.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
                controls.append(`<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`);
            }
            
            // Botón Siguiente
            const nextDisabled = currentPage === totalPages ? 'disabled' : '';
            controls.append(`<li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente &raquo;</a>
            </li>`);
        }

        // Función para mostrar errores
        function showError(message) {
            $('#alumnosBody').html(`<tr><td colspan="6" class="text-center text-danger">${message}</td></tr>`);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }

        // Event Handlers
        $('#btnNuevoAlumno').on('click', function() {
            $('#formAlumno')[0].reset();
            $('#id_alumno').val('');
            $('#modalLabel').text('Agregar Alumno');
            alumnoModal.show();
        });

        // Búsqueda
        $('#btnSearch').on('click', function() {
            const search = $('#searchInput').val().trim();
            cargarAlumnos(1, search);
        });

        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                const search = $(this).val().trim();
                cargarAlumnos(1, search);
            }
        });

        $('#btnClear').on('click', function() {
            $('#searchInput').val('');
            cargarAlumnos(1, '');
        });

        // Cambio de items por página
        $('#itemsPerPage').on('change', function() {
            itemsPerPage = parseInt($(this).val());
            cargarAlumnos(1, searchTerm);
        });

        // Paginación
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (page && page !== currentPage && page >= 1 && page <= totalPages) {
                cargarAlumnos(page, searchTerm);
            }
        });

        // Editar alumno
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            $.get(`/GORA/controllers/alumnoController.php?action=show&id=${id}`, function(data) {
                const alumno = JSON.parse(data);
                $('#id_alumno').val(alumno.id_alumno);
                $('#matricula').val(alumno.matricula);
                $('#nombre').val(alumno.nombre);
                $('#apellido_paterno').val(alumno.apellido_paterno);
                $('#apellido_materno').val(alumno.apellido_materno);
                $('#carreras_id_carrera').val(alumno.carreras_id_carrera);
                $('#grupos_id_grupo').val(alumno.grupos_id_grupo);
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

        // Guardar alumno
        $('#btnGuardar').on('click', function() {
            const id = $('#id_alumno').val();
            const url = id ? `/GORA/controllers/alumnoController.php?action=update` : `/GORA/controllers/alumnoController.php?action=store`;
            const data = $('#formAlumno').serialize();
            
            $.post(url, data, function(response) {
                alumnoModal.hide();
                cargarAlumnos(currentPage, searchTerm); // Recargar la página actual
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

        // Eliminar alumno
        $(document).on('click', '.btn-eliminar', function() {
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
                    $.post(`/GORA/controllers/alumnoController.php?action=delete`, { id: id }, function() {
                        Swal.fire(
                            '¡Eliminado!',
                            'El alumno ha sido eliminado.',
                            'success'
                        );
                        cargarAlumnos(currentPage, searchTerm); // Recargar la página actual
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

        // Cargar datos iniciales
        cargarAlumnos();
    }); // Close window load
    </script>