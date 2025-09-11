<?php
/**
 * MODELO DE GRUPO - GORA
 * 
 * Maneja todas las operaciones de base de datos relacionadas
 * con los grupos de estudiantes.
 */

class Grupo {
    private $conn;
    private $table_name = "grupos";

    // Propiedades del grupo
    public $id_grupo;
    public $nombre;
    public $estatus;
    public $usuarios_id_usuario_tutor;
    public $carreras_id_carrera;
    public $modalidades_id_modalidad;
    public $usuarios_id_usuario_movimiento;

    /**
     * Constructor del modelo de grupo
     * @param PDO $db - Conexión a la base de datos
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtiene todos los grupos con información relacionada
     * @return array - Array con todos los grupos
     */
    public function getAll() {
        $query = "SELECT 
                    g.id_grupo, 
                    g.nombre, 
                    g.estatus,
                    g.usuarios_id_usuario_tutor,    -- ID del tutor (necesario para editar)
                    g.carreras_id_carrera,          -- ID de la carrera (necesario para editar)
                    g.modalidades_id_modalidad,     -- ID de la modalidad (necesario para editar)
                    CONCAT(u.nombre, ' ', u.apellido_paterno) as tutor_nombre, -- Nombre para mostrar
                    c.nombre as carrera_nombre,     -- Nombre para mostrar
                    m.nombre as modalidad_nombre    -- Nombre para mostrar
                  FROM " . $this->table_name . " g
                  LEFT JOIN usuarios u ON g.usuarios_id_usuario_tutor = u.id_usuario
                  LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                  LEFT JOIN modalidades m ON g.modalidades_id_modalidad = m.id_modalidad
                  WHERE g.estatus = 1
                  ORDER BY g.nombre ASC";
      
         $stmt = $this->conn->prepare($query);
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene grupos paginados con búsqueda
     * @param int $offset - Desplazamiento para la paginación
     * @param int $limit - Límite de registros por página
     * @param string $search - Término de búsqueda
     * @return array - Array con grupos paginados
     */
    public function getAllPaginated($offset, $limit, $search = '') {
        $sql = "SELECT 
                    g.id_grupo, 
                    g.nombre, 
                    g.estatus,
                    g.usuarios_id_usuario_tutor,
                    g.carreras_id_carrera,
                    g.modalidades_id_modalidad,
                    CONCAT(u.nombre, ' ', u.apellido_paterno) as tutor_nombre,
                    c.nombre as carrera_nombre,
                    m.nombre as modalidad_nombre
                  FROM " . $this->table_name . " g
                  LEFT JOIN usuarios u ON g.usuarios_id_usuario_tutor = u.id_usuario
                  LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                  LEFT JOIN modalidades m ON g.modalidades_id_modalidad = m.id_modalidad
                  WHERE g.estatus = 1";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (LOWER(g.nombre) LIKE LOWER(:search) 
                     OR LOWER(u.nombre) LIKE LOWER(:search) 
                     OR LOWER(u.apellido_paterno) LIKE LOWER(:search) 
                     OR LOWER(c.nombre) LIKE LOWER(:search)
                     OR LOWER(m.nombre) LIKE LOWER(:search))";
            $params[':search'] = '%' . $search . '%';
        }
        
