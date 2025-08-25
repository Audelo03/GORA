<?php
session_start();
require "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["usuario_id"] = $user["id_usuario"];
        $_SESSION["usuario_nombre"] = $user["nombre"];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Plataforma Universidad</title>
    
    <link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../vendor/twbs/bootstrap-icons/font/bootstrap-icons.css">

    <style>
      .form-signin {
        max-width: 400px;
        padding: 2rem;
      }
    </style>
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary h-100">
    
    <main class="form-signin w-100 m-auto bg-white rounded-3 shadow">
        <form method="POST">
            <div class="text-center mb-4">
                <i class="bi bi-mortarboard-fill" style="font-size: 3rem; color: var(--bs-primary);"></i>
                <h1 class="h3 mb-3 fw-normal">Iniciar Sesión</h1>
            </div>

            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="floatingInput" placeholder="ejemplo@correo.com" required>
                <label for="floatingInput"><i class="bi bi-envelope-fill me-2"></i>Correo electrónico</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Contraseña" required>
                <label for="floatingPassword"><i class="bi bi-key-fill me-2"></i>Contraseña</label>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">
                        Recordar sesión
                    </label>
                </div>
                <a href="#" class="small text-decoration-none">¿Olvidaste tu contraseña?</a>
            </div>

            <button class="btn btn-primary w-100 py-2" type="submit">Entrar</button>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger d-flex align-items-center mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                </div>
            <?php endif; ?>

            <p class="mt-4 mb-3 text-body-secondary text-center">&copy; 2025 ITSA</p>
        </form>
    </main>

</body>
</html>