<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Alumno.php";

class AlumnoController {
    public $alumno;

    public function __construct($conn) {
        $this->alumno = new Alumno($conn);
    }

    public function listarAlumnos() {
        return $this->alumno->getAll();
    }

    public function verAlumno($id) {
        return $this->alumno->getById($id);
    }

    public function crearAlumno($data) {
        $this->alumno->matricula = $data["matricula"];
        $this->alumno->nombre = $data["nombre"];
        $this->alumno->apellido_paterno = $data["apellido_paterno"];
        $this->alumno->apellido_materno = $data["apellido_materno"];
        $this->alumno->estatus = $data["estatus"];
        $this->alumno->tutores_id_tutor = $data["tutor"];
        $this->alumno->carreras_id_carrera = $data["carrera"];
        $this->alumno->grupos_id_grupo = $data["grupo"];

        return $this->alumno->create();
    }

    public function actualizarAlumno($id, $data) {
        $this->alumno->id_alumno = $id;
        $this->alumno->matricula = $data["matricula"];
        $this->alumno->nombre = $data["nombre"];
        $this->alumno->apellido_paterno = $data["apellido_paterno"];
        $this->alumno->apellido_materno = $data["apellido_materno"];
        $this->alumno->estatus = $data["estatus"];
        $this->alumno->tutores_id_tutor = $data["tutor"];
        $this->alumno->carreras_id_carrera = $data["carrera"];
        $this->alumno->grupos_id_grupo = $data["grupo"];

        return $this->alumno->update();
    }

    public function eliminarAlumno($id) {
        return $this->alumno->delete($id);
    }

}
?>
