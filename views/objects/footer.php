</main> </div> <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const btnToggle = document.getElementById('btn-toggle');
  const sidebar = document.getElementById('app-sidebar');
  const content = document.getElementById('app-content');

  btnToggle.addEventListener('click', function () {
    // Si es pantalla grande (desktop)
    if (window.innerWidth > 991.98) {
      sidebar.classList.toggle('collapsed');
      content.classList.toggle('collapsed');
    } else {
      // Si es móvil, se muestra/oculta como off-canvas
      sidebar.classList.toggle('show');
    }
  });
});
</script>

</body>
</html>