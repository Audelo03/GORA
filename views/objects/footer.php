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

    $(document).ready(function() {
    
    $('main').on('click', '.pagination[data-id-grupo] a.page-link', function(e) {
        e.preventDefault();

        const pageLink = $(this);
        const parentLi = pageLink.parent();
        
        if (parentLi.hasClass('disabled')) {
            return;
        }

        const paginationUl = pageLink.closest('.pagination');
        const id_grupo = paginationUl.data('id-grupo');
        const totalPages = parseInt(paginationUl.data('total-pages'));
        let currentPage = parseInt(paginationUl.data('current-page'));
        const studentListContainer = $('#lista-alumnos-' + id_grupo);

        // Determinar la página a la que ir
        if (parentLi.data('role') === 'prev') {
            currentPage--;
        } else if (parentLi.data('role') === 'next') {
            currentPage++;
        }

        studentListContainer.html('<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>');

        $.ajax({
            url: 'alumnos_paginados.php',
            type: 'GET',
            data: {
                action: 'load_students',
                id_grupo: id_grupo,
                page: currentPage
            },
            dataType: 'json',
            success: function(response) {
                studentListContainer.html(response.html);
                
                // Actualizar el número de página actual en el atributo data
                paginationUl.data('current-page', currentPage);

                // Actualizar el texto del indicador de página
                const pageIndicator = paginationUl.find('li[data-role="page-indicator"] span.page-link');
                if (pageIndicator.length) {
                    pageIndicator.text(currentPage + ' de ' + totalPages);
                }

                // Actualizar el estado del botón "Anterior"
                const prevButton = paginationUl.find('li[data-role="prev"]');
                prevButton.toggleClass('disabled', currentPage === 1);

                // Actualizar el estado del botón "Siguiente"
                const nextButton = paginationUl.find('li[data-role="next"]');
                nextButton.toggleClass('disabled', currentPage === totalPages);
                
                // Re-inicializar tooltips para los nuevos elementos si es necesario
                if (typeof initTooltips === 'function') {
                    initTooltips();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                studentListContainer.html('<div class="alert alert-danger">Error al cargar la lista de alumnos.</div>');
                console.error("Error de AJAX:", textStatus, errorThrown);
            }
        });
    });
});

</script>


</body>
</html>