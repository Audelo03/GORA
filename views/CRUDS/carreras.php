<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();

$coordinadores = $conn->query("SELECT id_usuario, CONCAT(nombre, ' ', apellido_paterno) as nombre_completo FROM usuarios WHERE niveles_usuarios_id_nivel_usuario = 2 ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC); 
$modificacion_ruta= "../";
$page_title = "Carreras";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevaCarrera">
                <i class="bi bi-plus-circle"></i> Agregar Carrera
            </button>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar carreras...">
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
        <table class="table table-striped table-hover" id="tablaCarreras">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Coordinador</th>
                    <th>Fecha De Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="carrerasBody"></tbody>
        </table>
    </div>

    <!-- Controles de paginación -->
    <nav aria-label="Paginación de carreras" class="mt-3">
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

<div class="modal fade" id="carreraModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Carrera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCarrera">
                    <input type="hidden" id="id_carrera" name="id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Carrera</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="coordinador" class="form-label">Coordinador</label>
                        <select id="coordinador_id" name="usuario_id" class="form-select" required>
                            <option value="">Seleccione un coordinador</option>
                            <?php foreach ($coordinadores as $coordinador): ?>
                                <option value="<?= $coordinador['id_usuario'] ?>"><?= htmlspecialchars($coordinador['nombre_completo']) ?></option>
                            <?php endforeach; ?>
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
<?php include __DIR__ . "/../objects/footer.php";?>

<script>
window.addEventListener('load', function() {
    const carreraModal = new bootstrap.Modal(document.getElementById('carreraModal'));
    
    // Variables de paginación
    let currentPage = 1;
    let itemsPerPage = 10;
    let totalItems = 0;
    let totalPages = 0;
    let searchTerm = '';
    let isLoading = false;

    // Función para cargar carreras con paginación
    function cargarCarreras(page = 1, search = '') {
        if (isLoading) return;
        
        isLoading = true;
        currentPage = page;
        searchTerm = search;
        
        // Mostrar loading
        $('#carrerasBody').html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>');
        
        const params = new URLSearchParams({
            action: 'paginated',
            page: page,
            limit: itemsPerPage,
            search: search
        });
        
        $.get(`/ITSAdata/controllers/carrerasController.php?${params}`, function(response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            
            if (data.success) {
                totalItems = data.total;
                totalPages = data.totalPages;
                currentPage = data.currentPage;
                
                renderCarreras(data.carreras);
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

    // Función para renderizar la tabla de carreras
    function renderCarreras(carreras) {
        $('#carrerasBody').empty();
        
        if (carreras.length === 0) {
            $('#carrerasBody').html('<tr><td colspan="5" class="text-center text-muted">No se encontraron carreras</td></tr>');
            return;
        }
        
        carreras.forEach(c => {
            const coordinadorNombre = c.coordinador_nombre ? `${c.coordinador_nombre} ${c.coordinador_apellido_paterno}` : 'N/A';
            const row = `<tr>
                <td>${c.id_carrera}</td>
                <td>${c.nombre}</td>
                <td>${coordinadorNombre}</td>
                <td>${c.fecha_creacion}</td>
                <td>
                    <button class="btn btn-warning btn-sm btn-editar" data-id='${JSON.stringify(c)}'><i class="bi bi-pencil-square"></i></button>
                    <button class="btn btn-danger btn-sm btn-eliminar" data-id="${c.id_carrera}"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`;
            $('#carrerasBody').append(row);
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
        $('#carrerasBody').html(`<tr><td colspan="5" class="text-center text-danger">${message}</td></tr>`);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }

    // Event Handlers
    $('#btnNuevaCarrera').on('click', function() {
        $('#formCarrera')[0].reset();
        $('#id_carrera').val('');
        $('#modalLabel').text('Agregar Carrera');
        carreraModal.show();
    });

    // Búsqueda
    $('#btnSearch').on('click', function() {
        const search = $('#searchInput').val().trim();
        cargarCarreras(1, search);
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            const search = $(this).val().trim();
            cargarCarreras(1, search);
        }
    });

    $('#btnClear').on('click', function() {
        $('#searchInput').val('');
        cargarCarreras(1, '');
    });

    // Cambio de items por página
    $('#itemsPerPage').on('change', function() {
        itemsPerPage = parseInt($(this).val());
        cargarCarreras(1, searchTerm);
    });

    // Paginación
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page !== currentPage && page >= 1 && page <= totalPages) {
            cargarCarreras(page, searchTerm);
        }
    });

    // Modal events
    $('#carreraModal').on('hidden.bs.modal', function() {
        $('#formCarrera')[0].reset();
        $('#id_carrera').val('');
    });

    // Guardar carrera
    $('#btnGuardar').on('click', function() {
        let id = $("#id_carrera").val();
        let url = id ? "/ITSAdata/controllers/carrerasController.php?action=update" : "/ITSAdata/controllers/carrerasController.php?action=store";
        
        $.post(url, $('#formCarrera').serialize(), function() {
            carreraModal.hide();
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'La carrera se ha guardado correctamente.',
                timer: 1500,
                showConfirmButton: false
            });
            // Recargar la página actual
            cargarCarreras(currentPage, searchTerm);
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Hubo un error al guardar la carrera.'
            });
        });
    });

    // Editar carrera
    $(document).on('click', '.btn-editar', function() {
        const carrera = $(this).data('id');
        $("#id_carrera").val(carrera.id_carrera);
        $("#nombre").val(carrera.nombre);
        $("#coordinador_id").val(carrera.coordinador_id);
        $('#modalLabel').text('Editar Carrera');
        carreraModal.show();
    });

    // Eliminar carrera
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
                $.post("/ITSAdata/controllers/carrerasController.php?action=delete", { id: idParaEliminar }, function(response) {
                    if (response.status === 'success') {
                        Swal.fire('¡Eliminada!', response.message, 'success');
                        cargarCarreras(currentPage, searchTerm);
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Error de Conexión', 'No se pudo comunicar con el servidor. Inténtalo de nuevo.', 'error');
                });
            }
        });
    });

    // Cargar datos iniciales
    cargarCarreras();
});
</script>

