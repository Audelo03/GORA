-- Índices recomendados para mejorar el rendimiento de las consultas
-- Ejecutar estos índices en la base de datos para optimizar el rendimiento

-- Índices para la tabla alumnos
CREATE INDEX idx_alumnos_grupo ON alumnos(grupos_id_grupo);
CREATE INDEX idx_alumnos_carrera ON alumnos(carreras_id_carrera);
CREATE INDEX idx_alumnos_estatus ON alumnos(estatus);
CREATE INDEX idx_alumnos_matricula ON alumnos(matricula);
CREATE INDEX idx_alumnos_nombre ON alumnos(nombre);

-- Índices para la tabla usuarios
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_usuarios_nivel ON usuarios(niveles_usuarios_id_nivel_usuario);
CREATE INDEX idx_usuarios_estatus ON usuarios(estatus);

-- Índices para la tabla grupos
CREATE INDEX idx_grupos_tutor ON grupos(usuarios_id_usuario_tutor);
CREATE INDEX idx_grupos_carrera ON grupos(carreras_id_carrera);
CREATE INDEX idx_grupos_modalidad ON grupos(modalidades_id_modalidad);

-- Índices para la tabla seguimientos
CREATE INDEX idx_seguimientos_alumno ON seguimientos(alumnos_id_alumno);
CREATE INDEX idx_seguimientos_tipo ON seguimientos(tipo_seguimiento_id);
CREATE INDEX idx_seguimientos_fecha ON seguimientos(fecha_creacion);
CREATE INDEX idx_seguimientos_estatus ON seguimientos(estatus);

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_alumnos_grupo_estatus ON alumnos(grupos_id_grupo, estatus);
CREATE INDEX idx_seguimientos_alumno_fecha ON seguimientos(alumnos_id_alumno, fecha_creacion);
CREATE INDEX idx_usuarios_nivel_estatus ON usuarios(niveles_usuarios_id_nivel_usuario, estatus);

-- Índices para búsquedas de texto
CREATE FULLTEXT INDEX idx_alumnos_busqueda ON alumnos(nombre, apellido_paterno, apellido_materno, matricula);
CREATE FULLTEXT INDEX idx_usuarios_busqueda ON usuarios(nombre, apellido_paterno, apellido_materno, email);
