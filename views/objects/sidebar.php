<?php
$current = basename($_SERVER['PHP_SELF']);
if (!function_exists('active')) {
  function active($files) {
    global $current;
    return in_array($current, (array)$files) ? 'active' : '';
  }
}
if (isset($_SESSION))
  $nivel = $_SESSION["usuario_nivel"];




?>

<nav id="app-sidebar" class="sidebar bg-dark text-white position-fixed h-100 p-3 collapsed">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <span class="fs-5 fw-bold">ITSADATA</span>
    <button id="btn-toggle-sidebar" class="btn btn-sm btn-outline-light">
  <i class="bi bi-list"></i>
</button>
  </div>

  <div id = "slow" class = "slow">

  <ul class="nav nav-pills flex-column mb-auto">
    <?php if ($nivel === 1 || $nivel === 4): ?>
    <li class="nav-item">
      <a href="dashboard.php" class="nav-link text-white <?php echo active(['dashboard.php']); ?>">
        <i class="bi bi-speedometer2 me-2"></i> <span class="sidebar-text">Dashboard</span>
      </a>
    </li>
    <?php endif; ?>
    <li>
      <a href="listas.php" class="nav-link text-white <?php echo active(['listas.php']); ?>">
        <i class="bi bi-people me-2"></i> <span class="sidebar-text">Alumnos</span>
      </a>
    </li>
    <li>
      <?php if ($nivel === 1 || $nivel === 4): ?>
      <a href="estadisticas.php" class="nav-link text-white <?php echo active(['estadisticas.php']); ?>">
        <i class="bi bi-bar-chart-fill me-2"></i> <span class="sidebar-text">Estadísticas</span>
      </a>
      <?php endif; ?>
    </li>
      <a href="seguimientos.php" class="nav-link text-white <?php echo active(['seguimientos.php']); ?>">
        <i class="bi bi-journal-text me-2"></i> <span class="sidebar-text">Seguimientos</span>
      </a>
    </li>
    <li>
      <a href="profile.php" class="nav-link text-white <?php echo active(['profile.php']); ?>">
        <i class="bi bi-person-circle me-2"></i> <span class="sidebar-text">Perfil</span>
      </a>
    </li>
    
  </ul>
  </div>


</nav>
