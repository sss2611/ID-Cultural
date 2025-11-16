/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.5.29-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: idcultural
-- ------------------------------------------------------
-- Server version	10.5.29-MariaDB-ubu2004

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `artistas`
--

DROP TABLE IF EXISTS `artistas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `artistas` (
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
  `biografia` text DEFAULT NULL,
  `especialidades` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `sitio_web` varchar(255) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `status_perfil` varchar(20) DEFAULT 'pendiente',
  `motivo_rechazo` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_status_perfil` (`status_perfil`),
  KEY `idx_status_provincia` (`status_perfil`,`provincia`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `artistas`
--

LOCK TABLES `artistas` WRITE;
/*!40000 ALTER TABLE `artistas` DISABLE KEYS */;
INSERT INTO `artistas` VALUES (2,'nuevo','nuevo','2000-12-12','femenino','Argentina','Buenos Aires','La Plata','nuevo@gmail.com','$2y$10$7nxg3IMycH8sDjm0RbHDaO3DlYedW8ZOdsX4dXcJ3vV/K9IA.o8rq','artista','rechazado',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'rechazado','hbgb'),(3,'prueba','prueba','2001-02-21','masculino','Argentina','Buenos Aires','La Plata','prueba@gmail.com','$2y$10$Swtb6xK8KSKsuNXFLfcJtOZPfLgqUKYeWMBDJqVFqCO/c7C8UYDwi','artista','validado',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'rechazado','jnn'),(5,'Carlos','Gomez','1995-03-15','masculino','Argentina','Santiago del Estero','La Banda','carlos@gmail.com','$2y$10$...','artista','pendiente',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'validado',NULL),(6,'Maria','Ledezma','1988-07-22','femenino','Argentina','Santiago del Estero','Termas de Río Hondo','maria@gmail.com','$2y$10$...','artista','rechazado',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'validado',NULL),(7,'Marcos','Romano','1999-03-10','masculino','Argentina','Santiago del Estero','Santiago del Estero','ejemplo@ejemplo.com','$2y$10$59P2kCjWBNm4xTkxCzTr9O45Jv5B2e/b2.e5U2R/lvyNX/8wTIhwe','artista','validado',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'validado',NULL),(8,'Marcos','Romano','1949-05-15','Masculino','Argentina','Santiago del Estero','Tintina','tralalero@tralala.com','$2y$10$QWpJ6kpCFoLzBNN3TXOP7uqXmpqsL7SsF6XqCKHDrqaukbKJrraLu','artista','validado','Un buen pibe (sera?)','Escultor de carne','@marcos_romano_updated','marcos.romano.updated','@marcos_romano_updated','https://marcos-romano-updated.com','/uploads/imagens/media_690f8d41bdf2f6.84231658.jpeg','validado',NULL);
/*!40000 ALTER TABLE `artistas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intereses_artista`
--

DROP TABLE IF EXISTS `intereses_artista`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `intereses_artista` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artista_id` int(11) DEFAULT NULL,
  `interes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `artista_id` (`artista_id`),
  CONSTRAINT `intereses_artista_ibfk_1` FOREIGN KEY (`artista_id`) REFERENCES `artistas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intereses_artista`
--

LOCK TABLES `intereses_artista` WRITE;
/*!40000 ALTER TABLE `intereses_artista` DISABLE KEYS */;
INSERT INTO `intereses_artista` VALUES (1,7,'musica'),(2,7,'artes_visuales'),(3,8,'musica'),(4,8,'danza'),(5,8,'teatro');
/*!40000 ALTER TABLE `intereses_artista` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_validacion_perfiles`
--

DROP TABLE IF EXISTS `logs_validacion_perfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs_validacion_perfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artista_id` int(11) NOT NULL,
  `validador_id` int(11) DEFAULT NULL,
  `accion` varchar(20) NOT NULL,
  `motivo_rechazo` text DEFAULT NULL,
  `fecha_accion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_artista_id` (`artista_id`),
  KEY `idx_validador_id` (`validador_id`),
  KEY `idx_fecha_accion` (`fecha_accion`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_validacion_perfiles`
--

LOCK TABLES `logs_validacion_perfiles` WRITE;
/*!40000 ALTER TABLE `logs_validacion_perfiles` DISABLE KEYS */;
INSERT INTO `logs_validacion_perfiles` VALUES (1,8,1,'validar',NULL,'2025-11-09 00:10:27'),(2,7,1,'validar',NULL,'2025-11-09 00:21:06'),(3,6,1,'rechazar','lo','2025-11-09 00:24:58'),(4,6,1,'validar',NULL,'2025-11-09 00:25:02'),(5,5,1,'validar',NULL,'2025-11-09 00:26:54'),(6,3,1,'rechazar','jnn','2025-11-09 14:26:36'),(7,2,1,'rechazar','hbgb','2025-11-09 14:26:43'),(8,8,1,'validar',NULL,'2025-11-10 00:40:48');
/*!40000 ALTER TABLE `logs_validacion_perfiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `noticias`
--

DROP TABLE IF EXISTS `noticias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `noticias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  `editor_id` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `editor_id` (`editor_id`),
  CONSTRAINT `noticias_ibfk_1` FOREIGN KEY (`editor_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noticias`
--

LOCK TABLES `noticias` WRITE;
/*!40000 ALTER TABLE `noticias` DISABLE KEYS */;
INSERT INTO `noticias` VALUES (1,'¡Gran Apertura del Festival de Arte!','Este fin de semana se celebra el festival anual de arte con más de 50 artistas locales...','http://localhost:8080/static/uploads/noticias/noticia_1761572259_68ff75a35a30c.jpeg',2,'2025-08-14 19:38:03'),(7,'Evento','Test','http://localhost:8080/static/uploads/noticias/noticia_1761540083_68fef7f38e1ad.jpeg',2,'2025-10-27 04:31:57');
/*!40000 ALTER TABLE `noticias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publicaciones`
--

DROP TABLE IF EXISTS `publicaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `publicaciones` (
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
  KEY `validador_id` (`validador_id`),
  CONSTRAINT `publicaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `artistas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `publicaciones_ibfk_2` FOREIGN KEY (`validador_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publicaciones`
--

LOCK TABLES `publicaciones` WRITE;
/*!40000 ALTER TABLE `publicaciones` DISABLE KEYS */;
INSERT INTO `publicaciones` VALUES (5,8,'sdsada','dsasadd','literatura','{\"action\":\"save\",\"genero-lit\":\"sdad\",\"editorial\":\"aasdsad\"}',NULL,'validado','2025-11-08 21:55:42','2025-11-08 21:55:42',3,'2025-11-08 22:06:24'),(6,8,'aaaaaaaaaaa','aaaaaaaaaaaaaaa','musica','{\"action\":\"save\",\"plataformas\":\"aaaa\",\"sello\":\"aaaaa\"}','/uploads/imagens/media_690fc13a934708.23834792.jpeg','validado','2025-11-08 22:16:26','2025-11-08 22:16:26',3,'2025-11-08 22:17:00'),(7,8,'sdasdadad','asdasdasdsadad','musica','{\"action\":\"save\",\"plataformas\":\"asdasdsad\",\"sello\":\"dasdsad\"}',NULL,'pendiente_validacion','2025-11-10 00:02:00','2025-11-10 00:07:01',NULL,NULL);
/*!40000 ALTER TABLE `publicaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_content`
--

DROP TABLE IF EXISTS `site_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_key` varchar(100) NOT NULL,
  `content_value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_key` (`content_key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_content`
--

LOCK TABLES `site_content` WRITE;
/*!40000 ALTER TABLE `site_content` DISABLE KEYS */;
INSERT INTO `site_content` VALUES (1,'welcome_title','Bienvenidos a ID Cultural'),(2,'welcome_paragraph','<strong>ID Cultural</strong> es una plataforma digital dedicada a visibilizar, preservar y promover la identidad artística y cultural de Santiago del Estero. Te invitamos a explorar, descubrir y formar parte de este espacio pensado para fortalecer nuestras raíces.'),(3,'welcome_slogan','La identidad de un pueblo, en un solo lugar.'),(4,'carousel_image_1','https://placehold.co/1200x450/367789/FFFFFF?text=Cultura+Santiagueña'),(5,'carousel_image_2','https://placehold.co/1200x450/C30135/FFFFFF?text=Nuestros+Artistas'),(6,'carousel_image_3','https://placehold.co/1200x450/efc892/333333?text=Biblioteca+Digital');
/*!40000 ALTER TABLE `site_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_logs`
--

DROP TABLE IF EXISTS `system_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_logs`
--

LOCK TABLES `system_logs` WRITE;
/*!40000 ALTER TABLE `system_logs` DISABLE KEYS */;
INSERT INTO `system_logs` VALUES (1,1,'Administrador Principal','INICIO DE SESIÓN','El usuario ha iniciado sesión correctamente.','2025-08-14 18:21:33'),(2,2,'Editor de Contenidos','CREACIÓN DE NOTICIA','Se ha creado la noticia con ID: 101.','2025-08-14 18:21:33'),(3,3,'Validador de Artistas','VALIDACIÓN DE ARTISTA','Se ha aprobado la solicitud del artista con ID: 1. Comentario: Excelente portfolio.','2025-08-14 18:21:33'),(4,3,'Validador de Artistas','RECHAZO DE ARTISTA','Se ha rechazado la solicitud del artista con ID: 2. Motivo: Faltan referencias comprobables.','2025-08-14 18:21:33'),(5,1,'Usuario Desconocido','VALIDACIÓN DE ARTISTA','Se ha validado la solicitud con ID: 1.','2025-08-14 18:39:49'),(6,1,'Admin','VALIDACIÓN DE ARTISTA','Se ha cambiado el estado del artista ID: 1 a validado.','2025-08-14 19:15:57'),(7,1,'Admin','VALIDACIÓN DE ARTISTA','Se ha cambiado el estado del artista ID: 4 a validado. Comentario: buen pibe','2025-08-14 19:21:30'),(8,1,'Admin','VALIDACIÓN DE ARTISTA','Se ha cambiado el estado del artista ID: 3 a validado.','2025-08-14 19:32:05'),(9,1,'Admin','RECHAZO DE ARTISTA','Se ha cambiado el estado del artista ID: 2 a rechazado. Motivo: ninguna cancion es tuya','2025-08-14 19:32:25'),(10,1,'Admin','VALIDACIÓN DE ARTISTA','Se ha cambiado el estado del artista ID: 7 a validado.','2025-10-27 05:21:21'),(11,3,' ','VALIDACIÓN DE PUBLICACIÓN','Publicación ID: 2 del artista \'marcos romano\' (ID: 8) ha sido validada.','2025-11-08 03:52:07'),(12,3,'validador','VALIDACIÓN DE PUBLICACIÓN','Publicación ID: 3 del artista \'Marcos Romano\' (ID: 8) ha sido validada.','2025-11-08 21:08:42'),(13,3,'validador','VALIDACIÓN DE PUBLICACIÓN','Publicación ID: 5 del artista \'Marcos Romano\' (ID: 8) ha sido validada.','2025-11-08 22:06:24'),(14,3,'validador','VALIDACIÓN DE PUBLICACIÓN','Publicación ID: 6 del artista \'Marcos Romano\' (ID: 8) ha sido validada.','2025-11-08 22:17:00');
/*!40000 ALTER TABLE `system_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador Principal','admin@idcultural.com','$2y$10$cv2EG9pZ/4y1H.z.QztN.OuGTO9x8resRsMrnJxdaKFPqreWtndf6','admin'),(2,'Editor de Contenidos','editor@idcultural.com','$2y$10$9/iW1.fVT0I8E2PiYzNGv.q5AKtnboEwl4rBAHuMgV2rVcDW6wd6W','editor'),(3,'Validador de Artistas','validador@idcultural.com','$2y$10$SFb4oh3S6IiTZ/LFr/e20uVodzb7n9u/I5OQu11A8AtoUNzns5QHW','validador');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-10  1:40:29
