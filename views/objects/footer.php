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
      // Este botón solo funciona en vista de escritorio
    
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
      
    });
  }

});
</script>

</body>
</html>