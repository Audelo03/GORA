<?php
require_once __DIR__ . "/../../controllers/authController.php";
require_once __DIR__ . "/../../config/db.php";

$auth = new AuthController($conn);
$auth->checkAuth();
$page_title = "Usuarios";
$modificacion_ruta = "../";
include "../objects/header.php";

?>

<div class="container mt-4">

    <button class="btn btn-success mb-3" id="btnNuevoUsuario">
        <i class="bi bi-person-plus-fill"></i> Agregar Usuario
    </button>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="tablaUsuarios">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Email</th>
                <th>Nivel</th>
                <th>Estatus</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
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
                        <div class="col-md-6">
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
                <button type="button" class="btn btn-primary" onclick="guardarUsuario()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<?php
include "../objects/footer.php";

?>
<script>
const usuarioModal = new bootstrap.Modal(document.getElementById('usuarioModal'));
const estatusMap = {
    1: '<span class="badge bg-success">Activo</span>',
    0: '<span class="badge bg-danger">Inactivo</span>'
};

function cargarNiveles() {
    // Usamos 'json' para que jQuery parsee la respuesta automáticamente
    $.get("../../controllers/nivelesusuariosController.php?accion=listar", function(niveles) {
        let options = "";
        niveles.forEach(n => {
            options += `<option value="${n.id_nivel_usuario}">${n.nombre}</option>`;
        });
        $("#niveles_usuarios_id_nivel_usuario").html(options);
    }, 'json');
}

function cargarUsuarios() {
    $.get("../../controllers/usuarioController.php?action=index", function(data) {
        let usuarios = typeof data === 'string' ? JSON.parse(data) : data;
        let rows = "";
        usuarios.forEach(u => {
            const nombreCompleto = `${u.nombre} ${u.apellido_paterno} ${u.apellido_materno ?? ''}`.trim();
            rows += `<tr>
                <td>${u.id_usuario}</td>
                <td>${nombreCompleto}</td>
                <td>${u.email}</td>
                <td>${u.nivel_usuario ?? 'N/A'}</td>
                <td>${estatusMap[u.estatus] ?? 'Desconocido'}</td>
                <td>
                    <button onclick='editarUsuario(${JSON.stringify(u)})' class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil-square"></i></button>
                    <button onclick="eliminarUsuario(${u.id_usuario})" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                </td>
            </tr>`;
        });
        $("#tablaUsuarios tbody").html(rows);
    });
}

$('#btnNuevoUsuario').click(function() {
    $('#formUsuario')[0].reset();
    $('#id_usuario').val('');
    $('#modalLabel').text('Agregar Usuario');
    // Se requiere contraseña para nuevos usuarios
    $('#password').prop('required', true); 
    usuarioModal.show();
});

function editarUsuario(usuario) {
    $('#formUsuario')[0].reset();
    $('#id_usuario').val(usuario.id_usuario);
    $('#nombre').val(usuario.nombre);
    $('#apellido_paterno').val(usuario.apellido_paterno);
    $('#apellido_materno').val(usuario.apellido_materno);
    $('#email').val(usuario.email);
    $('#password').val('');
    // La contraseña no es requerida al editar
    $('#password').prop('required', false);
    $('#niveles_usuarios_id_nivel_usuario').val(usuario.niveles_usuarios_id_nivel_usuario);
    $('#estatus').val(usuario.estatus);
    $('#modalLabel').text('Editar Usuario');
    usuarioModal.show();
}

/**
 * --- MODIFICADO ---
 * Guarda un usuario nuevo o actualiza uno existente y muestra alertas de SweetAlert.
 */
function guardarUsuario() {
    // Validar contraseña para nuevos usuarios
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
    let url = id ? "../../controllers/usuarioController.php?action=update" : "../../controllers/usuarioController.php?action=store";

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
        
     
        usuarioModal.hide();
        cargarUsuarios();

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
});}

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
            $.post("../../controllers/usuarioController.php?action=delete", { id_usuario: id }, function(response) {
                cargarUsuarios();
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

$(document).ready(function() {
    cargarNiveles();
    cargarUsuarios();
});
</script>
