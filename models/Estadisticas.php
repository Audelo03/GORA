<?php
class Estadisticas {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function totalAlumnos() {
        $sql = "SELECT COUNT(id_alumno) as total FROM alumnos";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function totalCarreras() {
        $sql = "SELECT COUNT(id_carrera) as total FROM carreras";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function totalGrupos() {
        $sql = "SELECT COUNT(id_grupo) as total FROM grupos";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function alumnosPorCarrera() {
        $sql = "SELECT c.nombre, COUNT(a.id_alumno) as total
                FROM carreras c
                LEFT JOIN alumnos a ON c.id_carrera = a.carreras_id_carrera
                GROUP BY c.nombre ORDER BY total DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el número de alumnos agrupados por su estatus.
     */
    public function alumnosPorEstatus() {
        $sql = "SELECT CASE 
                        WHEN estatus = 1 THEN 'Activo'
                        WHEN estatus = 0 THEN 'Inactivo'
                        WHEN estatus = 2 THEN 'Egresado'
                        WHEN estatus = 3 THEN 'Baja'
                        ELSE 'Desconocido'
                    END as estatus_nombre, 
                    COUNT(id_alumno) as total
                FROM alumnos
                GROUP BY estatus";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el número de grupos agrupados por modalidad.
     */
    public function gruposPorModalidad() {
        $sql = "SELECT m.nombre, COUNT(g.id_grupo) as total
                FROM modalidades m
                LEFT JOIN grupos g ON m.id_modalidad = g.modalidades_id_modalidad
                GROUP BY m.nombre ORDER BY total DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calcula la tasa de asistencia general.
     */
    public function tasaDeAsistenciaGeneral() {
        $sql = "SELECT 
                    (SUM(CASE WHEN estatus = 1 THEN 1 ELSE 0 END) / COUNT(id)) * 100 as porcentaje
                FROM asistencias";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        // Retorna el porcentaje redondeado a 2 decimales, o 0 si no hay datos.
        return $resultado ? round($resultado['porcentaje'], 2) : 0;
    }

    /**
     * Obtiene el número de seguimientos agrupados por su estatus.
     */
    public function seguimientosPorEstatus() {
        $sql = "SELECT CASE 
                        WHEN estatus = 1 THEN 'Abierto'
                        WHEN estatus = 2 THEN 'En Progreso'
                        WHEN estatus = 3 THEN 'Cerrado'
                        ELSE 'Desconocido'
                    END as estatus_nombre, 
                    COUNT(id_seguimiento) as total
                FROM seguimientos
                GROUP BY estatus";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el número de seguimientos agrupados por tipo.
     */
    public function seguimientosPorTipo() {
        $sql = "SELECT ts.nombre, COUNT(s.id_seguimiento) as total
                FROM tipo_seguimiento ts
                LEFT JOIN seguimientos s ON ts.id_tipo_seguimiento = s.tipo_seguimiento_id
                GROUP BY ts.nombre ORDER BY total DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>