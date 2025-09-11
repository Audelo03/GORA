<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";
$auth = new AuthController($conn);
$auth->checkAuth();
$modificacion_ruta = "../";
$page_title = "Modalidades";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevaModalidad">
                <i class="bi bi-plus-circle"></i> Agregar Modalidad
            </button>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar modalidades...">
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
        <table class="table table-bordered table-striped table-hover">
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

    <!-- Controles de paginación -->
    <nav aria-label="Paginación de modalidades" class="mt-3">
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
include __DIR__ . "/../objects/footer.php";

?>
<script>
window.addEventListener('load', function() {
    const modalidadModal = new bootstrap.Modal(document.getElementById('modalidadModal'));
    
    // Variables de paginación
    let currentPage = 1;
    let itemsPerPage = 10;
    let totalItems = 0;
    let totalPages = 0;
    let searchTerm = '';
    let isLoading = false;

    // Función para cargar modalidades con paginación
    function cargarModalidades(page = 1, search = '') {
        if (isLoading) return;
        
        isLoading = true;
        currentPage = page;
        searchTerm = search;
        
        // Mostrar loading
        $('#modalidadesBody').html('<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>');
        
        const params = new URLSearchParams({
            action: 'paginated',
            page: page,
            limit: itemsPerPage,
            search: search
        });
        
        $.get(`/GORA/controllers/modalidadesController.php?${params}`, function(response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            
            if (data.success) {
                totalItems = data.total;
                totalPages = data.totalPages;
                currentPage = data.currentPage;
                
                renderModalidades(data.modalidades);
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

        // Función para renderizar la tabla de modalidades
        function renderModalidades(modalidades) {
            $('#modalidadesBody').empty();
            
            if (modalidades.length === 0) {
                $('#modalidadesBody').html('<tr><td colspan="3" class="text-center text-muted">No se encontraron modalidades</td></tr>');
                return;
            }
            
            modalidades.forEach(m => {
                const row = `<tr>
                <td>${m.id_modalidad}</td>
                <td>${m.nombre}</td>
                <td>
                    <button class="btn btn-warning btn-sm btn-editar" data-modalidad='${JSON.stringify(m)}' 
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Modalidad">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-danger btn-sm btn-eliminar" data-id="${m.id_modalidad}" 
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Modalidad">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </td>
            </tr>`;
            $('#modalidadesBody').append(row);
        });
        
        // Reinicializar tooltips después de renderizar
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
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
        $('#modalidadesBody').html(`<tr><td colspan="3" class="text-center text-danger">${message}</td></tr>`);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }

    // Event Handlers
    $('#btnNuevaModalidad').on('click', function() {
        $('#formModalidad')[0].reset();
        $('#id_modalidad').val('');
        $('#modalLabel').text('Agregar Modalidad');
        modalidadModal.show();
    });

    // Búsqueda
    $('#btnSearch').on('click', function() {
        const search = $('#searchInput').val().trim();
        cargarModalidades(1, search);
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            const search = $(this).val().trim();
            cargarModalidades(1, search);
        }
    });

    $('#btnClear').on('click', function() {
        $('#searchInput').val('');
        cargarModalidades(1, '');
    });

    // Cambio de items por página
    $('#itemsPerPage').on('change', function() {
        itemsPerPage = parseInt($(this).val());
        cargarModalidades(1, searchTerm);
    });

    // Paginación
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page !== currentPage && page >= 1 && page <= totalPages) {
            cargarModalidades(page, searchTerm);
        }
    });

    // Modal events
    $('#modalidadModal').on('hidden.bs.modal', function() {
        $('#formModalidad')[0].reset();
        $('#id_modalidad').val('');
    });

    // Guardar modalidad
    $('#btnGuardar').on('click', function() {
        let id = $("#id_modalidad").val();
        let url = id ? "/GORA/controllers/modalidadesController.php?action=update" : "/GORA/controllers/modalidadesController.php?action=store";

        $.post(url, $('#formModalidad').serialize())
            .done(function(response) {
                if (response.status === 'success') {
                    modalidadModal.hide();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    // Recargar la página actual
                    cargarModalidades(currentPage, searchTerm);
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

    // Editar modalidad
    $(document).on('click', '.btn-editar', function() {
        const modalidad = $(this).data('modalidad');
        
        $("#id_modalidad").val(modalidad.id_modalidad);
        $("#nombre").val(modalidad.nombre);
        $('#modalLabel').text('Editar Modalidad');
        modalidadModal.show();
    });

    // Eliminar modalidad
    $(document).on('click', '.btn-eliminar', function() {
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
                $.post("/GORA/controllers/modalidadesController.php?action=delete", { id: idParaEliminar }, function(response) {
                    if (response.status === 'success') {
                        Swal.fire('¡Eliminado!', response.message, 'success');
                        cargarModalidades(currentPage, searchTerm);
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Error', 'Verifica tus relaciones con otras tablas.', 'error');
                });
            }
        });
    });

    // Cargar datos iniciales
    cargarModalidades();
    
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

