<?php
// Remove session_start() as it's already started in index.php
// functions_util.php is already included in index.php
require_once __DIR__ . "/../controllers/authController.php";

$auth = new AuthController($conn);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Si ya tiene un error guardado
if (isset($_SESSION['error_message'])) {
    $iniciado = true;
    include __DIR__ . "/logout.php";    
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Petición inválida.";
    } else {
        $email = trim($_POST["email"] ?? "");
        $password = trim($_POST["password"] ?? "");

        if (empty($email) || empty($password)) {
            $error = "Debes llenar todos los campos.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Formato de correo inválido.";
        } else {
            // Sanitizar email
            $email = eemail($email);

            // Intento de login
            if ($auth->login($email, $password)) {
                // Prevenir fijación de sesión
                session_regenerate_id(true);

                // Verificación de rol
                if (isset($_SESSION["usuario_nivel"])) {
                    if ($_SESSION["usuario_nivel"] == 4 || $_SESSION["usuario_nivel"] == 1) {
                        header("Location: /GORA/dashboard");
                    } else {
                        header("Location: /GORA/listas");
                    }
                    exit;
                } else {
                    $error = "No se pudo determinar el nivel de usuario.";
                }
            } else {
                // Manejo de intentos fallidos
                if (!isset($_SESSION['login_attempts'])) {
                    $_SESSION['login_attempts'] = 0;
                }
                $_SESSION['login_attempts']++;

                if ($_SESSION['login_attempts'] > 5) {
                    $error = "Demasiados intentos fallidos. Intenta más tarde.";
                } else {
                    $error = "Correo o contraseña incorrectos.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
      .form-signin {
        max-width: 400px;
        padding: 2rem;
      }
      html, body {
        height: 100%;
      }
      .bg-image {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.3;
        z-index: -1;
      }
    </style>
    <link href="/GORA/public/css/theme.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center py-4 h-100 login-page">
  
    <main class="form-signin w-100 m-auto login-card rounded-3">
        <form method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            <?php unset($_SESSION['error_message']); endif; ?>

            <div class="text-center mb-4">
                <p class="h3 mb-3 font-weight-bold login-brand">GORA</p>
                <h2 class="h5 mb-4 fw-normal login-title">Iniciar Sesión</h2>
            </div>

            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="floatingInput" placeholder="ejemplo@correo.com" required>
                <label for="floatingInput"><i class="bi bi-envelope-fill me-2"></i>Correo electrónico</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Contraseña" required>
                <label for="floatingPassword"><i class="bi bi-key-fill me-2"></i>Contraseña</label>
            </div>
            <button class="btn login-submit w-100 py-2" type="submit">Entrar</button>

            <?php if(!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <?= htmlspecialchars($error) ?>
                    </div>
                </div>
            <?php endif; ?>

            <p class="mt-4 mb-3 text-center login-footer">2025 ITSA</p>
        </form>
    </main>

</body>
</html>
