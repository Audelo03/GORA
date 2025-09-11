<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php"; // Para obtener datos para los selects

$auth = new AuthController($conn);
$auth->checkAuth();

$carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$tutores = $conn->query("SELECT id_usuario, CONCAT(nombre, ' ', apellido_paterno) as nombre_completo FROM usuarios WHERE niveles_usuarios_id_nivel_usuario = 3 ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC); // Asumiendo que 3 es el ID para tutores
$modalidades = $conn->query("SELECT id_modalidad, nombre FROM modalidades ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$modificacion_ruta = "../";
$page_title = "Grupos";
include __DIR__ . "/../objects/header.php";
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" id="btnNuevoGrupo">
                <i class="bi bi-plus-circle"></i> Agregar Grupo
            </button>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Buscar grupos...">
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
                    <th>Tutor</th>
                    <th>Carrera</th>
                    <th>Modalidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="gruposBody"></tbody>
        </table>
    </div>

    <!-- Controles de paginación -->
    <nav aria-label="Paginación de grupos" class="mt-3">
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
                        <label for="usuarios_id_usuario_tutor" class="form-label">Tutor Asignado</label>
                        <select class="form-select" id="usuarios_id_usuario_tutor" name="usuarios_id_usuario_tutor" required>
                            <option value="">Seleccione un tutor</option>
                            <?php foreach ($tutores as $tutor) : ?>
                                <option value="<?= $tutor['id_usuario'] ?>"><?= htmlspecialchars($tutor['nombre_completo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="carreras_id_carrera" class="form-label">Carrera</label>
                        <select class="form-select" id="carreras_id_carrera" name="carreras_id_carrera" required>
                            <option value="">Seleccione una carrera</option>
                            <?php foreach ($carreras as $carrera) : ?>
                                <option value="<?= $carrera['id_carrera'] ?>"><?= htmlspecialchars($carrera['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="modalidades_id_modalidad" class="form-label">Modalidad</label>
                        <select class="form-select" id="modalidades_id_modalidad" name="modalidades_id_modalidad" required>
                            <option value="">Seleccione una modalidad</option>
                            <?php foreach ($modalidades as $modalidad) : ?>
                                <option value="<?= $modalidad['id_modalidad'] ?>"><?= htmlspecialchars($modalidad['nombre']) ?></option>
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

<?php
include __DIR__ . "/../objects/footer.php";

?><script>
window.addEventListener('load', function() {
    const grupoModal = new bootstrap.Modal(document.getElementById('grupoModal'));
    
    // Variables de paginación
    let currentPage = 1;
    let itemsPerPage = 10;
    let totalItems = 0;
    let totalPages = 0;
    let searchTerm = '';
    let isLoading = false;

    // Función para cargar grupos con paginación
    function cargarGrupos(page = 1, search = '') {
        if (isLoading) return;
        
        isLoading = true;
        currentPage = page;
        searchTerm = search;
        
        // Mostrar loading
        $('#gruposBody').html('<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>');
        
        const params = new URLSearchParams({
            action: 'paginated',
            page: page,
            limit: itemsPerPage,
            search: search
        });
        
        $.get(`/GORA/controllers/gruposController.php?${params}`, function(response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            
            if (data.success) {
                totalItems = data.total;
                totalPages = data.totalPages;
                currentPage = data.currentPage;
                
                renderGrupos(data.grupos);
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

        // Función para renderizar la tabla de grupos
        function renderGrupos(grupos) {
            $('#gruposBody').empty();
            
            if (grupos.length === 0) {
                $('#gruposBody').html('<tr><td colspan="6" class="text-center text-muted">No se encontraron grupos</td></tr>');
                return;
            }
            
            grupos.forEach(g => {
                const row = `<tr>
                <td>${g.id_grupo}</td>
                <td>${g.nombre}</td>
                <td>${g.tutor_nombre ?? 'N/A'}</td>
                <td>${g.carrera_nombre ?? 'N/A'}</td>
                <td>${g.modalidad_nombre ?? 'N/A'}</td>
                <td>
                    <button class="btn btn-warning btn-sm btn-editar" data-grupo='${JSON.stringify(g)}' 
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Grupo">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-danger btn-sm btn-eliminar" data-id="${g.id_grupo}" 
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Grupo">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </td>
            </tr>`;
            $('#gruposBody').append(row);
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
        $('#gruposBody').html(`<tr><td colspan="6" class="text-center text-danger">${message}</td></tr>`);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }

    // Event Handlers
    $('#btnNuevoGrupo').on('click', function() {
        $('#formGrupo')[0].reset();
        $('#id_grupo').val('');
        $('#modalLabel').text('Agregar Grupo');
        grupoModal.show();
    });

    // Búsqueda
    $('#btnSearch').on('click', function() {
        const search = $('#searchInput').val().trim();
        cargarGrupos(1, search);
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            const search = $(this).val().trim();
            cargarGrupos(1, search);
        }
    });

    $('#btnClear').on('click', function() {
        $('#searchInput').val('');
        cargarGrupos(1, '');
    });

    // Cambio de items por página
    $('#itemsPerPage').on('change', function() {
        itemsPerPage = parseInt($(this).val());
        cargarGrupos(1, searchTerm);
    });

    // Paginación
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page !== currentPage && page >= 1 && page <= totalPages) {
            cargarGrupos(page, searchTerm);
        }
    });

    // Modal events
    $('#grupoModal').on('hidden.bs.modal', function() {
        $('#formGrupo')[0].reset();
        $('#id_grupo').val('');
    });

    // Guardar grupo
    $('#btnGuardar').on('click', function() {
        let id = $("#id_grupo").val();
        let url = id ? "/GORA/controllers/gruposController.php?action=update" : "/GORA/controllers/gruposController.php?action=store";
        
        $.post(url, $('#formGrupo').serialize())
            .done(function(response) {
                if (response.status === 'success') {
                    grupoModal.hide();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    // Recargar la página actual
                    cargarGrupos(currentPage, searchTerm);
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

    // Editar grupo
    $(document).on('click', '.btn-editar', function() {
        const grupo = $(this).data('grupo');
        
        $("#id_grupo").val(grupo.id_grupo);
        $("#nombre").val(grupo.nombre);
        $("#usuarios_id_usuario_tutor").val(grupo.usuarios_id_usuario_tutor);
        $("#carreras_id_carrera").val(grupo.carreras_id_carrera);
        $("#modalidades_id_modalidad").val(grupo.modalidades_id_modalidad);
        $('#modalLabel').text('Editar Grupo');
        grupoModal.show();
    });

    // Eliminar grupo
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
                $.post("/GORA/controllers/gruposController.php?action=delete", { id: idParaEliminar }, function(response) {
                    if (response.status === 'success') {
                        Swal.fire('¡Eliminado!', response.message, 'success');
                        cargarGrupos(currentPage, searchTerm);
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Error de Conexión', 'No se pudo comunicar con el servidor.', 'error');
                });
            }
        });
    });

    // Inicializar Select2 para los selects
    function inicializarSelect2() {
        // Select de Tutores
        $('#usuarios_id_usuario_tutor').select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccione un tutor',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#grupoModal')
        });

        // Select de Carreras
        $('#carreras_id_carrera').select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccione una carrera',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#grupoModal')
        });

        // Select de Modalidades
        $('#modalidades_id_modalidad').select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccione una modalidad',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#grupoModal')
        });
    }

    // Cargar datos iniciales
    cargarGrupos();
    
    // Inicializar Select2 después de cargar los datos
    setTimeout(inicializarSelect2, 100);
    
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Reinicializar Select2 cuando se abre el modal
    $('#grupoModal').on('shown.bs.modal', function() {
        setTimeout(function() {
            $('#usuarios_id_usuario_tutor').select2('destroy').select2({
                theme: 'bootstrap-5',
                placeholder: 'Seleccione un tutor',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#grupoModal')
            });
            
            $('#carreras_id_carrera').select2('destroy').select2({
                theme: 'bootstrap-5',
                placeholder: 'Seleccione una carrera',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#grupoModal')
            });
            
            $('#modalidades_id_modalidad').select2('destroy').select2({
                theme: 'bootstrap-5',
                placeholder: 'Seleccione una modalidad',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#grupoModal')
            });
        }, 100);
    });
});
</script>

