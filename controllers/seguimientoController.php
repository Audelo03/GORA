<?php

class SeguimientoController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }


    public function crear(
        int $id_alumno, 
        int $id_usuario_movimiento, 
        string $descripcion, 
        int $estatus, 
        string $fecha_movimiento, 
        ?string $fecha_compromiso,
        ?int $tipo_seguimiento_id,
        ?int $tutor_id
    ): bool {
        
       $sql = "INSERT INTO seguimientos 
                    (descripcion, estatus, fecha_compromiso, usuarios_id_usuario_movimiento, alumnos_id_alumno, tutor_id, tipo_seguimiento_id, fecha_creacion) 
                VALUES 
                    (:descripcion, :estatus, :fecha_compromiso, :id_usuario_movimiento, :id_alumno, :tutor_id, :tipo_seguimiento_id, NOW())";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':estatus', $estatus, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario_movimiento', $id_usuario_movimiento, PDO::PARAM_INT);
            $stmt->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
            $stmt->bindParam(':tipo_seguimiento_id', $tipo_seguimiento_id, PDO::PARAM_INT);
            $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT); // <-- 3. Vincular el nuevo parámetro

            if (empty($fecha_compromiso)) {
                $stmt->bindValue(':fecha_compromiso', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':fecha_compromiso', $fecha_compromiso, PDO::PARAM_STR);
            }

            return $stmt->execute();

        } catch (PDOException $e) {

            return false;
        }
    }

    public function obtenerTiposSeguimiento(): array {
        $sql = "SELECT id_tipo_seguimiento, nombre FROM tipo_seguimiento ORDER BY nombre ASC";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerPorId(int $id_seguimiento): ?array {
        $sql = "SELECT * FROM seguimientos WHERE id_seguimiento = :id_seguimiento";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_seguimiento', $id_seguimiento, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

     public function actualizar(
        int $id_seguimiento,
        string $descripcion,
        int $estatus,
        ?string $fecha_compromiso,
        ?int $tipo_seguimiento_id
    ): bool {
        $sql = "UPDATE seguimientos SET
                    descripcion = :descripcion,
                    estatus = :estatus,
                    fecha_compromiso = :fecha_compromiso,
                    tipo_seguimiento_id = :tipo_seguimiento_id
                WHERE id_seguimiento = :id_seguimiento";
        
        try {
            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(':id_seguimiento', $id_seguimiento, PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':estatus', $estatus, PDO::PARAM_INT);
            $stmt->bindParam(':tipo_seguimiento_id', $tipo_seguimiento_id, PDO::PARAM_INT);

            if (empty($fecha_compromiso)) {
                $stmt->bindValue(':fecha_compromiso', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':fecha_compromiso', $fecha_compromiso, PDO::PARAM_STR);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerSeguimientosPorRol($id_usuario, $id_nivel_usuario) {
        
        $sql_base = "
            SELECT 
                s.id_seguimiento,
                s.descripcion,
                s.estatus,
                s.fecha_creacion,
                s.fecha_compromiso,
                a.id_alumno,
                CONCAT(a.nombre, ' ', a.apellido_paterno) AS nombre_alumno,
                a.matricula,
                c.nombre AS nombre_carrera,
                u.nombre AS nombre_tutor,
                ts.nombre AS tipo_seguimiento
            FROM seguimientos s
            JOIN alumnos a ON s.alumnos_id_alumno = a.id_alumno
            JOIN usuarios u ON s.usuarios_id_usuario_movimiento = u.id_usuario
            JOIN carreras c ON a.carreras_id_carrera = c.id_carrera
            LEFT JOIN tipo_seguimiento ts ON s.tipo_seguimiento_id = ts.id_tipo_seguimiento
            LEFT JOIN usuarios ut ON s.tutor_id = ut.id_usuario
            ";

        $params = [];

        switch ($id_nivel_usuario) {
            // Nivel 2: Coordinador de Carrera
            case 2:
                $sql_base .= " WHERE c.usuarios_id_usuario_coordinador = :id_usuario";
                $params[':id_usuario'] = $id_usuario;
                break;
            
            // Nivel 3: Tutor
            case 3:
                $sql_base .= " WHERE s.tutor_id = :id_usuario";
                $params[':id_usuario'] = $id_usuario;
                break;
            case 1:
                break;
            default:
                return [];
                break;
        }

        $sql_base .= " ORDER BY c.nombre ASC, s.fecha_creacion DESC";

        try {
            $stmt = $this->conn->prepare($sql_base);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }



}