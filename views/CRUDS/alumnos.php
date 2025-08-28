<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if ($_SESSION['usuario_nivel'] != 1) {
    header("Location: ../login.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Alumnos</title>
    <link rel="stylesheet" href="../../vendor/bootstrap/css/bootstrap.min.css">
    <style>
        #formAlumno {
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
    <h2 class="mb-4">Gestión de Alumnos</h2>
    <button class="btn btn-success mb-3" onclick="showForm()">Agregar Alumno</button>

    <table class="table table-striped table-hover" id="tablaAlumnos">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Matrícula</th>
            <th>Nombre</th>
            <th>Apellido Paterno</th>
            <th>Apellido Materno</th>
            <th>Estatus</th>
            <th>Carrera</th>
            <th>Grupo</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div id="formAlumno" style="display:none;">
    <h4 class="mb-3">Formulario de Alumno</h4>
    <input type="hidden" id="id">
    <input type="text" id="matricula" placeholder="Matrícula" class="form-control mb-2">
    <input type="text" id="nombre" placeholder="Nombre" class="form-control mb-2">
    <input type="text" id="apellido_paterno" placeholder="Apellido Paterno" class="form-control mb-2">
    <input type="text" id="apellido_materno" placeholder="Apellido Materno" class="form-control mb-2">
    <select id="estatus" class="form-control mb-2">
        <option value="1">Activo</option>
        <option value="0">Inactivo</option>
        <option value="2">Egresado</option>
        <option value="3">Baja</option>
    </select>
    <input type="number" id="carreras_id_carrera" placeholder="ID Carrera" class="form-control mb-2">
    <input type="number" id="grupos_id_grupo" placeholder="ID Grupo" class="form-control mb-2">
    <div class="d-flex justify-content-between">
        <button onclick="guardarAlumno()" class="btn btn-primary">Guardar</button>
        <button onclick="hideForm()" class="btn btn-secondary">Cancelar</button>
    </div>
</div>

<script src= "../../node_modules/jquery/dist/jquery.min.js"></script>
<script>
function cargarAlumnos() {
    $.get("../../controllers/alumnoController.php?action=index", function(data) {
        let alumnos = JSON.parse(data);
        let rows = "";
        alumnos.forEach(a => {
            rows += `<tr>
                <td>${a.id_alumno}</td>
                <td>${a.matricula}</td>
                <td>${a.nombre}</td>
                <td>${a.apellido_paterno}</td>
                <td>${a.apellido_materno ?? ''}</td>
                <td>${a.estatus}</td>
                <td>${a.carrera ?? ''}</td>
                <td>${a.grupo ?? ''}</td>
                <td>
                    <button onclick="editarAlumno(${a.id_alumno}, '${a.matricula}', '${a.nombre}', '${a.apellido_paterno}', '${a.apellido_materno ?? ''}', ${a.estatus}, ${a.carreras_id_carrera}, ${a.grupos_id_grupo})" class="btn btn-sm btn-warning mb-1">Editar</button>
                    <button onclick="eliminarAlumno(${a.id_alumno})" class="btn btn-sm btn-danger mb-1">Eliminar</button>
                </td>
            </tr>`;
        });
        $("#tablaAlumnos tbody").html(rows);
    });
}

function showForm() {
    $("#formAlumno").slideDown();
   
}

function hideForm() {
    $("#formAlumno").slideUp();
}

function guardarAlumno() {
    let id = $("#id").val();
    let datos = {
        id: id,
        matricula: $("#matricula").val(),
        nombre: $("#nombre").val(),
        apellido_paterno: $("#apellido_paterno").val(),
        apellido_materno: $("#apellido_materno").val(),
        estatus: parseInt($("#estatus").val()),
        carreras_id_carrera: parseInt($("#carreras_id_carrera").val()),
        grupos_id_grupo: parseInt($("#grupos_id_grupo").val())
    };
    let url = id ? "../../controllers/alumnoController.php?action=update" : "../../controllers/alumnoController.php?action=store";
    $.post(url, datos, function() {
        cargarAlumnos();
        hideForm();
    });
}

function editarAlumno(id, matricula, nombre, apPat, apMat, estatus, carrera, grupo) {
    $("#id").val(id);
    $("#matricula").val(matricula);
    $("#nombre").val(nombre);
    $("#apellido_paterno").val(apPat);
    $("#apellido_materno").val(apMat);
    $("#estatus").val(estatus);
    $("#carreras_id_carrera").val(carrera);
    $("#grupos_id_grupo").val(grupo);
    showForm();
}

function eliminarAlumno(id) {
    if(confirm("¿Desea eliminar este alumno?")) {
        $.post("../../controllers/alumnoController.php?action=delete", {id:id}, function() {
            cargarAlumnos();
        });
    }
}

$(document).ready(cargarAlumnos);
</script>
</body>
</html>
