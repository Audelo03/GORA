<?php
/*if (session_status() !== PHP_SESSION_ACTIVE) {
     header("Location: ../login.php");
}
if ($_SESSION['usuario_nivel'] != 1) {
    header("Location: ../login.php");
    exit();
}*/

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../../vendor/bootstrap/css/bootstrap.min.css">
    <style>
        #formUsuario {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Gestión de Usuarios</h2>
    <button class="btn btn-success mb-3" onclick="showForm()">Agregar Usuario</button>

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

<div id="formUsuario" style="display:none;">
    <h4 class="mb-3" id="formTitle">Formulario de Usuario</h4>
    <input type="hidden" id="id_usuario">

    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre(s)</label>
        <input type="text" id="nombre" placeholder="Nombre del usuario" class="form-control">
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
            <input type="text" id="apellido_paterno" placeholder="Apellido Paterno" class="form-control">
        </div>
        <div class="col">
            <label for="apellido_materno" class="form-label">Apellido Materno</label>
            <input type="text" id="apellido_materno" placeholder="Apellido Materno (Opcional)" class="form-control">
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" placeholder="correo@ejemplo.com" class="form-control">
    </div>
    
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" id="password" placeholder="Dejar en blanco para no cambiar" class="form-control">
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="niveles_usuarios_id_nivel_usuario" class="form-label">Nivel de Usuario</label>
            <select id="niveles_usuarios_id_nivel_usuario" class="form-control">
                <option value="1">Administrador General</option>
                <option value="2">Coordinador de Carrera</option>
                <option value="3">Tutor Académico</option>
                <option value="4">Director Academico</option>
            </select>
        </div>
        <div class="col">
            <label for="estatus" class="form-label">Estatus</label>
            <select id="estatus" class="form-control">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>
    </div>
    
    <div class="d-flex justify-content-between">
        <button onclick="guardarUsuario()" class="btn btn-primary">Guardar</button>
        <button onclick="hideForm()" class="btn btn-secondary">Cancelar</button>
    </div>
</div>

<script src="../../node_modules/jquery/dist/jquery.min.js"></script>
<script>
const estatusMap = {
    1: '<span class="badge bg-success">Activo</span>',
    0: '<span class="badge bg-danger">Inactivo</span>'
};

function cargarUsuarios() {
    $.get("../../controllers/usuarioController.php?action=index", function(data) {
        let usuarios;
        try {
            // Intenta interpretar la respuesta del servidor como JSON
            usuarios = JSON.parse(data);
        } catch (e) {
            // Si falla, es porque la respuesta no es un JSON válido (probablemente un error de PHP)
            console.error("La respuesta del servidor no es un JSON válido.");
            console.error("Respuesta recibida:", data); // Muestra la respuesta exacta en la consola
            $("#tablaUsuarios tbody").html('<tr><td colspan="6" class="text-center text-danger">Error al cargar datos. Revisa la consola para más detalles.</td></tr>');
            return; // Detiene la ejecución
        }

        let rows = "";
        if (usuarios.length === 0) {
            rows = '<tr><td colspan="6" class="text-center">No se encontraron usuarios.</td></tr>';
        } else {
            usuarios.forEach(u => {
                const nombreCompleto = `${u.nombre} ${u.apellido_paterno} ${u.apellido_materno ?? ''}`;
                rows += `<tr>
                    <td>${u.id_usuario}</td>
                    <td>${nombreCompleto}</td>
                    <td>${u.email}</td>
                    <td>${u.nivel_usuario ?? 'N/A'}</td>
                    <td>${estatusMap[u.estatus] ?? 'Desconocido'}</td>
                    <td>
                        <button onclick='editarUsuario(${JSON.stringify(u)})' class="btn btn-sm btn-warning mb-1">Editar</button>
                        <button onclick="eliminarUsuario(${u.id_usuario})" class="btn btn-sm btn-danger mb-1">Eliminar</button>
                    </td>
                </tr>`;
            });
        }
        $("#tablaUsuarios tbody").html(rows);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("Error en la petición AJAX:", textStatus, errorThrown);
        $("#tablaUsuarios tbody").html('<tr><td colspan="6" class="text-center text-danger">Error de conexión con el servidor.</td></tr>');
    });
}


function showForm(usuario = null) {
    $("#id_usuario").val('');
    $("#nombre").val('');
    $("#apellido_paterno").val('');
    $("#apellido_materno").val('');
    $("#email").val('');
    $("#password").val('');
    $("#niveles_usuarios_id_nivel_usuario").val('1');
    $("#estatus").val('1');

    if (usuario) {
        $("#formTitle").text("Editar Usuario");
        $("#id_usuario").val(usuario.id_usuario);
        $("#nombre").val(usuario.nombre);
        $("#apellido_paterno").val(usuario.apellido_paterno);
        $("#apellido_materno").val(usuario.apellido_materno);
        $("#email").val(usuario.email);
        $("#niveles_usuarios_id_nivel_usuario").val(usuario.niveles_usuarios_id_nivel_usuario);
        $("#estatus").val(usuario.estatus);
    } else {
        $("#formTitle").text("Agregar Usuario");
    }
    
    $("#formUsuario").slideDown();
}

function hideForm() {
    $("#formUsuario").slideUp();
}

function guardarUsuario() {
    let id = $("#id_usuario").val();
    let datos = {
        id_usuario: id,
        nombre: $("#nombre").val(),
        apellido_paterno: $("#apellido_paterno").val(),
        apellido_materno: $("#apellido_materno").val(),
        email: $("#email").val(),
        password: $("#password").val(),
        estatus: parseInt($("#estatus").val()),
        niveles_usuarios_id_nivel_usuario: parseInt($("#niveles_usuarios_id_nivel_usuario").val())
    };

    if (!datos.nombre || !datos.apellido_paterno || !datos.email) {
        alert("Nombre, Apellido Paterno y Email son campos requeridos.");
        return;
    }
    if (!id && !datos.password) {
        alert("La contraseña es requerida para nuevos usuarios.");
        return;
    }

    let url = id ? "../../controllers/usuarioController.php?action=update" : "../../controllers/usuarioController.php?action=store";
    
    $.post(url, datos, function(response) {
        console.log(response);
        cargarUsuarios();
        hideForm();
    }).fail(function() {
        alert("Ocurrió un error al guardar el usuario.");
    });
}

function editarUsuario(usuario) {
    showForm(usuario);
}

function eliminarUsuario(id) {
    if(confirm("¿Está seguro de que desea eliminar este usuario? Esta acción no se puede deshacer.")) {
        $.post("../../controllers/usuarioController.php?action=delete", {id_usuario: id}, function(response) {
            console.log(response);
            cargarUsuarios();
        }).fail(function() {
            alert("Ocurrió un error al eliminar el usuario.");
        });
    }
}

$(document).ready(cargarUsuarios);
</script>
</body>
</html>
