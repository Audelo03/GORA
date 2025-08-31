</main> </div> <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const btnToggle = document.getElementById('btn-toggle');
  const sidebar = document.getElementById('app-sidebar');
  const content = document.getElementById('app-content');

  btnToggle.addEventListener('click', function () {
    // Si es pantalla grande (desktop)
      sidebar.classList.toggle('collapsed');
     
  });
});
</script>

</body>
</html>