<?php
require_once __DIR__ . '/../models/Asistencia.php';

class AsistenciaController {
    private $conn;
    private $table_name = "asistencias";

    public $asistencia;

    public function __construct($db) {
            $this->conn = $db;
           $this->asistencia = new Asistencia($db);
    }

    public function guardarAsistenciasGrupo($id_grupo, $fecha, $ids_alumnos_presentes, $todos_los_alumnos_del_grupo) {
        $query_delete = "DELETE FROM " . $this->table_name . " WHERE id_grupo = :id_grupo AND fecha = :fecha";
        $stmt_delete = $this->conn->prepare($query_delete);
        $stmt_delete->bindParam(':id_grupo', $id_grupo);
        $stmt_delete->bindParam(':fecha', $fecha);
        $stmt_delete->execute();
 

        $query_insert = "INSERT INTO " . $this->table_name . " (id_alumno, id_grupo, fecha, estatus) VALUES (:id_alumno, :id_grupo, :fecha, :estatus)";
        $stmt_insert = $this->conn->prepare($query_insert);

        try {
            $this->conn->beginTransaction();
            foreach ($todos_los_alumnos_del_grupo as $alumno) {
                $estatus = in_array($alumno['id_alumno'], $ids_alumnos_presentes) ? 1 : 0;
                $stmt_insert->bindParam(':id_alumno', $alumno['id_alumno']);
                $stmt_insert->bindParam(':id_grupo', $id_grupo);
                $stmt_insert->bindParam(':fecha', $fecha);
                $stmt_insert->bindParam(':estatus', $estatus);
                $stmt_insert->execute();
            }
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            echo "Error al guardar asistencia: " . $e->getMessage();
            return false;
        }
    }

    public function obtenerListasPorGrupoId($id_grupo) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_grupo = :id_grupo GROUP BY fecha ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_grupo', $id_grupo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
