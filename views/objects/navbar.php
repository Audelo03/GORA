<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 shadow-sm">
    <div class="container-fluid">
    

        <a class="navbar-brand" href="#"><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></a>
        
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-1"></i>
                    <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                    <li><a class="dropdown-item" href="#">Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="login.php" onclick="return confirm('¿Estás seguro de que quieres cerrar sesión?')">Cerrar Sesión</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>