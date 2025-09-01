<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Alumno.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class AlumnoController {
    public $alumno;

    public function __construct($conn) {
        $this->alumno = new Alumno($conn);
    }

    // --- MÉTODOS DE PAGINACION Y CONTEO ---
    public function contarTotalCarreras($terminoBusqueda) {
    $sql = "SELECT COUNT(DISTINCT c.id_carrera) 
            FROM carreras c
            LEFT JOIN alumnos a ON a.carreras_id_carrera = c.id_carrera
            LEFT JOIN grupos g ON g.carreras_id_carrera = c.id_carrera
            WHERE (
                LOWER(c.nombre) LIKE LOWER(:termino)
                OR LOWER(g.nombre) LIKE LOWER(:termino)   -- 🔹 Buscar por grupo
                OR LOWER(a.nombre) LIKE LOWER(:termino)
                OR LOWER(a.apellido_paterno) LIKE LOWER(:termino)
                OR LOWER(a.apellido_materno) LIKE LOWER(:termino)
                OR LOWER(a.matricula) LIKE LOWER(:termino)
            )";
    $stmt = $this->alumno->conn->prepare($sql);
    $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
    $stmt->execute();
    return (int)$stmt->fetchColumn();
}

public function obtenerCarrerasPaginadas($terminoBusqueda, $offset, $limit) {
    $sql = "SELECT DISTINCT c.* FROM carreras c
            LEFT JOIN alumnos a ON a.carreras_id_carrera = c.id_carrera
            LEFT JOIN grupos g ON g.carreras_id_carrera = c.id_carrera
            WHERE (
                LOWER(c.nombre) LIKE LOWER(:termino)
                OR LOWER(g.nombre) LIKE LOWER(:termino)   -- 🔹 Buscar por grupo
                OR LOWER(a.nombre) LIKE LOWER(:termino)
                OR LOWER(a.apellido_paterno) LIKE LOWER(:termino)
                OR LOWER(a.apellido_materno) LIKE LOWER(:termino)
                OR LOWER(a.matricula) LIKE LOWER(:termino)
            )
            ORDER BY c.nombre 
            LIMIT :limit OFFSET :offset";
    $stmt = $this->alumno->conn->prepare($sql);
    $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
    public function contarTotalGruposPorCarrera($idCarrera, $terminoBusqueda) {
        $sql = "SELECT COUNT(DISTINCT g.id_grupo) 
                FROM grupos g
                LEFT JOIN alumnos a ON a.grupos_id_grupo = g.id_grupo
                WHERE g.carreras_id_carrera = :idCarrera 
                AND (
                    LOWER(g.nombre) LIKE LOWER(:termino)
                    OR LOWER(a.nombre) LIKE LOWER(:termino)
                    OR LOWER(a.apellido_paterno) LIKE LOWER(:termino)
                    OR LOWER(a.apellido_materno) LIKE LOWER(:termino)
                    OR LOWER(a.matricula) LIKE LOWER(:termino)
                )";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
        $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function obtenerGruposPaginadosPorCarrera($idCarrera, $terminoBusqueda, $offset, $limit) {
        $sql = "SELECT DISTINCT g.id_grupo, g.nombre
                FROM grupos g
                LEFT JOIN alumnos a ON a.grupos_id_grupo = g.id_grupo
                WHERE g.carreras_id_carrera = :idCarrera 
                AND (
                    LOWER(g.nombre) LIKE LOWER(:termino)
                    OR LOWER(a.nombre) LIKE LOWER(:termino)
                    OR LOWER(a.apellido_paterno) LIKE LOWER(:termino)
                    OR LOWER(a.apellido_materno) LIKE LOWER(:termino)
                    OR LOWER(a.matricula) LIKE LOWER(:termino)
                )
                ORDER BY g.nombre 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':idCarrera', $idCarrera, PDO::PARAM_INT);
        $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function contarTotalGruposPorTutor($idUsuario, $terminoBusqueda) {
        $sql = "SELECT COUNT(DISTINCT g.id_grupo) 
                FROM grupos g
                LEFT JOIN alumnos a ON a.grupos_id_grupo = g.id_grupo
                WHERE g.usuarios_id_usuario_tutor = :idUsuario 
                AND (
                    LOWER(g.nombre) LIKE LOWER(:termino)
                    OR LOWER(a.nombre) LIKE LOWER(:termino)
                    OR LOWER(a.apellido_paterno) LIKE LOWER(:termino)
                    OR LOWER(a.apellido_materno) LIKE LOWER(:termino)
                    OR LOWER(a.matricula) LIKE LOWER(:termino)
                )";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    
    public function obtenerGruposPaginadosPorTutor($idUsuario, $terminoBusqueda, $offset, $limit) {
        $sql = "SELECT DISTINCT g.id_grupo, g.nombre
                FROM grupos g
                LEFT JOIN alumnos a ON a.grupos_id_grupo = g.id_grupo
                WHERE g.usuarios_id_usuario_tutor = :idUsuario 
                AND (
                    LOWER(g.nombre) LIKE LOWER(:termino)
                    OR LOWER(a.nombre) LIKE LOWER(:termino)
                    OR LOWER(a.apellido_paterno) LIKE LOWER(:termino)
                    OR LOWER(a.apellido_materno) LIKE LOWER(:termino)
                    OR LOWER(a.matricula) LIKE LOWER(:termino)
                )
                ORDER BY g.nombre 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':termino', '%' . $terminoBusqueda . '%');
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
//renderizar alumnos
    public function listarAlumnosPorIdsDeGrupos($grupos_ids, $conn, string $parentUid = "root"){ 
        ob_start();
        ?>
        <div class="accordion" id="accordion_<?= htmlspecialchars($parentUid) ?>">
            <?php foreach ($grupos_ids as $id_grupo_data): 
                $id_grupo_id = $id_grupo_data["id_grupo"];
                $stmt = $conn->prepare("SELECT nombre FROM grupos WHERE id_grupo = :id_grupo");
                $stmt->bindParam(':id_grupo', $id_grupo_id, PDO::PARAM_INT);
                $stmt->execute();
                $grupo_result = $stmt->fetch(PDO::FETCH_ASSOC);
                $grupo_nombre = $grupo_result ? $grupo_result['nombre'] : "Grupo no encontrado (ID: ".htmlspecialchars($id_grupo_id).")";
                
                $alumnosResult = $this->alumno->listByGroupId($id_grupo_id);
                $alumnos = is_array($alumnosResult) ? $alumnosResult : [];

                $grupoUid = "grupo_" . $id_grupo_id . "_" . uniqid();
            ?>
            <div class="accordion-item">
                <h2 class="accordion-header d-flex align-items-center" id="heading_<?= htmlspecialchars($grupoUid) ?>">
    
    <button class="accordion-button collapsed bg-info text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?= htmlspecialchars($grupoUid) ?>">
        <i class="bi bi-people-fill me-2"></i> Grupo: <?= htmlspecialchars($grupo_nombre) ?> (<?= count($alumnos) ?> alumnos)
    </button>
    
                    <a href="gestionar_listas.php?id_grupo=<?= htmlspecialchars($id_grupo_id) ?>"
                       class="btn btn-primary flex-shrink-0 me-2"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       title="Gestionar Listas">
                        <i class="bi bi-pencil-square me-1"></i>
                    </a>
                  <a href="asistencia.php?id_grupo=<?= htmlspecialchars($id_grupo_id) ?>&fecha=<?= urlencode(date('Y-m-d')) ?>"
   class="btn btn-success flex-shrink-0 me-2" 
   data-bs-toggle="tooltip" 
   data-bs-placement="top" 
   title="Tomar Asistencia">
    <i class="bi bi-list-check me-1"></i>
</a>

                </h2>
                <div id="collapse_<?= htmlspecialchars($grupoUid) ?>" class="accordion-collapse collapse" data-bs-parent="#accordion_<?= htmlspecialchars($parentUid) ?>">
                    <div class="accordion-body">
                        <?php if (empty($alumnos)): ?>
                            <p>No hay alumnos en este grupo.</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($alumnos as $a): ?>
                                    <li class="list-group-item">
                                        <i class="bi bi-person-circle text-primary me-2"></i>
                                        <?= htmlspecialchars($a['nombre'] . ' ' . $a['apellido_paterno'] . ' ' . ($a['apellido_materno'] ?? '')) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function renderizarAcordeonCarrera($dataCarrera, $conn, $auth) {
        ob_start();
        $carreraid = $dataCarrera["id_carrera"];
        $nombre_carrera = $dataCarrera["nombre"];
        $carreraUid = "carrera_" . $carreraid;
        ?>
        <div class="accordion mb-3" id="<?= $carreraUid ?>">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_<?= $carreraUid ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?= $carreraUid ?>">
                        <i class="bi bi-mortarboard-fill me-2"></i> Carrera: <?= htmlspecialchars($nombre_carrera) ?>
                    </button>
                </h2>
                <div id="collapse_<?= $carreraUid ?>" class="accordion-collapse collapse" data-bs-parent="#<?= $carreraUid ?>">
                    <div class="accordion-body">
                        <?php 
                            $grupos_ids = $auth->usuario->getGruposIdByCarreraId($carreraid);
                            if (!empty($grupos_ids)) {
                               echo $this->listarAlumnosPorIdsDeGrupos($grupos_ids, $conn, $carreraUid); 
                            } else {
                                echo "<p>No hay grupos asignados a esta carrera.</p>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    public function getNombreGrupo($id_grupo)
    {
        $sql = "SELECT nombre FROM grupos WHERE id_grupo = :id_grupo";
        $stmt = $this->alumno->conn->prepare($sql);
        $stmt->bindParam(':id_grupo', $id_grupo, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['nombre'] : null;
    }

    public function getAlumnosByGrupo($id_grupo)
{
    $query = "SELECT 
                a.id_alumno, 
                a.matricula, 
                CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', a.apellido_materno) as nombre_completo,
                g.nombre as nombre_grupo
              FROM alumnos a
              JOIN grupos g ON a.grupos_id_grupo = g.id_grupo
              WHERE a.grupos_id_grupo = :id_grupo
              ORDER BY a.apellido_paterno, a.apellido_materno, a.nombre";
    
     $stmt = $this->alumno->conn->prepare($query);
    $stmt->bindParam(":id_grupo", $id_grupo);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>