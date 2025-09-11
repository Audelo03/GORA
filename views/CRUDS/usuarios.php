<?php
// Remove session_start() as it's already started in index.php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Usuarios";
include __DIR__ . "/../objects/header.php";

?>

    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <button class="btn btn-success" id="btnNuevoUsuario">
                    <i class="bi bi-plus-circle"></i> Agregar Usuario
                </button>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Buscar usuarios...">
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
            <table class="table table-striped table-hover" id="tablaUsuarios">
                <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Nivel</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody id="usuariosBody"></tbody>
            </table>
        </div>

        <!-- Controles de paginación -->
        <nav aria-label="Paginación de usuarios" class="mt-3">
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

<div class="modal fade" id="usuarioModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Formulario de Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                 <form id="formUsuario">
                    <input type="hidden" id="id_usuario" name="id_usuario">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="nombre" class="form-label">Nombre(s)</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                            <input type="text" id="apellido_paterno" name="apellido_paterno" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                            <input type="text" id="apellido_materno" name="apellido_materno" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                        <small class="form-text text-muted">La contraseña es requerida para nuevos usuarios.</small>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="niveles_usuarios_id_nivel_usuario" class="form-label">Nivel de Usuario</label>
                            <select id="niveles_usuarios_id_nivel_usuario" name="niveles_usuarios_id_nivel_usuario" class="form-select">
                                </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarUsuario()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<?php
include __DIR__ . "/../objects/footer.php";

?>
<script>
// Global variables
let currentPage = 1;
let itemsPerPage = 10;
let totalItems = 0;
let totalPages = 0;
let searchTerm = '';
let isLoading = false;


// Global function for loading users
function cargarUsuarios(page = 1, search = '') {
        if (isLoading) return;
        
        isLoading = true;
        currentPage = page;
        searchTerm = search;
        
        // Mostrar loading
        $('#usuariosBody').html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>');
        
        const params = new URLSearchParams({
            action: 'paginated',
            page: page,
            limit: itemsPerPage,
            search: search
        });
        
        $.get(`/GORA/controllers/usuarioController.php?${params}`, function(response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            
            if (data.success) {
                totalItems = data.total;
                totalPages = data.totalPages;
                currentPage = data.currentPage;
                
                renderUsuarios(data.usuarios);
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

// Global function for rendering users table
function renderUsuarios(usuarios) {
        $('#usuariosBody').empty();
        
        if (usuarios.length === 0) {
            $('#usuariosBody').html('<tr><td colspan="5" class="text-center text-muted">No se encontraron usuarios</td></tr>');
            return;
        }
        
        usuarios.forEach(u => {
            const nombreCompleto = `${u.nombre} ${u.apellido_paterno} ${u.apellido_materno ?? ''}`.trim();
            const row = `<tr>
                <td>${u.id_usuario}</td>
                <td>${nombreCompleto}</td>
                <td>${u.email}</td>
                <td>${u.nivel_usuario ?? 'N/A'}</td>
                <td>
                    <button onclick='editarUsuario(${JSON.stringify(u)})' class="btn btn-sm btn-warning" 
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Usuario">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button onclick="eliminarUsuario(${u.id_usuario})" class="btn btn-sm btn-danger" 
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar Usuario">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </td>
            </tr>`;
            $('#usuariosBody').append(row);
        });
        
        // Reinicializar tooltips después de renderizar
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
}

// Global function for updating pagination info
function updatePaginationInfo() {
        const start = ((currentPage - 1) * itemsPerPage) + 1;
        const end = Math.min(currentPage * itemsPerPage, totalItems);
        $('#paginationInfo').text(`Mostrando ${start}-${end} de ${totalItems} registros`);
}

// Global function for rendering pagination controls
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

// Global function for showing errors
function showError(message) {
        $('#usuariosBody').html(`<tr><td colspan="5" class="text-center text-danger">${message}</td></tr>`);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
}

// Global function for loading levels
function cargarNiveles() {
    $.get("/GORA/controllers/nivelesusuariosController.php?accion=listar", function(niveles) {
        let options = "";
        niveles.forEach(n => {
            options += `<option value="${n.id_nivel_usuario}">${n.nombre}</option>`;
        });
        $("#niveles_usuarios_id_nivel_usuario").html(options);
    }, 'json');
}

// Event listener for page load
window.addEventListener('load', function() {
    const usuarioModal = new bootstrap.Modal(document.getElementById('usuarioModal'));
    
    // Event Handlers
    $('#btnNuevoUsuario').on('click', function() {
        $('#formUsuario')[0].reset();
        $('#id_usuario').val('');
        $('#modalLabel').text('Agregar Usuario');
        $('#password').prop('required', true); 
        usuarioModal.show();
    });

    // Búsqueda
    $('#btnSearch').on('click', function() {
        const search = $('#searchInput').val().trim();
        cargarUsuarios(1, search);
    });

    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            const search = $(this).val().trim();
            cargarUsuarios(1, search);
        }
    });

    $('#btnClear').on('click', function() {
        $('#searchInput').val('');
        cargarUsuarios(1, '');
    });

    // Cambio de items por página
    $('#itemsPerPage').on('change', function() {
        itemsPerPage = parseInt($(this).val());
        cargarUsuarios(1, searchTerm);
    });

    // Paginación
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page !== currentPage && page >= 1 && page <= totalPages) {
            cargarUsuarios(page, searchTerm);
        }
    });

    // Modal events
    $('#usuarioModal').on('hidden.bs.modal', function() {
        $('#formUsuario')[0].reset();
        $('#id_usuario').val('');
    });

    // Inicializar Select2 para los selects
    function inicializarSelect2() {
        // Select de Niveles de Usuario
        $('#niveles_usuarios_id_nivel_usuario').select2({
            theme: 'bootstrap-5',
            placeholder: 'Seleccione un nivel',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#usuarioModal')
        });
    }

    // Cargar datos iniciales
    cargarNiveles();
    cargarUsuarios();
    
    // Inicializar Select2 después de cargar los datos
    setTimeout(inicializarSelect2, 100);
    
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Reinicializar Select2 cuando se abre el modal
    $('#usuarioModal').on('shown.bs.modal', function() {
        setTimeout(function() {
            $('#niveles_usuarios_id_nivel_usuario').select2('destroy').select2({
                theme: 'bootstrap-5',
                placeholder: 'Seleccione un nivel',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#usuarioModal')
            });
        }, 100);
    });
});

