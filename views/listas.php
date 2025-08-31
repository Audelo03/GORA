<?php 
require_once __DIR__ . '/../controllers/authController.php';
$auth = new AuthController($conn);
$auth->checkAuth();

$niveles_autorizados = [1 => "Admin", 2 => "Coordinador", 3 => "Tutor", 4 => "Director"];
$nivel_nombre  = $niveles_autorizados[$_SESSION['usuario_nivel']] ?? "Desconocido"; 
$nombre = $_SESSION['usuario_nombre'] . ' ' . $_SESSION['usuario_apellido_paterno'] . ' ' . $_SESSION['usuario_apellido_materno'];

$page_title = "Dashboard de Alumnos";
include 'objects/header.php';
include "objects/navbar.php";
?>

<div class="container mt-5">
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">Lista de Alumnos</h1>
                <p class="mb-0 text-muted">Nivel: <strong><?= htmlspecialchars($nivel_nombre) ?></strong></p>
                <p class="mb-0 text-muted">Usuario: <strong><?= htmlspecialchars($nombre) ?></strong></p>
            </div>
            <a href="login.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="buscador" class="form-control" placeholder="Buscar por alumno, matrícula, grupo o carrera...">
            </div>
        </div>
    </div>

    <div id="contenedor-alumnos"></div>

    <nav class="mt-4" aria-label="Paginación de alumnos">
        <ul class="pagination justify-content-center" id="paginacion-controles"></ul>
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buscadorInput = document.getElementById('buscador');
    const contenedorAlumnos = document.getElementById('contenedor-alumnos');
    const paginacionControles = document.getElementById('paginacion-controles');
    let timeout = null;

    async function cargarAlumnos(page = 1, termino = '') {
        contenedorAlumnos.classList.add('loading');
        contenedorAlumnos.innerHTML = `<div class="d-flex justify-content-center p-5"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>`;

        try {
            const response = await fetch(`alumnos_paginados.php?page=${page}&termino=${encodeURIComponent(termino)}`);
            if (!response.ok) throw new Error('Error en la respuesta del servidor.');
            
            const data = await response.json();
            contenedorAlumnos.innerHTML = data.html;
            actualizarPaginacion(data.currentPage, data.totalPages, termino);

        } catch (error) {
            console.error('Error al cargar alumnos:', error);
            contenedorAlumnos.innerHTML = `<div class="alert alert-danger">No se pudieron cargar los datos. Intente de nuevo más tarde.</div>`;
        } finally {
            contenedorAlumnos.classList.remove('loading');
        }
    }

    function actualizarPaginacion(currentPage, totalPages, termino) {
        paginacionControles.innerHTML = '';
        if (totalPages <= 1) return;

        paginacionControles.innerHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" data-termino="${termino}">Anterior</a>
            </li>`;

        for (let i = 1; i <= totalPages; i++) {
            paginacionControles.innerHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}" data-termino="${termino}">${i}</a>
                </li>`;
        }

        paginacionControles.innerHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}" data-termino="${termino}">Siguiente</a>
            </li>`;
    }

    paginacionControles.addEventListener('click', function(e) {
        e.preventDefault();
        const target = e.target.closest('a');
        if (target && !target.parentElement.classList.contains('disabled')) {
            const page = parseInt(target.getAttribute('data-page'));
            const termino = target.getAttribute('data-termino');
            cargarAlumnos(page, termino);
        }
    });

    buscadorInput.addEventListener('keyup', function (e) {
        clearTimeout(timeout);
        const termino = e.target.value.trim();
        timeout = setTimeout(() => {
            cargarAlumnos(1, termino);
        }, 500);
    });

    cargarAlumnos(1); 
});
</script>

<?php include 'objects/footer.php'; ?>
