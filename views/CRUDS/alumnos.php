<?php
require_once __DIR__ . "/../../controllers/alumnoController.php";
require_once __DIR__ . "/../../controllers/authController.php";

$auth = new AuthController($conn);
$auth->checkAuth();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Alumnos</title>
    <link rel="stylesheet" href="../../vendor/bootstrap/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h1 class="mb-4">Listado de Alumnos</h1>

        <!-- FORMULARIO DE EDICION -->
        <div id="formularioEdicion" class="card mb-4 d-none">
            <div class="card-body">
                <h4 class="card-title">Editar Alumno</h4>
                <form id="formEditar">
                    <input type="hidden" id="editId" name="id_alumno">

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Matrícula</label>
                            <input type="text" id="editMatricula" name="matricula" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Nombre</label>
                            <input type="text" id="editNombre" name="nombre" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Apellido Paterno</label>
                            <input type="text" id="editApellidoP" name="apellido_paterno" class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label">Apellido Materno</label>
                            <input type="text" id="editApellidoM" name="apellido_materno" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Estatus</label>
                            <input type="text" id="editEstatus" name="estatus" class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label">Carrera</label>
                            <input type="text" id="editCarrera" name="carrera" class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label">Grupo</label>
                            <input type="text" id="editGrupo" name="grupo" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    <button type="button" id="cancelarEdicion" class="btn btn-secondary">Cancelar</button>
                </form>
            </div>
        </div>

        <!-- BOTON Y TABLA -->
    
        <table class="table table-bordered">
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
            <tbody id="alumnosBody"></tbody>
        </table>
    </div>

    <script>
    import DataTable from 'datatables.net-dt';
    
    document.addEventListener("DOMContentLoaded", () => {
        const btnCargar = document.getElementById("cargarAlumnos");
        const tbody = document.getElementById("alumnosBody");

        const formEdicion = document.getElementById("formularioEdicion");
        const formEditar = document.getElementById("formEditar");
        const btnCancelar = document.getElementById("cancelarEdicion");

        async function cargarAlumnos() {
            try {
                const response = await fetch("../../controllers/alumnoController.php?action=index");
                if (!response.ok) throw new Error("Error en la petición");
                const data = await response.json();

                tbody.innerHTML = "";
                data.forEach(a =>  {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${a.id_alumno}</td>
                        <td>${a.matricula ?? ''}</td>
                        <td>${a.nombre}</td>
                        <td>${a.apellido_paterno ?? ''}</td>
                        <td>${a.apellido_materno ?? ''}</td>
                        <td>${a.estatus ?? ''}</td>
                        <td>${a.carrera ?? ''}</td>
                        <td>${a.grupo ?? ''}</td>
                        <td>
                            <button class="btn btn-warning btn-sm editar" data-alumno='${JSON.stringify(a)}'>Editar</button>
                            <button class="btn btn-danger btn-sm eliminar" data-id="${a.id_alumno}">Eliminar</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                // Eliminar
                document.querySelectorAll(".eliminar").forEach(btn => {
                    btn.addEventListener("click", async (e) => {
                        const id = e.target.dataset.id;
                        if (confirm("¿Seguro que deseas eliminar este alumno?")) {
                            const resp = await fetch("../../controllers/alumnoController.php?action=delete&id=" + id);
                            if (resp.ok) cargarAlumnos();
                        }
                    });
                });

                // editar
                document.querySelectorAll(".editar").forEach(btn => {
                    btn.addEventListener("click", (e) => {
                        const alumno = JSON.parse(e.target.dataset.alumno);
                        document.getElementById("editId").value = alumno.id_alumno;
                        document.getElementById("editMatricula").value = alumno.matricula ?? "";
                        document.getElementById("editNombre").value = alumno.nombre ?? "";
                        document.getElementById("editApellidoP").value = alumno.apellido_paterno ?? "";
                        document.getElementById("editApellidoM").value = alumno.apellido_materno ?? "";
                        document.getElementById("editEstatus").value = alumno.estatus ?? "";
                        document.getElementById("editCarrera").value = alumno.carrera ?? "";
                        document.getElementById("editGrupo").value = alumno.grupo ?? "";

                        console.log(alumno);

                        formEdicion.classList.remove("d-none");
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                });

            } catch (error) {
                console.error(error);
            }
        }
        cargarAlumnos();

        formEditar.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(formEditar);
            const resp = await fetch("../../controllers/alumnoController.php?action=update", {
                method: "POST",
                body: formData
            });
            console.log([...formData]);
            
            console.log(resp);
            if (resp.ok) {
                alert("Alumno actualizado correctamente");
                formEdicion.classList.add("d-none");
                cargarAlumnos();
            } else {
                alert("Error al actualizar alumno");
            }
        });

        btnCancelar.addEventListener("click", () => {
            formEdicion.classList.add("d-none");
        });
    });
    </script>
</body>
</html>