// Global functions
function editarUsuario(usuario) {
    $('#formUsuario')[0].reset();
    $('#id_usuario').val(usuario.id_usuario);
    $('#nombre').val(usuario.nombre);
    $('#apellido_paterno').val(usuario.apellido_paterno);
    $('#apellido_materno').val(usuario.apellido_materno);
    $('#email').val(usuario.email);
    $('#password').val('');
    $('#password').prop('required', false);
    $('#niveles_usuarios_id_nivel_usuario').val(usuario.niveles_usuarios_id_nivel_usuario);
    $('#modalLabel').text('Editar Usuario');
    $('#usuarioModal').modal('show');
}

function guardarUsuario() {
    let id = $("#id_usuario").val();
    if (!id && !$("#password").val()) {
        Swal.fire({
            icon: 'error',
            title: 'Campo requerido',
            text: 'La contraseña es obligatoria para los nuevos usuarios.'
        });
        return; 
    }

    let datos = $("#formUsuario").serialize();
    let url = id ? "/GORA/controllers/usuarioController.php?action=update" : "/GORA/controllers/usuarioController.php?action=store";

    $.post(url, datos, function(response) {
        let estado = response.status;
        let mensaje = response.message;

        if (estado === "ok") {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
            
            $('#usuarioModal').modal('hide');
            $('#formUsuario')[0].reset();
            // Recargar la página actual
            const currentPage = window.currentPage || 1;
            const searchTerm = window.searchTerm || '';
            cargarUsuarios(currentPage, searchTerm);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Ocurrió un error',
                text: mensaje 
            });
        }
    }, 'json').fail(function() {
        Swal.fire({
            icon: 'error',
            title: 'Error de comunicación',
            text: 'No se pudo conectar con el servidor. Por favor, inténtelo de nuevo.'
        });
    });
}

function eliminarUsuario(id) {
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
            $.post("/GORA/controllers/usuarioController.php?action=delete", { id_usuario: id }, function(response) {
                $('#usuarioModal').modal('hide');
                // Recargar la página actual
                const currentPage = window.currentPage || 1;
                const searchTerm = window.searchTerm || '';
                cargarUsuarios(currentPage, searchTerm);
                Swal.fire(
                    '¡Eliminado!',
                    'El usuario ha sido eliminado.',
                    'success'
                );
            }, 'json').fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo eliminar el usuario, verifica sus dependencias.'
                });
            });
        }
    });
}
</script>
