<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";
$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Seguimientos";
$modificacion_ruta = "../";
include __DIR__ . "/../objects/header.php";
?>

    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tipoSeguimientoModal" id="btnNuevo">
                    <i class="bi bi-plus-circle"></i> Agregar Tipo
                </button>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar tipos de seguimiento...">
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

        <!-- Controles de paginación -->
        <nav aria-label="Paginación de tipos de seguimiento" class="mt-3">
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

    <?php
include __DIR__ . "/../objects/footer.php";

?>
    <script>
    window.addEventListener('load', function() {
        const modal = new bootstrap.Modal(document.getElementById('tipoSeguimientoModal'));
        
        // Variables de paginación
        let currentPage = 1;
        let itemsPerPage = 10;
        let totalItems = 0;
        let totalPages = 0;
        let searchTerm = '';
        let isLoading = false;

        // Función para cargar tipos con paginación
        function cargarTipos(page = 1, search = '') {
            if (isLoading) return;
            
            isLoading = true;
            currentPage = page;
            searchTerm = search;
            
            // Mostrar loading
            $('#tiposBody').html('<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>');
            
            const params = new URLSearchParams({
                action: 'paginated',
                page: page,
                limit: itemsPerPage,
                search: search
            });
            
            $.get(`/GORA/controllers/tipoSeguimientoController.php?${params}`, function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (data.success) {
                    totalItems = data.total;
                    totalPages = data.totalPages;
                    currentPage = data.currentPage;
                    
                    renderTipos(data.tiposSeguimiento);
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

        // Función para renderizar la tabla de tipos
        function renderTipos(tipos) {
            $('#tiposBody').empty();
            
            if (tipos.length === 0) {
                $('#tiposBody').html('<tr><td colspan="3" class="text-center text-muted">No se encontraron tipos de seguimiento</td></tr>');
                return;
            }
            
            tipos.forEach(t => {
                const row = `<tr>
                    <td>${t.id_tipo_seguimiento}</td>
                    <td>${t.nombre}</td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar" data-id="${t.id_tipo_seguimiento}" title="Editar"><i class="bi bi-pencil-square"></i></button>
                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="${t.id_tipo_seguimiento}" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                    </td>
                </tr>`;
                $('#tiposBody').append(row);
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
            $('#tiposBody').html(`<tr><td colspan="3" class="text-center text-danger">${message}</td></tr>`);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }

        // Event Handlers
        $("#btnNuevo").on('click', function() {
            $("#formTipo")[0].reset();
            $("#id_tipo_seguimiento").val('');
            $("#modalLabel").text("Agregar Tipo de Seguimiento");
            modal.show();
        });

        // Búsqueda
        $('#btnSearch').on('click', function() {
            const search = $('#searchInput').val().trim();
            cargarTipos(1, search);
        });

        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                const search = $(this).val().trim();
                cargarTipos(1, search);
            }
        });

        $('#btnClear').on('click', function() {
            $('#searchInput').val('');
            cargarTipos(1, '');
        });

        // Cambio de items por página
        $('#itemsPerPage').on('change', function() {
            itemsPerPage = parseInt($(this).val());
            cargarTipos(1, searchTerm);
        });

        // Paginación
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (page && page !== currentPage && page >= 1 && page <= totalPages) {
                cargarTipos(page, searchTerm);
            }
        });

        // Modal events
        $('#tipoSeguimientoModal').on('hidden.bs.modal', function() {
            $("#formTipo")[0].reset();
            $("#id_tipo_seguimiento").val('');
        });

        // Guardar tipo
        $("#btnGuardar").on('click', function() {
            const id = $("#id_tipo_seguimiento").val();
            const url = id ? `/GORA/controllers/tipoSeguimientoController.php?action=update` : `/GORA/controllers/tipoSeguimientoController.php?action=store`;
            const data = $('#formTipo').serialize();

            $.post(url, data).done(function(response) {
                modal.hide();
                Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    text: 'El registro ha sido guardado correctamente.',
                    timer: 1500,
                    showConfirmButton: false
                });
                // Recargar la página actual
                cargarTipos(currentPage, searchTerm);
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error al guardar. Revise los datos e intente de nuevo.'
                });
            });
        });

        // Editar tipo
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            $.get(`/GORA/controllers/tipoSeguimientoController.php?action=show&id=${id}`, function(data) {
                const tipo = data;
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

        // Eliminar tipo
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
                    $.post(`/GORA/controllers/tipoSeguimientoController.php?action=delete`, { id: id }, function(response) {
                        Swal.fire('¡Eliminado!', 'El registro ha sido eliminado.', 'success');
                        cargarTipos(currentPage, searchTerm);
                    }).fail(function() {
                        Swal.fire('Error', 'No se pudo eliminar el registro.', 'error');
                    });
                }
            });
        });

        // Cargar datos iniciales
        cargarTipos();
    });
    </script>
