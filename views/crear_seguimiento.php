<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../controllers/authController.php";
require_once __DIR__ . "/../controllers/alumnoController.php";
require_once __DIR__ . "/../controllers/seguimientoController.php";

$page_title = "Crear Seguimiento";
include 'objects/header.php';
include 'objects/navbar.php';


// --- AUTENTICACIÓN ---
$auth = new AuthController($conn);
$auth->checkAuth();

$id_alumno = filter_input(INPUT_GET, 'id_alumno', FILTER_VALIDATE_INT);
if (!$id_alumno) {
    header("Location: listas.php?error=invalid_id");
    exit;
}

$alumnoController = new AlumnoController($conn);
$alumno = $alumnoController->obtenerAlumnoPorId($id_alumno);

if (!$alumno) {
    header("Location: listas.php?error=alumno_not_found");
    exit;
}

$errors = [];
$descripcion = "";
$estatus = 1;
$fecha_movimiento = date('Y-m-d');
$fecha_compromiso = "";

// --- PROCESAR FORMULARIO ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estatus = filter_input(INPUT_POST, 'estatus', FILTER_VALIDATE_INT);
    $fecha_movimiento = $_POST['fecha_movimiento'] ?? date('Y-m-d');
    $fecha_compromiso = $_POST['fecha_compromiso'] ?? null;
    $id_usuario_movimiento = $_SESSION['usuario_id'] ?? null;

    if (empty($descripcion)) {
        $errors['descripcion'] = "La descripción es obligatoria.";
    }

    if ($estatus === false || !in_array($estatus, [1, 2, 3])) {
        $errors['estatus'] = "Selecciona un estatus válido.";
    }

    if (empty($errors)) {
        $seguimientoController = new SeguimientoController($conn);
        $resultado = $seguimientoController->crear(
            $id_alumno,
            $id_usuario_movimiento,
            $descripcion,
            (int)$estatus,
            $fecha_movimiento,
            $fecha_compromiso
        );

        if ($resultado) {
            header("Location: ver_seguimientos.php?id_alumno=$id_alumno&success=created");
            exit;
        } else {
            $errors['general'] = "Hubo un error al guardar el seguimiento.";
        }
    }
}


?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow border-0">
                <div class="card-body p-4">

                    <h3 class="mb-4">Nuevo Seguimiento</h3>

                    <div class="mb-4 p-3 bg-light rounded">
                        <h5 class="mb-1"><?= htmlspecialchars($alumno['nombre_completo']) ?></h5>
                        <small class="text-muted">Matrícula: <?= htmlspecialchars($alumno['matricula']) ?></small>
                    </div>

                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control <?= isset($errors['descripcion']) ? 'is-invalid' : '' ?>"
                                      id="descripcion" name="descripcion" rows="4"
                                      placeholder="Describe el seguimiento..."><?= htmlspecialchars($descripcion) ?></textarea>
                            <?php if (isset($errors['descripcion'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['descripcion']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="estatus" class="form-label">Estatus</label>
                            <select class="form-select <?= isset($errors['estatus']) ? 'is-invalid' : '' ?>"
                                    id="estatus" name="estatus">
                                <option value="1" <?= $estatus == 1 ? 'selected' : '' ?>>Abierto</option>
                                <option value="2" <?= $estatus == 2 ? 'selected' : '' ?>>En Progreso</option>
                                <option value="3" <?= $estatus == 3 ? 'selected' : '' ?>>Cerrado</option>
                            </select>
                            <?php if (isset($errors['estatus'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['estatus']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_movimiento" class="form-label">Fecha Seguimiento</label>
                                <input type="date" class="form-control" id="fecha_movimiento"
                                       name="fecha_movimiento" value="<?= htmlspecialchars($fecha_movimiento) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_compromiso" class="form-label">Fecha Compromiso</label>
                                <input type="date" class="form-control" id="fecha_compromiso"
                                       name="fecha_compromiso" value="<?= htmlspecialchars($fecha_compromiso) ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="ver_seguimientos.php?id_alumno=<?= htmlspecialchars($id_alumno) ?>"
                               class="btn btn-outline-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'objects/footer.php'; ?>
