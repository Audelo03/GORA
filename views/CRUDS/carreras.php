<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Carreras</title>
    <link rel="stylesheet" href="../../vendor/bootstrap/css/bootstrap.min.css">
    <style>
        #formCarrera {
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
    <h2 class="mb-4">Gestión de Carreras</h2>
    <button class="btn btn-success mb-3" onclick="showForm()">Agregar Carreras</button>

    <table class="table table-striped table-hover" id="tablaAlumnos">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Fecha De Creación</th>
            <th>Fecha De Actualizacion</th>
            <th>Coordinador</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div id="formCarreras" style="display:none;">
    <h4 class="mb-3">Formulario de Carreras</h4>
    <input type="hidden" id="id">
    <input type="text" id="nombre" placeholder="Nombre" class="form-control mb-2">
    <input type= "date" id="fecha_creacion" placeholder="Fecha de creacion" class="form-control mb-2">
    <select id="coordinares" class="form-control mb-2">
        <?php
        //LISTAR COORDINADORES
        include "../../controllers/usuarioController.php";
        $usuarioController = new UsuarioController($conn);
        $cords = $usuarioController->listarCoordinadores();
        foreach ($cords as $usuario) {
            echo "<option value='{$usuario['id_usuario']}'>{$usuario['nombre']} {$usuario['apellido_paterno']} {$usuario['apellido_materno']}</option>";
        }
        ?>
    </select>
</div>

<script src= "../../node_modules/jquery/dist/jquery.min.js"></script>
<script>
function cargarCarreras() {
    $.get("../../controllers/carrerasController.php?action=index", function(data) {
        let carreras = JSON.parse(data);
        let rows = "";
        carreras.forEach(c => {
            
            rows += `<tr>
                <td>${c.id_carrera}</td>
                <td>${c.nombre}</td>
                <td>${c.fecha_creacion}</td>
                <td>${c.fecha_movimiento}</td>
                <td>${c.coordinador_nombre . ' '. coordinador_apellido_paterno}</td>
                
                
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
