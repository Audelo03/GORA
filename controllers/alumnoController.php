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

    public function obtenerAlumnosParaTutorLvl4($alumnoController, $conn, $usuario_id) {
    
            $alumnos = $alumnoController->alumno->getByTutorId($usuario_id);
            $grupos = [];
            foreach ($alumnos as $a) $grupos[$a['grupos_id_grupo']][] = $a;

            echo "<h2>Mis Alumnos</h2>";
            foreach ($grupos as $id_grupo => $alumnos) {
                $stmt = $conn->prepare("SELECT nombre FROM grupos WHERE id_grupo = :id_grupo");
                $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
                $stmt->execute();
                $grupo = $stmt->fetch(PDO::FETCH_ASSOC)['nombre'] ?? "Desconocido";

                echo "<h3 class='mt-3'>Grupo: {$grupo}</h3>";
                echo "<ul class='list-group mb-3'>";
                foreach ($alumnos as $a) {
                    echo "<li class='list-group-item'>{$a['nombre']} {$a['apellido_paterno']} {$a['apellido_materno']}</li>";
                }
                echo "</ul>";
            }
        
}

}
?>
