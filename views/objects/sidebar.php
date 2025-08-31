<?php
$current = basename($_SERVER['PHP_SELF']);
function active($files) {
    global $current;
    return in_array($current, (array)$files) ? 'active' : '';
}
?>

<nav id="app-sidebar" class="sidebar bg-dark text-white position-fixed h-100 p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <span class="fs-5 fw-bold">ITSADATA</span>
    <button id="btn-toggle" class="btn btn-sm btn-outline-light">
      <i class="bi bi-list"></i>
    </button>
  </div>

  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item">
      <a href="dashboard.php" class="nav-link text-white <?php echo active(['dashboard.php']); ?>">
        <i class="bi bi-speedometer2 me-2"></i> <span class="sidebar-text">Dashboard</span>
      </a>
    </li>
    <li>
      <a href="pages/alumnos.php" class="nav-link text-white <?php echo active(['alumnos.php']); ?>">
        <i class="bi bi-people me-2"></i> <span class="sidebar-text">Alumnos</span>
      </a>
    </li>
    <li>
      <a href="seguimientos.php" class="nav-link text-white <?php echo active(['seguimientos.php']); ?>">
        <i class="bi bi-journal-text me-2"></i> <span class="sidebar-text">Seguimientos</span>
      </a>
    </li>
    <li>
      <a href="perfil.php" class="nav-link text-white <?php echo active(['perfil.php']); ?>">
        <i class="bi bi-person-circle me-2"></i> <span class="sidebar-text">Perfil</span>
      </a>
    </li>
  </ul>

  <hr class="border-secondary">
  <div class="d-flex align-items-center">
    <i class="bi bi-person-badge me-2"></i>
    <div class="small lh-sm sidebar-text">
      <div class="fw-semibold"><?php echo $_SESSION['usuario_nombre'] ?? 'Usuario'; ?></div>
      <div class="text-secondary">Tutor</div>
    </div>
  </div>
  <a href="../controllers/logout.php" class="btn btn-outline-light btn-sm mt-3 w-100 sidebar-text"
     onclick="return confirm('¿Cerrar sesión?')">
    <i class="bi bi-box-arrow-right me-1"></i> <span>Cerrar sesión</span>
  </a>
</nav>
