</main> </div> 
<script>

    function initTooltips() {
        var oldTooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        oldTooltipList.map(function (tooltipEl) {
            var tooltip = bootstrap.Tooltip.getInstance(tooltipEl);
            if (tooltip) {
                tooltip.dispose();
            }
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Ejecutar la función una vez cuando la página carga por primera vez
    document.addEventListener('DOMContentLoaded', function () {
        initTooltips();
        });
</script>
<script src="../node_modules/jquery/dist/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('btn-toggle-sidebar');
    const sidebar = document.getElementById('app-sidebar');
    const content = document.getElementById('app-content');

    if (toggleButton && sidebar && content) {
            toggleButton.addEventListener('click', function() {
  
            sidebar.classList.toggle('collapsed');
        
            
            content.classList.toggle('collapsed');
        });
    }
});
</script>
<script src="../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>


<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(event) {
            event.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Estás a punto de cerrar tu sesión actual.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = logoutLink.href;
                }
            });
        });
    }
});
</script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const mainElement = document.querySelector('main');

    if (mainElement) {
        mainElement.addEventListener('click', async function(e) {
            const pageLink = e.target.closest('.pagination[data-id-grupo] a.page-link');

            if (!pageLink) {
                return;
            }

            e.preventDefault();

            const parentLi = pageLink.parentElement;

            if (parentLi.classList.contains('disabled')) {
                return;
            }

            const paginationUl = pageLink.closest('.pagination');
            const id_grupo = paginationUl.dataset.idGrupo; // data-id-grupo se convierte en idGrupo
            const totalPages = parseInt(paginationUl.dataset.totalPages, 10);
            let currentPage = parseInt(paginationUl.dataset.currentPage, 10);
            const studentListContainer = document.getElementById(`lista-alumnos-${id_grupo}`);

            if (parentLi.dataset.role === 'prev') {
                currentPage--;
            } else if (parentLi.dataset.role === 'next') {
                currentPage++;
            }

            studentListContainer.innerHTML = '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

    

            const params = new URLSearchParams({
                action: 'load_students',
                id_grupo: id_grupo,
                page: currentPage
            });
            const url = `alumnos_paginados.php?${params.toString()}`;

            try {
                // Realizar la petición con fetch
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }

                const data = await response.json();

                studentListContainer.innerHTML = data.html;

                paginationUl.dataset.currentPage = currentPage;

                const pageIndicator = paginationUl.querySelector('li[data-role="page-indicator"] span.page-link');
                if (pageIndicator) {
                    pageIndicator.textContent = `${currentPage} de ${totalPages}`;
                }

                const prevButton = paginationUl.querySelector('li[data-role="prev"]');
                if (prevButton) {
                    prevButton.classList.toggle('disabled', currentPage === 1);
                }

                const nextButton = paginationUl.querySelector('li[data-role="next"]');
                if (nextButton) {
                    nextButton.classList.toggle('disabled', currentPage === totalPages);
                }
                if (typeof initTooltips === 'function') {
                    initTooltips();
                }

            } catch (error) {
                studentListContainer.innerHTML = '<div class="alert alert-danger">Error al cargar la lista de alumnos.</div>';
                console.error("Error de Fetch:", error);
            }
           
        });
    }
});
</script>

</body>
</html>