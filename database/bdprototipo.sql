-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-09-2025 a las 08:01:47
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `itsadatav2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(10) UNSIGNED NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `apellido_paterno` varchar(60) NOT NULL,
  `apellido_materno` varchar(60) DEFAULT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo, 2=Egresado, 3=Baja',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuarios_id_usuario_movimiento` int(10) UNSIGNED DEFAULT NULL,
  `carreras_id_carrera` int(10) UNSIGNED NOT NULL,
  `grupos_id_grupo` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_alumno`, `matricula`, `nombre`, `apellido_paterno`, `apellido_materno`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `carreras_id_carrera`, `grupos_id_grupo`) VALUES
(3, '20250003', 'José', 'González', 'Ramírez', 2, '2025-08-26 19:23:20', '2025-09-01 01:06:11', 1, 3, 1),
(8, '20250008', 'Elena', 'Vázquez', 'Reyes', 1, '2025-08-26 19:23:20', '2025-09-01 01:06:14', 1, 8, 1),
(10, '20250010', 'Gabriela', 'Ortiz', 'Mendoza', 1, '2025-08-26 19:23:20', '2025-09-01 01:06:16', 1, 10, 1),
(13, '20250008ew', 'Elena v2rsrwer', 'Vázquez', 'Reyes', 1, '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 8, 8),
(15, '12312d', 'fasdfasdf', 'sdfasdf', 'fasdf', 1, '2025-08-28 06:19:45', '2025-08-28 06:20:26', 2, 1, 2),
(16, '123123', '12312', '123123', '123123', 1, '2025-08-28 06:33:32', '2025-08-28 06:33:32', 2, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_alumno` int(10) UNSIGNED NOT NULL,
  `id_grupo` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `estatus` int(11) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencias`
--

INSERT INTO `asistencias` (`id`, `id_alumno`, `id_grupo`, `fecha`, `estatus`, `fecha_registro`) VALUES
(496, 15, 2, '2025-09-01', 1, '2025-09-01 03:24:55'),
(506, 13, 8, '2025-09-01', 1, '2025-09-01 04:04:48'),
(564, 16, 1, '2025-09-01', 0, '2025-09-01 17:50:36'),
(565, 3, 1, '2025-09-01', 1, '2025-09-01 17:50:36'),
(566, 10, 1, '2025-09-01', 1, '2025-09-01 17:50:36'),
(567, 8, 1, '2025-09-01', 1, '2025-09-01 17:50:36'),
(600, 15, 2, '2025-09-02', 1, '2025-09-02 05:02:38'),
(609, 16, 1, '2025-09-02', 1, '2025-09-02 05:07:42'),
(610, 3, 1, '2025-09-02', 1, '2025-09-02 05:07:42'),
(611, 10, 1, '2025-09-02', 1, '2025-09-02 05:07:42'),
(612, 8, 1, '2025-09-02', 1, '2025-09-02 05:07:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `id_carrera` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuarios_id_usuario_movimiento` int(10) UNSIGNED DEFAULT NULL,
  `usuarios_id_usuario_coordinador` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carreras`
--

INSERT INTO `carreras` (`id_carrera`, `nombre`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `usuarios_id_usuario_coordinador`) VALUES
(1, 'Ingeniería en Sistemas Computacionales', '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 2),
(2, 'Licenciatura en Administración de Empresas', '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 7),
(3, 'Licenciatura en Diseño Gráfico Digital', '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 2),
(4, 'Ingeniería Mecatrónica', '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 7),
(5, 'Licenciatura en Psicología Organizacional', '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 2),
(6, 'Arquitectura', '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 7),
(7, 'Licenciatura en Gastronomía', '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 2),
(8, 'Ingeniería Industrial y de Sistemas', '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 7),
(9, 'Licenciatura en Derecho', '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 2),
(10, 'Medicina Veterinaria y Zootecnia', '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id_grupo` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuarios_id_usuario_movimiento` int(10) UNSIGNED DEFAULT NULL,
  `usuarios_id_usuario_tutor` int(10) UNSIGNED DEFAULT NULL,
  `carreras_id_carrera` int(10) UNSIGNED NOT NULL,
  `modalidades_id_modalidad` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id_grupo`, `nombre`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `usuarios_id_usuario_tutor`, `carreras_id_carrera`, `modalidades_id_modalidad`) VALUES
