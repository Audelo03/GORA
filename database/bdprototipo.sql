CREATE TABLE `niveles_usuarios` (
  `id_nivel_usuario` integer PRIMARY KEY,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(80)
);

CREATE TABLE `usuarios` (
  `id_usuario` integer PRIMARY KEY,
  `nombre` varchar(80) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` char(50) NOT NULL,
  `estatus` SMALLINT NOT NULL,
  `fecha_creacion` date,
  `fecha_moviemento` date,
  `tipo_movimiento` SMALLINT,
  `usuarios_id_usuario_movimiento` integer,
  `niveles_usuarios_id_nivel_usuario` integer
);


CREATE TABLE `carreras` (
  `id_carrera` integer PRIMARY KEY,
  `nombre` varchar(30),
  `fecha_creacion` date,
  `fecha_moviemento` date,
  `tipo_movimiento` SMALLINT,
  `usuarios_id_usuario_movimiento` integer,
  'coordinador' integer
);

CREATE TABLE `modalidades` (
  `id_modalidad` integer PRIMARY KEY,
  `nombre` varchar(100)
);

CREATE TABLE `grupos` (
  `id_grupo` integer PRIMARY KEY,
  `nombre` varchar(30),
  `estatus` SMALLINT,
  `fecha_creacion` date,
  `fecha_moviemento` date,
  `tipo_movimiento` SMALLINT,
  `usuarios_id_usuario_movimiento` integer,
  `usuarios_id_usuario_tutor` integer,
  `carreras_id_carrera` integer,
  `modalidades_id_modalidad` integer
);

CREATE TABLE `alumnos` (
  `id_alumno` integer PRIMARY KEY,
  `matricula` integer NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `apellido_paterno` varchar(60),
  `apellido_materno` varchar(60),
  `estatus` SMALLINT NOT NULL,
  `fecha_creacion` date,
  `fecha_moviemento` date,
  `tipo_movimiento` SMALLINT,
  `usuarios_id_usuario_movimiento` integer,
 
  `carreras_id_carrera` integer,
  `grupos_id_grupo` integer
);

CREATE TABLE `seguimientos` (
  `id_seguimiento` integer PRIMARY KEY,
  `descripcion` text,
  `estatus` SMALLINT,
  `fecha_creacion` date,
  `fecha_moviemento` date,
  `fecha_compromiso` date,
  `tipo_movimiento` SMALLINT,
  `usuarios_id_usuario_movimiento` integer,
  `alumnos_alumno_id` integer
);

ALTER TABLE `usuarios` ADD FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);

ALTER TABLE `usuarios` ADD FOREIGN KEY (`niveles_usuarios_id_nivel_usuario`) REFERENCES `niveles_usuarios` (`id_nivel_usuario`);

ALTER TABLE `carreras` ADD FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);

ALTER TABLE `carreras` ADD FOREIGN KEY (`coordinador`) REFERENCES `usuarios` (`id_usuario`);

ALTER TABLE `grupos` ADD FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);

ALTER TABLE `grupos` ADD FOREIGN KEY (`usuarios_id_usuario_tutor`) REFERENCES `usuarios` (`id_usuario`);

ALTER TABLE `grupos` ADD FOREIGN KEY (`carreras_id_carrera`) REFERENCES `carreras` (`id_carrera`);

ALTER TABLE `grupos` ADD FOREIGN KEY (`modalidades_id_modalidad`) REFERENCES `modalidades` (`id_modalidad`);

ALTER TABLE 'grupos' ADD FOREIGN KEY (' `usuarios_id_usuario_tutor`) REFERENCES `usuarios` (`id_usuario`);')

ALTER TABLE `alumnos` ADD FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);

ALTER TABLE `alumnos` ADD FOREIGN KEY (`carreras_id_carrera`) REFERENCES `carreras` (`id_carrera`);

ALTER TABLE `alumnos` ADD FOREIGN KEY (`grupos_id_grupo`) REFERENCES `grupos` (`id_grupo`);

ALTER TABLE `seguimientos` ADD FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);

ALTER TABLE `seguimientos` ADD FOREIGN KEY (`alumnos_alumno_id`) REFERENCES `alumnos` (`id_alumno`);
