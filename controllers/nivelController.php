<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Usuario.php";

class nivelController {
    public $usuario;

    public function __construct($conn) {
        $this->usuario = new Usuario($conn);
    }
    //ADMIN
    public function fetchLvl1($alumnoController, $conn) {
        return $this->usuario->obtenerAlumnosParaAdminLv1($alumnoController, $conn);
    }
    //COORDINADOR
    public function fetchLvl2($alumnoController, $conn, $auth, $usuario_id) {
        return $this->usuario->obtenerAlumnosParaCoordinadorLvl2($alumnoController, $conn, $auth, $usuario_id);
    }

    //TUTOR
    public function fetchLvl3($alumnoController, $conn, $usuario_id, $auth) {
        return $this->usuario->obtenerAlumnosParaTutorLvl3($alumnoController, $conn, $usuario_id, $auth);
    }

    //DIRECTOR
    public function fetchLvl4($alumnoController, $conn) {
        return $this->usuario->obtenerAlumnosParaDirLvl4($alumnoController, $conn);
    }
}
?>
