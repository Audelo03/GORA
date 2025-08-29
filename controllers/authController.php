<?php
if (!isset($_SESSION))
    session_start();
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Usuario.php";

class AuthController {
    public $usuario;

    public function __construct($conn) {
        $this->usuario = new Usuario($conn);
    }

    public function login($email, $password) {
        $user = $this->usuario->getByEmail($email);
       // if ($user && password_verify($password, $user["password"])) {
        if ($user && $password== $user["password"]) {
        $_SESSION["usuario_id"] = $user["id_usuario"];
            $_SESSION["usuario_nombre"] = $user["nombre"];
            $_SESSION["usuario_nivel"] = $user["niveles_usuarios_id_nivel_usuario"];
            $_SESSION["usuario_apellido_paterno"] = $user["apellido_paterno"];
            $_SESSION["usuario_apellido_materno"] = $user["apellido_materno"];
            return true;
        }
        return false;
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ../views/login.php");
        exit;
    }

    public function checkAuth() {
        if (!isset($_SESSION["usuario_id"])) {
            header("Location: ../views/login.php");
            exit;
        }
    }
}
?>
