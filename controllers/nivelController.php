<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../controllers/alumnoController.php";

class nivelController {
    public $alumnoController;

    public function __construct($conn) {
        $this->alumnoController = new AlumnoController($conn);
    }
    //ADMIN
    public function fetchLvl1($conn,$auth) {
        return $this->alumnoController->obtenerAlumnosParaAdminLv1($conn,$auth);
    }
    //COORDINADOR
    public function fetchLvl2($conn, $auth, $usuario_id) {
        return $this->alumnoController->obtenerAlumnosParaCoordinadorLvl2($conn, $auth, $usuario_id,null);
    }

    //TUTOR
    public function fetchLvl3($conn, $auth) {
        return $this->alumnoController->obtenerAlumnosParaTutorLvl3($conn, $auth);
    }

    //DIRECTOR
    public function fetchLvl4($conn,$auth) {
        return $this->alumnoController->obtenerAlumnosParaDirLvl4($conn,$auth);
    }
}
?>