        $sql .= " ORDER BY g.nombre ASC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el total de grupos con filtro de búsqueda
     * @param string $search - Término de búsqueda
     * @return int - Total de grupos que coinciden con la búsqueda
     */
    public function countAll($search = '') {
        $sql = "SELECT COUNT(*) FROM " . $this->table_name . " g
                LEFT JOIN usuarios u ON g.usuarios_id_usuario_tutor = u.id_usuario
                LEFT JOIN carreras c ON g.carreras_id_carrera = c.id_carrera
                LEFT JOIN modalidades m ON g.modalidades_id_modalidad = m.id_modalidad
                WHERE g.estatus = 1";
        
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (LOWER(g.nombre) LIKE LOWER(:search) 
                     OR LOWER(u.nombre) LIKE LOWER(:search) 
                     OR LOWER(u.apellido_paterno) LIKE LOWER(:search) 
                     OR LOWER(c.nombre) LIKE LOWER(:search)
                     OR LOWER(m.nombre) LIKE LOWER(:search))";
            $params[':search'] = '%' . $search . '%';
        }
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    
    /**
     * Lee un solo grupo por su ID
     * @return void - Asigna los valores a las propiedades del objeto
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_grupo = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id_grupo);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->nombre = $row['nombre'];
        $this->estatus = $row['estatus'];
        $this->usuarios_id_usuario_tutor = $row['usuarios_id_usuario_tutor'];
        $this->carreras_id_carrera = $row['carreras_id_carrera'];
        $this->modalidades_id_modalidad = $row['modalidades_id_modalidad'];
    }

    /**
     * Crea un nuevo grupo en la base de datos
     * @return bool - True si se creó exitosamente
     * @throws Exception - Si hay error en la creación
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nombre=:nombre, estatus=:estatus, usuarios_id_usuario_tutor=:tutor, carreras_id_carrera=:carrera, modalidades_id_modalidad=:modalidad, usuarios_id_usuario_movimiento=:usuario_movimiento";
        
        try {
            $stmt = $this->conn->prepare($query);

            // Sanitizar y enlazar parámetros
            $stmt->bindValue(":nombre", htmlspecialchars(strip_tags($this->nombre)));
            $stmt->bindValue(":estatus", htmlspecialchars(strip_tags($this->estatus)));
            $stmt->bindValue(":tutor", htmlspecialchars(strip_tags($this->usuarios_id_usuario_tutor)));
            $stmt->bindValue(":carrera", htmlspecialchars(strip_tags($this->carreras_id_carrera)));
            $stmt->bindValue(":modalidad", htmlspecialchars(strip_tags($this->modalidades_id_modalidad)));
            $stmt->bindValue(":usuario_movimiento", htmlspecialchars(strip_tags($this->usuarios_id_usuario_movimiento)));

            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            // Si el error es por una clave única duplicada (ej. el nombre del grupo ya existe)
            if ($e->getCode() == '23000') {
                throw new Exception("El nombre del grupo ya existe. Por favor, elija otro.");
            }
            // Para cualquier otro error
            throw new Exception("Error al crear el grupo: " . $e->getMessage());
        }
    }
    /**
     * Actualiza un grupo existente en la base de datos
     * @return bool - True si se actualizó exitosamente
     * @throws Exception - Si hay error en la actualización
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nombre=:nombre, estatus=:estatus, usuarios_id_usuario_tutor=:tutor, carreras_id_carrera=:carrera, modalidades_id_modalidad=:modalidad, usuarios_id_usuario_movimiento=:usuario_movimiento WHERE id_grupo = :id";
        
        try {
            $stmt = $this->conn->prepare($query);

            // Sanitizar y enlazar parámetros
            $stmt->bindValue(":nombre", htmlspecialchars(strip_tags($this->nombre)));
            $stmt->bindValue(":estatus", htmlspecialchars(strip_tags($this->estatus)));
            $stmt->bindValue(":tutor", htmlspecialchars(strip_tags($this->usuarios_id_usuario_tutor)));
            $stmt->bindValue(":carrera", htmlspecialchars(strip_tags($this->carreras_id_carrera)));
            $stmt->bindValue(":modalidad", htmlspecialchars(strip_tags($this->modalidades_id_modalidad)));
            $stmt->bindValue(":usuario_movimiento", htmlspecialchars(strip_tags($this->usuarios_id_usuario_movimiento)));
            $stmt->bindValue(":id", htmlspecialchars(strip_tags($this->id_grupo)));

            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró el grupo para actualizar o no se realizaron cambios en los datos.");
            }
            return true;

        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                throw new Exception("El nombre del grupo ya existe en otro registro.");
            }
            throw new Exception("Error al actualizar el grupo: " . $e->getMessage());
        }
    }


    /**
     * Elimina un grupo de la base de datos
     * @return bool - True si se eliminó exitosamente
     * @throws Exception - Si hay error en la eliminación
     */
    public function delete() {
        $query = "UPDATE " . $this->table_name . " SET estatus = 0 WHERE id_grupo = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":id", htmlspecialchars(strip_tags($this->id_grupo)));
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                throw new Exception("No se encontró el grupo para eliminar.");
            }
            return true;

        } catch (PDOException $e) {
            throw new Exception("Error al eliminar el grupo: " . $e->getMessage());
        }
    }
}
?>