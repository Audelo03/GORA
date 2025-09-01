<?php
class Asistencia {

    private $conn;
    private $table_name = "asistencias";

    public $id;
    public $id_alumno;
    public $id_grupo;
    public $fecha;
    public $estatus;
    public $fecha_registro;
    public function __construct($db) {
        $this->conn = $db;
    }

}
?>