</main> </div> <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Botón para encoger/expandir el sidebar en escritorio
const btnToggleSidebar = document.getElementById('btn-toggle-sidebar');
  // Botón para mostrar/ocultar el sidebar en móvil
  const btnToggleNavbar = document.getElementById('btn-toggle-navbar');
  
  const sidebar = document.getElementById('app-sidebar');
  const content = document.getElementById('app-content');

  if (btnToggleSidebar) {
    btnToggleSidebar.addEventListener('click', function () {
      // Este botón solo funciona en vista de escritorio
      if (window.innerWidth > 991.98) {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
      }
    });
  }

  if (btnToggleNavbar) {
      btnToggleNavbar.addEventListener('click', function() {
        // Este botón solo funciona en vista móvil y activa el modo "off-canvas"
        sidebar.classList.toggle('show');
      });
  }
});
</script>

</body>
</html>