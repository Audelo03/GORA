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

    /**
     * Obtiene estadísticas de usuarios por nivel
     */
    public function usuariosPorNivel() {
        $sql = "SELECT nu.nombre, COUNT(u.id_usuario) as total
                FROM niveles_usuarios nu
                LEFT JOIN usuarios u ON nu.id_nivel_usuario = u.niveles_usuarios_id_nivel_usuario
                WHERE u.estatus = 1
                GROUP BY nu.nombre ORDER BY total DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene distribución de alumnos por grupo
     */
    public function alumnosPorGrupo() {
        $sql = "SELECT g.nombre as grupo, c.nombre as carrera, COUNT(a.id_alumno) as total
                FROM grupos g
                LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                GROUP BY g.id_grupo, g.nombre, c.nombre
                HAVING total > 0
                ORDER BY total DESC
                LIMIT 10";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas de asistencia por mes
     */
    public function asistenciaPorMes() {
        $sql = "SELECT 
                    DATE_FORMAT(fecha, '%Y-%m') as mes,
                    COUNT(*) as total_registros,
                    SUM(CASE WHEN estatus = 1 THEN 1 ELSE 0 END) as asistencias,
                    SUM(CASE WHEN estatus = 0 THEN 1 ELSE 0 END) as faltas,
                    ROUND((SUM(CASE WHEN estatus = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as porcentaje_asistencia
                FROM asistencias 
                WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(fecha, '%Y-%m')
                ORDER BY mes DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene seguimientos por mes
     */
    public function seguimientosPorMes() {
        $sql = "SELECT 
                    DATE_FORMAT(fecha_creacion, '%Y-%m') as mes,
                    COUNT(*) as total_seguimientos,
                    SUM(CASE WHEN estatus = 1 THEN 1 ELSE 0 END) as abiertos,
                    SUM(CASE WHEN estatus = 2 THEN 1 ELSE 0 END) as en_progreso,
                    SUM(CASE WHEN estatus = 3 THEN 1 ELSE 0 END) as cerrados
                FROM seguimientos 
                WHERE fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m')
                ORDER BY mes DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productividad de tutores
     */
    public function productividadTutores() {
        $sql = "SELECT 
                    CONCAT(u.nombre, ' ', u.apellido_paterno) as tutor,
                    COUNT(DISTINCT g.id_grupo) as grupos_asignados,
                    COUNT(DISTINCT a.id_alumno) as alumnos_tutoreados,
                    COUNT(s.id_seguimiento) as seguimientos_realizados,
                    ROUND(COUNT(s.id_seguimiento) / COUNT(DISTINCT a.id_alumno), 2) as promedio_seguimientos_por_alumno
                FROM usuarios u
                LEFT JOIN grupos g ON u.id_usuario = g.usuarios_id_usuario_tutor
                LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                LEFT JOIN seguimientos s ON a.id_alumno = s.alumnos_id_alumno
                WHERE u.niveles_usuarios_id_nivel_usuario = 3
                GROUP BY u.id_usuario, u.nombre, u.apellido_paterno
                HAVING grupos_asignados > 0
                ORDER BY seguimientos_realizados DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas de alumnos por año de ingreso
     */
    public function alumnosPorAnioIngreso() {
        $sql = "SELECT 
                    YEAR(fecha_creacion) as anio_ingreso,
                    COUNT(*) as total_alumnos,
                    SUM(CASE WHEN estatus = 1 THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estatus = 2 THEN 1 ELSE 0 END) as egresados,
                    SUM(CASE WHEN estatus = 3 THEN 1 ELSE 0 END) as bajas
                FROM alumnos 
                GROUP BY YEAR(fecha_creacion)
                ORDER BY anio_ingreso DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas de carreras más populares
     */
    public function carrerasMasPopulares() {
        $sql = "SELECT 
                    c.nombre as carrera,
                    COUNT(a.id_alumno) as total_alumnos,
                    COUNT(DISTINCT g.id_grupo) as total_grupos,
                    ROUND(COUNT(a.id_alumno) / COUNT(DISTINCT g.id_grupo), 2) as promedio_alumnos_por_grupo
                FROM carreras c
                LEFT JOIN grupos g ON c.id_carrera = g.carreras_id_carrera
                LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                GROUP BY c.id_carrera, c.nombre
                HAVING total_alumnos > 0
                ORDER BY total_alumnos DESC
                LIMIT 10";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas de modalidades más utilizadas
     */
    public function modalidadesMasUtilizadas() {
        $sql = "SELECT 
                    m.nombre as modalidad,
                    COUNT(g.id_grupo) as total_grupos,
                    COUNT(a.id_alumno) as total_alumnos,
                    ROUND(COUNT(a.id_alumno) / COUNT(g.id_grupo), 2) as promedio_alumnos_por_grupo
                FROM modalidades m
                LEFT JOIN grupos g ON m.id_modalidad = g.modalidades_id_modalidad
                LEFT JOIN alumnos a ON g.id_grupo = a.grupos_id_grupo
                GROUP BY m.id_modalidad, m.nombre
                HAVING total_grupos > 0
                ORDER BY total_grupos DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas generales del sistema
     */
    public function estadisticasGenerales() {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM alumnos WHERE estatus = 1) as alumnos_activos,
                    (SELECT COUNT(*) FROM usuarios WHERE estatus = 1) as usuarios_activos,
                    (SELECT COUNT(*) FROM grupos) as total_grupos,
                    (SELECT COUNT(*) FROM carreras) as total_carreras,
                    (SELECT COUNT(*) FROM seguimientos WHERE estatus = 1) as seguimientos_abiertos,
                    (SELECT COUNT(*) FROM asistencias WHERE fecha = CURDATE()) as asistencias_hoy,
                    (SELECT COUNT(*) FROM asistencias WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) as asistencias_semana,
                    (SELECT COUNT(*) FROM seguimientos WHERE fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) as seguimientos_mes";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>