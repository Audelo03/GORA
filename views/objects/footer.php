</main> </div> <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Botón para encoger/expandir el sidebar en escritorio
const btnToggleSidebar = document.getElementById('btn-toggle-sidebar');

  
  const sidebar = document.getElementById('app-sidebar');
  const content = document.getElementById('app-content');
  sidebar.classList.toggle('collapsed');
  content.classList.toggle('collapsed');

  if (btnToggleSidebar) {

    btnToggleSidebar.addEventListener('click', function () {

        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
      
    });
  }

});
</script>
<script src="../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script>

    function initTooltips() {
        // Eliminar instancias de tooltips anteriores para evitar conflictos
        var oldTooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        oldTooltipList.map(function (tooltipEl) {
            var tooltip = bootstrap.Tooltip.getInstance(tooltipEl);
            if (tooltip) {
                tooltip.dispose();
            }
        });

        // Inicializar todos los tooltips que se encuentren en la página
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
</body>
</html>