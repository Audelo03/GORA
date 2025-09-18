<?php
// Obtener la página actual del sistema de enrutamiento
$current_page = '';
$request_uri = $_SERVER['REQUEST_URI'];

// Remover la base del proyecto y parámetros
$base_path = '/GORA/';
if (strpos($request_uri, $base_path) === 0) {
    $path = substr($request_uri, strlen($base_path));
    $path = explode('?', $path)[0]; // Remover parámetros GET
    $path = explode('#', $path)[0]; // Remover fragmentos
    $current_page = trim($path, '/');
}

if (empty($current_page)) {
    $current_page = 'dashboard';
}



if (!function_exists('active')) {
  /**
   * Función para determinar si un enlace del sidebar está activo
   * @param array|string $pages - Páginas a verificar
   * @return string - Clase CSS 'active' si coincide, cadena vacía si no
   */
  function active($pages) {
    global $current_page;
    $pages_array = (array)$pages;
    
    // Verificar si la página actual coincide con alguna de las páginas especificadas
    foreach ($pages_array as $page) {
      if ($current_page === $page || $current_page === $page . '.php') {
        return 'active';
      }
    }
    return '';
  }
}
if (isset($_SESSION))
  $nivel = (int)$_SESSION["usuario_nivel"];

// Debug: Mostrar información del usuario (remover en producción)
if (isset($_SESSION["usuario_nivel"])) {
  echo "<!-- DEBUG: Nivel de usuario: " . $_SESSION["usuario_nivel"] . " (tipo: " . gettype($_SESSION["usuario_nivel"]) . ") -->";
  echo "<!-- DEBUG: Nivel convertido: " . $nivel . " (tipo: " . gettype($nivel) . ") -->";
}

if (!isset($modificacion_ruta)) {
  $modificacion_ruta = "";
}
?>

<nav id="app-sidebar" class="sidebar bg-dark text-white position-fixed h-100 p-3 collapsed">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <span class="fs-5 fw-bold">GORA</span>
    <button id="btn-toggle-sidebar" class="btn btn-sm btn-outline-light" >
      <i class="bi bi-list"></i>
    </button>
  </div>

  <div id="slow" class="slow">
    <ul class="nav nav-pills flex-column mb-auto">

      <?php if ($nivel == 1): ?>
      <li class="nav-item">
        <a href="/GORA/dashboard" class="nav-link text-white <?= active(['dashboard']); ?>" 
           <?= active(['dashboard']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Panel Principal"' ?>>
          <i class="bi bi-speedometer2 me-2"></i> <span class="sidebar-text">Dashboard</span>
        </a>
      </li>
      <?php endif; ?>

      <li>
        <a href="/GORA/listas" class="nav-link text-white <?= active(['listas']); ?>" 
           <?= active(['listas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Lista de Alumnos"' ?>>
          <i class="bi bi-people me-2"></i> <span class="sidebar-text">Alumnos</span>
        </a>
      </li>

      <?php if ($nivel == 1): ?>
      <li>
        <a href="/GORA/estadisticas" class="nav-link text-white <?= active(['estadisticas']); ?>" 
           <?= active(['estadisticas']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Estadísticas y Reportes"' ?>>
          <i class="bi bi-bar-chart-fill me-2"></i> <span class="sidebar-text">Estadísticas</span>
        </a>
      </li>
      <?php endif; ?>

      <li>
        <a href="/GORA/seguimientos" class="nav-link text-white <?= active(['seguimientos']); ?>" 
           <?= active(['seguimientos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Seguimientos"' ?>>
          <i class="bi bi-journal-text me-2"></i> <span class="sidebar-text">Seguimientos</span>
        </a>
      </li>

       <?php if ($nivel == 1): ?>

      <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2">Gestión</h6>

      <li>
        <a href="/GORA/usuarios" class="nav-link text-white <?= active(['usuarios']); ?>" 
           <?= active(['usuarios']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Usuarios"' ?>>
          <i class="bi bi-person-vcard me-2"></i> <span class="sidebar-text">Usuarios</span>
        </a>
      </li>

      <li>
        <a href="/GORA/alumnos" class="nav-link text-white <?= active(['alumnos']); ?>" 
           <?= active(['alumnos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Alumnos"' ?>>
          <i class="bi bi-person-workspace me-2"></i> <span class="sidebar-text">Alumnos</span>
        </a>
      </li>

      <li>
        <a href="/GORA/carreras" class="nav-link text-white <?= active(['carreras']); ?>" 
           <?= active(['carreras']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Carreras"' ?>>
          <i class="bi bi-book me-2"></i> <span class="sidebar-text">Carreras</span>
        </a>
      </li>

      <li>
        <a href="/GORA/grupos" class="nav-link text-white <?= active(['grupos']); ?>" 
           <?= active(['grupos']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Grupos"' ?>>
          <i class="bi bi-person-video2 me-2"></i> <span class="sidebar-text">Grupos</span>
        </a>
      </li>

      <li>
        <a href="/GORA/modalidades" class="nav-link text-white <?= active(['modalidades']); ?>" 
           <?= active(['modalidades']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de Modalidades"' ?>>
          <i class="bi bi-person-video3 me-2"></i> <span class="sidebar-text">Modalidades</span>
        </a>
      </li>

      <li>
        <a href="/GORA/tipo-seguimiento" class="nav-link text-white <?= active(['tipo-seguimiento', 'tipo_seguimiento']); ?>" 
           <?= active(['tipo-seguimiento', 'tipo_seguimiento']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión de tipos de seguimientos"' ?>>
          <i class="bi bi-person-rolodex me-2"></i> <span class="sidebar-text">Tipo de Seguimientos</span>
        </a>
      </li>
       <?php endif; ?>

      <h6 class="text-uppercase text-secondary fw-bold small mt-3 mb-2">Tú</h6>

      <li>
        <a href="/GORA/profile" class="nav-link text-white <?= active(['profile']); ?>" 
           <?= active(['profile']) ? '' : 'data-bs-toggle="tooltip" data-bs-placement="right" title="Mi Perfil de Usuario"' ?>>
          <i class="bi bi-person-circle me-2"></i> <span class="sidebar-text">Perfil</span>
        </a>
      </li>

    </ul>
  </div>
</nav>
