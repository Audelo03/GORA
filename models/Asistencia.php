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

    public function registrarAsistencia($id_alumno, $fecha, $estatus) {
        $query = "INSERT INTO " . $this->table_name . " (id_alumno, fecha, estatus) VALUES (:id_alumno, :fecha, :estatus) ON DUPLICATE KEY UPDATE estatus = :estatus";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar los datos
        $id_alumno = htmlspecialchars(strip_tags($id_alumno));
        $fecha = htmlspecialchars(strip_tags($fecha));
        $estatus = htmlspecialchars(strip_tags($estatus));

        // Vincular los valores
        $stmt->bindParam(":id_alumno", $id_alumno);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->bindParam(":estatus", $estatus);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
     public function getAsistenciaPorFechaYGrupo($fecha, $id_grupo) {
        $query = "SELECT a.id_alumno, s.estatus 
                  FROM alumnos a
                  LEFT JOIN asistencias s ON a.id_alumno = s.id_alumno AND s.fecha = :fecha
                  WHERE a.grupos_id_grupo = :id_grupo";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->bindParam(":id_grupo", $id_grupo);
        $stmt->execute();
        
        $asistencias = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $asistencias[$row['id_alumno']] = $row['estatus'];
        }
        
        return $asistencias;
    }

    public function getHistorialAsistenciaPorGrupo($id_grupo) {
        $query = "SELECT 
                    s.fecha,
                    s.estatus,
                    a.id_alumno,
                    CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as nombre_completo
                  FROM asistencias s
                  JOIN alumnos a ON s.id_alumno = a.id_alumno
                  WHERE a.grupos_id_grupo = :id_grupo
                  ORDER BY s.fecha DESC, a.apellido_paterno, a.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_grupo", $id_grupo, PDO::PARAM_INT);
        $stmt->execute();

        $historial = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $historial[$row['fecha']][] = $row;
        }

        return $historial;
    }
}
?>