(1, 'ISC-101-A', 1, '2025-08-26 19:23:20', '2025-09-01 00:47:01', 1, 3, 1, 1),
(2, 'LAE-302-B', 1, '2025-08-26 19:23:20', '2025-09-01 00:47:03', 1, 3, 2, 2),
(3, 'LDG-501-A', 1, '2025-08-26 19:23:20', '2025-08-31 21:21:11', 1, 1, 3, 3),
(4, 'IMT-703-C', 1, '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 8, 4, 1),
(5, 'LPO-202-A', 1, '2025-08-26 19:23:20', '2025-08-31 21:21:16', 1, 1, 5, 4),
(6, 'ARQ-801-B', 1, '2025-08-26 19:23:20', '2025-09-01 00:47:07', 1, 3, 6, 1),
(7, 'LGA-401-A', 1, '2025-08-26 19:23:20', '2025-08-26 19:23:20', 1, 3, 7, 5),
(8, 'IIS-602-C', 1, '2025-08-26 19:23:20', '2025-08-28 04:58:56', 1, 4, 8, 2),
(9, 'LD-901-A', 1, '2025-08-26 19:23:20', '2025-09-01 00:47:10', 1, 3, 9, 4),
(10, 'MVZ-101-B', 1, '2025-08-26 19:23:20', '2025-08-28 04:58:53', 1, 4, 10, 1),
(11, 'ISC-101-Abbbbb', 1, '2025-08-26 19:23:20', '2025-08-31 21:21:22', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modalidades`
--

CREATE TABLE `modalidades` (
  `id_modalidad` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `modalidades`
--

INSERT INTO `modalidades` (`id_modalidad`, `nombre`) VALUES
(3, 'A distancia (Online)'),
(7, 'Dual (Empresa-Escuela)'),
(4, 'Ejecutiva (Fines de semana)'),
(1, 'Escolarizada'),
(5, 'Mixta'),
(9, 'Nocturna'),
(10, 'Programa Especial'),
(2, 'Semiescolarizada'),
(6, 'Tutorial'),
(8, 'Verano Intensivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles_usuarios`
--

CREATE TABLE `niveles_usuarios` (
  `id_nivel_usuario` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `niveles_usuarios`
--

INSERT INTO `niveles_usuarios` (`id_nivel_usuario`, `nombre`, `descripcion`) VALUES
(1, 'Administrador General', 'Control total del sistema'),
(2, 'Coordinador de Carrera', 'Gestiona carreras, tutores y grupos'),
(3, 'Tutor Académico', 'Da seguimiento personalizado a los alumnos'),
(4, 'Director Academico', 'Gestiona inscripciones y trámites administrativos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimientos`
--

CREATE TABLE `seguimientos` (
  `id_seguimiento` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text NOT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Abierto, 2=En Progreso, 3=Cerrado',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_compromiso` date DEFAULT NULL,
  `usuarios_id_usuario_movimiento` int(10) UNSIGNED DEFAULT NULL,
  `alumnos_id_alumno` int(10) UNSIGNED NOT NULL,
  `tutor_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo_seguimiento_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `seguimientos`
--

INSERT INTO `seguimientos` (`id_seguimiento`, `descripcion`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `fecha_compromiso`, `usuarios_id_usuario_movimiento`, `alumnos_id_alumno`, `tutor_id`, `tipo_seguimiento_id`) VALUES
(4, 'Revisión de servicio social. Documentación completa. Trámite finalizado.', 3, '2025-08-26 19:23:20', '2025-08-26 19:23:20', NULL, 4, 3, NULL, NULL),
(9, 'Seguimiento de calificaciones. Se observa una mejora notable en el último parcial.', 2, '2025-08-26 19:23:20', '2025-09-02 05:03:57', NULL, 8, 8, NULL, 1),
(11, 'seguimiento de josé', 2, '2025-09-01 21:46:05', '2025-09-02 05:37:27', '2025-11-13', 3, 3, 3, NULL),
(12, 'dfasdf', 3, '2025-09-01 21:50:09', '2025-09-02 05:37:30', NULL, 3, 3, 3, NULL),
(13, 'dasd', 1, '2025-09-01 21:50:33', '2025-09-02 05:37:32', '2025-10-21', 3, 3, 3, NULL),
(14, 'esta bien wey', 1, '2025-09-01 21:51:30', '2025-09-02 05:37:33', '2025-09-21', 3, 15, 3, NULL),
(15, 'hola mundo, sdc', 1, '2025-09-01 21:55:32', '2025-09-02 05:37:35', '2025-09-21', 3, 3, 3, NULL),
(16, 'el muchacho esta wey', 1, '2025-09-02 04:44:00', '2025-09-02 05:37:36', '2025-09-10', 3, 3, 3, 2),
(17, 'alena', 1, '2025-09-02 04:49:14', '2025-09-02 05:37:39', '2025-09-21', 3, 8, 3, 2),
(18, 'sdfasdf1232', 1, '2025-09-02 04:59:07', '2025-09-02 05:37:41', '2025-05-21', 3, 15, 3, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_seguimiento`
--

CREATE TABLE `tipo_seguimiento` (
  `id_tipo_seguimiento` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_seguimiento`
--

INSERT INTO `tipo_seguimiento` (`id_tipo_seguimiento`, `nombre`) VALUES
(1, 'Académico'),
(2, 'Administrativo'),
(4, 'Canalización'),
(3, 'Personal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Almacenar siempre como hash, nunca texto plano',
  `estatus` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuarios_id_usuario_movimiento` int(10) UNSIGNED DEFAULT NULL,
  `niveles_usuarios_id_nivel_usuario` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido_paterno`, `apellido_materno`, `email`, `password`, `estatus`, `fecha_creacion`, `fecha_movimiento`, `usuarios_id_usuario_movimiento`, `niveles_usuarios_id_nivel_usuario`) VALUES
(1, 'Admin', 'Sistema', 'Principal', 'admin@example.com', '1234', 1, '2025-08-26 19:23:20', '2025-08-26 19:27:41', 1, 1),
(2, 'Laura', 'García', 'Martinez', 'laura.garcia@example.com', '1234', 1, '2025-08-26 19:23:20', '2025-08-26 19:27:57', 1, 2),
(3, 'Carlos', 'Rodriguez', 'Pérez', 'carlos.rodriguez@example.com', '1234', 1, '2025-08-26 19:23:20', '2025-08-28 05:32:17', 1, 3),
(4, 'Ana', 'Hernández', 'López', 'ana.hernandez@example.com', '1234', 1, '2025-08-26 19:23:20', '2025-08-26 19:28:04', 1, 4),
(5, 'Javier', 'Gómez', 'Fernández', 'javier.gomez@example.com', '1234', 1, '2025-08-26 19:23:20', '2025-08-28 05:32:23', 1, 1),
(6, 'Sofía', 'Torres', 'Ramírez', 'sofia.torres@example.com', '1234', 0, '2025-08-26 19:23:20', '2025-08-28 05:32:25', 1, 2),
(7, 'Miguel', 'Vargas', 'Jiménez', 'miguel.vargas@example.com', '1234', 1, '2025-08-26 19:23:20', '2025-08-28 05:32:28', 1, 3),
(8, 'Valeria', 'Cruz', 'Flores', 'valeria.cruz@example.com', '1234', 1, '2025-08-26 19:23:20', '2025-08-28 05:34:00', 1, 2),
(9, 'Diego', 'Rojas', 'Morales', 'diego.rojas@example.com', '1234', 1, '2025-08-26 19:23:20', '2025-08-28 05:32:31', 1, 4),
(10, 'Fernanda', 'Sánchez', 'Ortiz', 'fernanda.sanchez@example.com', '1234', 1, '2025-08-26 19:23:20', '2025-08-28 05:32:36', 1, 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD KEY `fk_alumnos_usuario_movimiento` (`usuarios_id_usuario_movimiento`),
  ADD KEY `fk_alumnos_carreras` (`carreras_id_carrera`),
  ADD KEY `fk_alumnos_grupos` (`grupos_id_grupo`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asistencia_unica` (`id_alumno`,`fecha`,`id_grupo`) USING BTREE,
  ADD KEY `id_grupo` (`id_grupo`);

--
-- Indices de la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD PRIMARY KEY (`id_carrera`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `fk_carreras_usuario_movimiento` (`usuarios_id_usuario_movimiento`),
  ADD KEY `fk_carreras_usuario_coordinador` (`usuarios_id_usuario_coordinador`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id_grupo`),
  ADD KEY `fk_grupos_usuario_movimiento` (`usuarios_id_usuario_movimiento`),
  ADD KEY `fk_grupos_usuario_tutor` (`usuarios_id_usuario_tutor`),
  ADD KEY `fk_grupos_carreras` (`carreras_id_carrera`),
  ADD KEY `fk_grupos_modalidades` (`modalidades_id_modalidad`);

--
-- Indices de la tabla `modalidades`
--
ALTER TABLE `modalidades`
  ADD PRIMARY KEY (`id_modalidad`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `niveles_usuarios`
--
ALTER TABLE `niveles_usuarios`
  ADD PRIMARY KEY (`id_nivel_usuario`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  ADD PRIMARY KEY (`id_seguimiento`),
  ADD KEY `fk_seguimientos_usuario_movimiento` (`usuarios_id_usuario_movimiento`),
  ADD KEY `fk_seguimientos_alumnos` (`alumnos_id_alumno`),
  ADD KEY `fk_seguimientos_tipo` (`tipo_seguimiento_id`),
  ADD KEY `fk_seguimientos_tutor` (`tutor_id`);

--
-- Indices de la tabla `tipo_seguimiento`
--
ALTER TABLE `tipo_seguimiento`
  ADD PRIMARY KEY (`id_tipo_seguimiento`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuarios_niveles` (`niveles_usuarios_id_nivel_usuario`),
  ADD KEY `fk_usuarios_self_movimiento` (`usuarios_id_usuario_movimiento`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=613;

--
-- AUTO_INCREMENT de la tabla `carreras`
--
ALTER TABLE `carreras`
  MODIFY `id_carrera` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `modalidades`
--
ALTER TABLE `modalidades`
  MODIFY `id_modalidad` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `niveles_usuarios`
--
ALTER TABLE `niveles_usuarios`
  MODIFY `id_nivel_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  MODIFY `id_seguimiento` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `tipo_seguimiento`
--
ALTER TABLE `tipo_seguimiento`
  MODIFY `id_tipo_seguimiento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD CONSTRAINT `fk_alumnos_carreras` FOREIGN KEY (`carreras_id_carrera`) REFERENCES `carreras` (`id_carrera`),
  ADD CONSTRAINT `fk_alumnos_grupos` FOREIGN KEY (`grupos_id_grupo`) REFERENCES `grupos` (`id_grupo`),
  ADD CONSTRAINT `fk_alumnos_usuario_movimiento` FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `asistencias_ibfk_2` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD CONSTRAINT `fk_carreras_usuario_coordinador` FOREIGN KEY (`usuarios_id_usuario_coordinador`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_carreras_usuario_movimiento` FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `fk_grupos_carreras` FOREIGN KEY (`carreras_id_carrera`) REFERENCES `carreras` (`id_carrera`),
  ADD CONSTRAINT `fk_grupos_modalidades` FOREIGN KEY (`modalidades_id_modalidad`) REFERENCES `modalidades` (`id_modalidad`),
  ADD CONSTRAINT `fk_grupos_usuario_movimiento` FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_grupos_usuario_tutor` FOREIGN KEY (`usuarios_id_usuario_tutor`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `seguimientos`
--
ALTER TABLE `seguimientos`
  ADD CONSTRAINT `fk_seguimientos_alumnos` FOREIGN KEY (`alumnos_id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_seguimientos_tipo` FOREIGN KEY (`tipo_seguimiento_id`) REFERENCES `tipo_seguimiento` (`id_tipo_seguimiento`),
  ADD CONSTRAINT `fk_seguimientos_tutor` FOREIGN KEY (`tutor_id`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `fk_seguimientos_usuario_movimiento` FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_niveles` FOREIGN KEY (`niveles_usuarios_id_nivel_usuario`) REFERENCES `niveles_usuarios` (`id_nivel_usuario`),
  ADD CONSTRAINT `fk_usuarios_self_movimiento` FOREIGN KEY (`usuarios_id_usuario_movimiento`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
