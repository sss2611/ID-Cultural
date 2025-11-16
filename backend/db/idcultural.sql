-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 07-11-2025 a las 01:27:04
-- Versión del servidor: 10.5.29-MariaDB-ubu2004
-- Versión de PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `idcultural`
--
CREATE DATABASE IF NOT EXISTS `idcultural` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `idcultural`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artistas`
--

CREATE TABLE IF NOT EXISTS `artistas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `fecha_nacimiento` varchar(20) NOT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `municipio` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'artista',
  `status` varchar(50) NOT NULL DEFAULT 'pendiente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `artistas`
--

INSERT INTO `artistas` (`id`, `nombre`, `apellido`, `fecha_nacimiento`, `genero`, `pais`, `provincia`, `municipio`, `email`, `password`, `role`, `status`) VALUES
(2, 'nuevo', 'nuevo', '2000-12-12', 'femenino', 'Argentina', 'Buenos Aires', 'La Plata', 'nuevo@gmail.com', '$2y$10$7nxg3IMycH8sDjm0RbHDaO3DlYedW8ZOdsX4dXcJ3vV/K9IA.o8rq', 'artista', 'rechazado'),
(3, 'prueba', 'prueba', '2001-02-21', 'masculino', 'Argentina', 'Buenos Aires', 'La Plata', 'prueba@gmail.com', '$2y$10$Swtb6xK8KSKsuNXFLfcJtOZPfLgqUKYeWMBDJqVFqCO/c7C8UYDwi', 'artista', 'validado'),
(5, 'Carlos', 'Gomez', '1995-03-15', 'masculino', 'Argentina', 'Santiago del Estero', 'La Banda', 'carlos@gmail.com', '$2y$10$...', 'artista', 'pendiente'),
(6, 'Maria', 'Ledezma', '1988-07-22', 'femenino', 'Argentina', 'Santiago del Estero', 'Termas de Río Hondo', 'maria@gmail.com', '$2y$10$...', 'artista', 'rechazado'),
(7, 'Marcos', 'Romano', '1999-03-10', 'masculino', 'Argentina', 'Santiago del Estero', 'Santiago del Estero', 'ejemplo@ejemplo.com', '$2y$10$59P2kCjWBNm4xTkxCzTr9O45Jv5B2e/b2.e5U2R/lvyNX/8wTIhwe', 'artista', 'pendiente'),
(8, 'Marcos', 'Romano', '1990-02-24', 'masculino', 'Argentina', 'Santiago del Estero', 'Santiago del Estero', 'tralalero@tralala.com', '$2y$10$AIrTFknOsJCh/oVAeWzJCu12jlG8sE3PWFllZI2wP79zxAjD2FpBi', 'artista', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intereses_artista`
--

CREATE TABLE IF NOT EXISTS `intereses_artista` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artista_id` int(11) DEFAULT NULL,
  `interes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `artista_id` (`artista_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `intereses_artista`
--

INSERT INTO `intereses_artista` (`id`, `artista_id`, `interes`) VALUES
(1, 7, 'musica'),
(2, 7, 'artes_visuales'),
(3, 8, 'musica'),
(4, 8, 'danza');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE IF NOT EXISTS `noticias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `editor_id` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `editor_id` (`editor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `contenido`, `imagen_url`, `editor_id`, `fecha_creacion`) VALUES
(1, '¡Gran Apertura del Festival de Arte!', 'Este fin de semana se celebra el festival anual de arte con más de 50 artistas locales...', NULL, 2, '2025-08-14 19:38:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicaciones`
--

CREATE TABLE IF NOT EXISTS `publicaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `campos_extra` text DEFAULT NULL,
  `multimedia` text DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'borrador',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_envio_validacion` timestamp NULL DEFAULT NULL,
  `validador_id` int(11) DEFAULT NULL,
  `fecha_validacion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `validador_id` (`validador_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `publicaciones`
--

INSERT INTO `publicaciones` (`id`, `usuario_id`, `titulo`, `descripcion`, `categoria`, `campos_extra`, `multimedia`, `estado`, `fecha_creacion`, `fecha_envio_validacion`, `validador_id`, `fecha_validacion`) VALUES
(2, 8, 'Obra Maestra', 'Laralinlaralan larin loran', 'musica', '{\"action\":\"save\",\"plataformas\":\"youtube\",\"sello\":\"musicamix\"}', NULL, 'borrador', '2025-11-04 18:32:52', NULL, NULL, NULL),
(3, 8, 'remix', 'Guarachogger', 'musica', '{\"action\":\"save\",\"plataformas\":\"music\",\"sello\":\"ferleal\"}', NULL, 'pendiente', '2025-11-04 18:33:44', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `site_content`
--

CREATE TABLE IF NOT EXISTS `site_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_key` varchar(100) NOT NULL,
  `content_value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_key` (`content_key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `site_content`
--

INSERT INTO `site_content` (`id`, `content_key`, `content_value`) VALUES
(1, 'welcome_title', '<p><span class=\"ql-font-arial ql-size-large\">Bienvenidos a ID Cultural</span></p>'),
(2, 'welcome_paragraph', '<p><strong>ID Cultural</strong></p><p> es una plataforma digital dedicada a visibilizar, preservar y promover la identidad artística y cultural de Santiago del Estero. Te invitamos a explorar, descubrir y formar parte de este espacio pensado para fortalecer nuestras raíces.</p>'),
(3, 'welcome_slogan', '<p>La identidad de un pueblo, en un solo lugar.</p>'),
(4, 'carousel_image_1', 'https://placehold.co/1200x450/367789/FFFFFF?text=Cultura+Santiagueña'),
(5, 'carousel_image_2', 'https://placehold.co/1200x450/C30135/FFFFFF?text=Nuestros+Artistas'),
(6, 'carousel_image_3', 'https://placehold.co/1200x450/efc892/333333?text=Biblioteca+Digital');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `system_logs`
--

CREATE TABLE IF NOT EXISTS `system_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `user_name`, `action`, `details`, `timestamp`) VALUES
(1, 1, 'Administrador Principal', 'INICIO DE SESIÓN', 'El usuario ha iniciado sesión correctamente.', '2025-08-14 18:21:33'),
(2, 2, 'Editor de Contenidos', 'CREACIÓN DE NOTICIA', 'Se ha creado la noticia con ID: 101.', '2025-08-14 18:21:33'),
(3, 3, 'Validador de Artistas', 'VALIDACIÓN DE ARTISTA', 'Se ha aprobado la solicitud del artista con ID: 1. Comentario: Excelente portfolio.', '2025-08-14 18:21:33'),
(4, 3, 'Validador de Artistas', 'RECHAZO DE ARTISTA', 'Se ha rechazado la solicitud del artista con ID: 2. Motivo: Faltan referencias comprobables.', '2025-08-14 18:21:33'),
(5, 1, 'Usuario Desconocido', 'VALIDACIÓN DE ARTISTA', 'Se ha validado la solicitud con ID: 1.', '2025-08-14 18:39:49'),
(6, 1, 'Admin', 'VALIDACIÓN DE ARTISTA', 'Se ha cambiado el estado del artista ID: 1 a validado.', '2025-08-14 19:15:57'),
(7, 1, 'Admin', 'VALIDACIÓN DE ARTISTA', 'Se ha cambiado el estado del artista ID: 4 a validado. Comentario: buen pibe', '2025-08-14 19:21:30'),
(8, 1, 'Admin', 'VALIDACIÓN DE ARTISTA', 'Se ha cambiado el estado del artista ID: 3 a validado.', '2025-08-14 19:32:05'),
(9, 1, 'Admin', 'RECHAZO DE ARTISTA', 'Se ha cambiado el estado del artista ID: 2 a rechazado. Motivo: ninguna cancion es tuya', '2025-08-14 19:32:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombre`, `email`, `password`, `role`) VALUES
(1, 'Administrador Principal', 'admin@idcultural.com', '$2y$10$cv2EG9pZ/4y1H.z.QztN.OuGTO9x8resRsMrnJxdaKFPqreWtndf6', 'admin'),
(2, 'Editor de Contenidos', 'editor@idcultural.com', '$2y$10$9/iW1.fVT0I8E2PiYzNGv.q5AKtnboEwl4rBAHuMgV2rVcDW6wd6W', 'editor'),
(3, 'Validador de Artistas', 'validador@idcultural.com', '$2y$10$SFb4oh3S6IiTZ/LFr/e20uVodzb7n9u/I5OQu11A8AtoUNzns5QHW', 'validador'),
(5, 'tester', 'test_admin@idcultural.com', '$2y$10$lzFdvOOONIQe/AULtRDnNeSR/jK0pJVyIdpwnihCm/4IGPO05fzwe', 'admin');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `intereses_artista`
--
ALTER TABLE `intereses_artista`
  ADD CONSTRAINT `intereses_artista_ibfk_1` FOREIGN KEY (`artista_id`) REFERENCES `artistas` (`id`);

--
-- Filtros para la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `noticias_ibfk_1` FOREIGN KEY (`editor_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `publicaciones`
--
ALTER TABLE `publicaciones`
  ADD CONSTRAINT `publicaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `artistas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publicaciones_ibfk_2` FOREIGN KEY (`validador_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
