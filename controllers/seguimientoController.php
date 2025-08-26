<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Seguimiento.php";

class SeguimientoController {
    private $seguimiento;

    public function __construct($conn) {
        $this->seguimiento = new Seguimiento($conn);
    }


    public function listarPorAlumno($idAlumno) {
        return $this->seguimiento->getByAlumno($idAlumno);
    }


    public function crearSeguimiento($data) {
        $this->seguimiento->descripcion = $data["descripcion"];
        $this->seguimiento->estatus = $data["estatus"];
        $this->seguimiento->fecha_compromiso = $data["fecha_compromiso"];
        $this->seguimiento->usuarios_id_usuario_movimiento = $data["usuario_id"];
        $this->seguimiento->alumnos_alumno_id = $data["alumno_id"];

        return $this->seguimiento->create();
    }

    public function eliminarSeguimiento($id) {
        return $this->seguimiento->delete($id);
    }
}
?>
