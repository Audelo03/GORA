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
                <div class="login-photo-placeholder mb-3">
                    <img src="/GORA/public/images/logo.png" alt="Logo GORA" class="login-logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="photo-placeholder" style="display: none;">
                        <i class="bi bi-image" style="font-size: 2.5rem; color: var(--bs-warning);"></i>
                        <small class="text-muted mt-2">Logo GORA</small>
                    </div>
                </div>
                <h2 class="h5 mb-4 fw-normal login-title">Iniciar Sesión</h2>
            </div>

            <div class="form-floating mb-3">
                <input type="email" 
                       name="email" 
                       class="form-control" 
                       id="floatingInput" 
                       placeholder="ejemplo@correo.com" 
                       autocomplete="email"
                       spellcheck="false"
                       required
                       aria-describedby="emailHelp">
                <label for="floatingInput"><i class="bi bi-envelope-fill me-2"></i>Correo electrónico</label>
                <div class="invalid-feedback" id="emailError"></div>
                <div class="valid-feedback" id="emailSuccess"></div>
            </div>
            
            <div class="form-floating mb-3 position-relative">
                <input type="password" 
                       name="password" 
                       class="form-control" 
                       id="floatingPassword" 
                       placeholder="Contraseña" 
                       autocomplete="current-password"
                       required
                       aria-describedby="passwordHelp">
                <label for="floatingPassword"><i class="bi bi-key-fill me-2"></i>Contraseña</label>
                <button type="button" 
                        class="btn btn-link position-absolute top-50 end-0 translate-middle-y pe-3 password-toggle" 
                        id="togglePassword" 
                        aria-label="Mostrar/ocultar contraseña"
                        style="border: none; background: none; z-index: 10;">
                    <i class="bi bi-eye" id="toggleIcon"></i>
                </button>
                <div class="invalid-feedback" id="passwordError"></div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('floatingInput');
            const passwordField = document.getElementById('floatingPassword');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const toggleIcon = document.getElementById('toggleIcon');
            const emailError = document.getElementById('emailError');
            const emailSuccess = document.getElementById('emailSuccess');
            const passwordError = document.getElementById('passwordError');

            // Email validation
            emailField.addEventListener('input', function() {
                const email = this.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email.length === 0) {
                    this.classList.remove('is-valid', 'is-invalid');
                    emailError.textContent = '';
                    emailSuccess.textContent = '';
                } else if (emailRegex.test(email)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    emailError.textContent = '';
                    emailSuccess.textContent = '✓ Formato de correo válido';
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    emailSuccess.textContent = '';
                    emailError.textContent = 'Formato de correo inválido';
                }
            });


            // Password visibility toggle
            togglePasswordBtn.addEventListener('click', function() {
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    toggleIcon.className = 'bi bi-eye-slash';
                    toggleIcon.setAttribute('aria-label', 'Ocultar contraseña');
                } else {
                    passwordField.type = 'password';
                    toggleIcon.className = 'bi bi-eye';
                    toggleIcon.setAttribute('aria-label', 'Mostrar contraseña');
                }
            });

            // Form submission validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const email = emailField.value.trim();
                const password = passwordField.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                let hasErrors = false;

                // Email validation
                if (!email || !emailRegex.test(email)) {
                    emailField.classList.add('is-invalid');
                    emailError.textContent = 'Por favor ingresa un correo electrónico válido';
                    hasErrors = true;
                }

                // Password validation
                if (!password) {
                    passwordField.classList.add('is-invalid');
                    passwordError.textContent = 'Por favor ingresa tu contraseña';
                    hasErrors = true;
                }

                if (hasErrors) {
                    e.preventDefault();
                    // Focus on first invalid field
                    const firstInvalid = document.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                    }
                }
            });

            // Clear validation on focus
            emailField.addEventListener('focus', function() {
                this.classList.remove('is-invalid');
                emailError.textContent = '';
            });

            passwordField.addEventListener('focus', function() {
                this.classList.remove('is-invalid');
                passwordError.textContent = '';
            });
        });
    </script>

</body>
</html>
