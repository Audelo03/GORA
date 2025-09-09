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

if (!isset($modificacion_ruta)) {
  $modificacion_ruta = "";
}
?>

<nav id="app-sidebar" class="sidebar bg-dark text-white position-fixed h-100 p-3 collapsed">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <span class="fs-5 fw-bold">GORA</span>
    <button id="btn-toggle-sidebar" class="btn btn-sm btn-outline-light">
      <i class="bi bi-list"></i>
    </button>
  </div>

  <div id="slow" class="slow">
    <ul class="nav nav-pills flex-column mb-auto">

      <?php if ($nivel === 1 || $nivel === 4): ?>
      <li class="nav-item">
        <a href="/ITSAdata/dashboard" class="nav-link text-white <?= active(['dashboard.php']); ?>">
          <i class="bi bi-speedometer2 me-2"></i> <span class="sidebar-text">Dashboard</span>
        </a>
      </li>
      <?php endif; ?>

      <li>
        <a href="/ITSAdata/listas" class="nav-link text-white <?= active(['listas.php']); ?>">
          <i class="bi bi-people me-2"></i> <span class="sidebar-text">Alumnos</span>
        </a>
      </li>

      <?php if ($nivel === 1 || $nivel === 4): ?>
      <li>
        <a href="/ITSAdata/estadisticas" class="nav-link text-white <?= active(['estadisticas.php']); ?>">
          <i class="bi bi-bar-chart-fill me-2"></i> <span class="sidebar-text">Estadísticas</span>
        </a>
      </li>
      <?php endif; ?>

      <li>
        <a href="/ITSAdata/seguimientos" class="nav-link text-white <?= active(['seguimientos.php']); ?>">
          <i class="bi bi-journal-text me-2"></i> <span class="sidebar-text">Seguimientos</span>
        </a>
      </li>

       <?php if ($nivel === 1 || $nivel === 4): ?>

      <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2">Gestión</h6>

      <li>
        <a href="/ITSAdata/usuarios" class="nav-link text-white <?= active(['usuarios.php']); ?>">
          <i class="bi bi-person-vcard me-2"></i> <span class="sidebar-text">Usuarios</span>
        </a>
      </li>

      <li>
        <a href="/ITSAdata/alumnos" class="nav-link text-white <?= active(['alumnos.php']); ?>">
          <i class="bi bi-person-workspace me-2"></i> <span class="sidebar-text">Alumnos</span>
        </a>
      </li>

      <li>
        <a href="/ITSAdata/carreras" class="nav-link text-white <?= active(['carreras.php']); ?>">
          <i class="bi bi-book me-2"></i> <span class="sidebar-text">Carreras</span>
        </a>
      </li>

      <li>
        <a href="/ITSAdata/grupos" class="nav-link text-white <?= active(['grupos.php']); ?>">
          <i class="bi bi-person-video2 me-2"></i> <span class="sidebar-text">Grupos</span>
        </a>
      </li>

      <li>
        <a href="/ITSAdata/modalidades" class="nav-link text-white <?= active(['modalidades.php']); ?>">
          <i class="bi bi-person-video3 me-2"></i> <span class="sidebar-text">Modalidades</span>
        </a>
      </li>

      <li>
        <a href="/ITSAdata/tipo-seguimiento" class="nav-link text-white <?= active(['tipo_seguimiento.php']); ?>">
          <i class="bi bi-person-rolodex me-2"></i> <span class="sidebar-text">Tipo de Seguimientos</span>
        </a>
      </li>
       <?php endif; ?>

      <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2">Tú</h6>

      <li>
        <a href="/ITSAdata/profile" class="nav-link text-white <?= active(['profile.php']); ?>">
          <i class="bi bi-person-circle me-2"></i> <span class="sidebar-text">Perfil</span>
        </a>
      </li>

    </ul>
  </div>
</nav>
