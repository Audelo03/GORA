<?php
include "../public/functions_util.php";
require_once __DIR__ . "/../controllers/authController.php";

$auth = new AuthController($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"]) && isset($_POST["password"])    ) {
    echo "<script>console.log('{$_POST["email"]}', '{$_POST["password"]}');</script>";
    $email = $_POST["email"];
    $email = eemail($email);
    $password = $_POST["password"];
    if ($auth->login($email, $password)) {
        if ($_SESSION["usuario_nivel"] == 4 || $_SESSION["usuario_nivel"] == 1)
            header("Location: dashboard.php");
        else
            header("Location: listas.php");
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
    <title>Login</title>
    
    <link rel="stylesheet" href="../vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../vendor/twbs/bootstrap-icons/font/bootstrap-icons.css">

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
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary h-100">
  
    
    <main class="form-signin w-100 m-auto bg-white rounded-3 shadow">
        <form method="POST">
            <div class="text-center mb-4">
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
            <button class="btn btn-danger w-100 py-2" type="submit">Entrar</button>

            <?php if(isset($error) ): ?>
                <div class="alert alert-danger d-flex align-items-center mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                </div>
            <?php endif; ?>

            <p class="mt-4 mb-3 text-body-secondary text-center">2025 ITSA</p>
        </form>
    </main>

</body>
</html>