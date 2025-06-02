-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: TFGPeluqueria
-- ------------------------------------------------------
-- Server version	8.0.42-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `citas`
--

DROP TABLE IF EXISTS `citas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citas` (
  `id_cita` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int DEFAULT NULL,
  `fecha_cita` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time DEFAULT NULL,
  `estado` enum('reservada','cancelada','finalizada') COLLATE utf8mb4_general_ci NOT NULL,
  `duracion_total` int NOT NULL,
  `precio_final` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_cita`),
  KEY `id_cliente` (`id_cliente`),
  KEY `idx_fecha_cita` (`fecha_cita`),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citas`
--

LOCK TABLES `citas` WRITE;
/*!40000 ALTER TABLE `citas` DISABLE KEYS */;
INSERT INTO `citas` VALUES (1,7,'2025-05-26','09:06:00','10:36:00','cancelada',90,70.01),(2,7,'2025-05-27','09:12:00','10:42:00','finalizada',90,70.01),(3,5,'2025-05-28','09:01:00','10:21:00','finalizada',80,40.51),(7,5,'2025-05-28','09:02:00','09:47:00','finalizada',45,30.00),(15,7,'2025-05-29','09:19:00','09:49:00','finalizada',30,20.01),(16,7,'2025-05-29','09:26:00','10:11:00','finalizada',45,30.00),(17,7,'2025-05-30','09:33:00','10:33:00','cancelada',60,50.00),(18,7,'2025-05-30','09:37:00','10:37:00','cancelada',60,50.00),(19,7,'2025-05-30','09:45:00','10:45:00','cancelada',60,50.00),(20,7,'2025-05-30','09:17:00','10:17:00','cancelada',60,50.00),(21,7,'2025-05-30','09:33:00','10:33:00','cancelada',60,50.00),(24,7,'2025-05-30','09:07:00','10:07:00','cancelada',60,50.00),(28,12,'2025-06-02','09:09:00','10:39:00','cancelada',90,70.01),(29,12,'2025-06-04','09:08:00','10:38:00','reservada',90,70.01);
/*!40000 ALTER TABLE `citas` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `validar_precio_final` BEFORE UPDATE ON `citas` FOR EACH ROW BEGIN
    DECLARE total_precio DECIMAL(10,2);

    SELECT SUM(s.precio) INTO total_precio
    FROM servicios s
    JOIN citas_servicios cs ON s.id_servicio = cs.id_servicio
    WHERE cs.id_cita = NEW.id_cita;

    IF NEW.precio_final != total_precio THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El precio final no coincide con la suma de los servicios.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `citas_servicios`
--

DROP TABLE IF EXISTS `citas_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citas_servicios` (
  `id_cita` int NOT NULL,
  `id_servicio` int NOT NULL,
  `id_empleado` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_cita`,`id_servicio`),
  KEY `id_servicio` (`id_servicio`),
  KEY `idx_citas_empleado` (`id_empleado`),
  KEY `idx_id_empleado` (`id_empleado`),
  CONSTRAINT `citas_servicios_ibfk_1` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id_cita`),
  CONSTRAINT `citas_servicios_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`),
  CONSTRAINT `citas_servicios_ibfk_3` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citas_servicios`
--

LOCK TABLES `citas_servicios` WRITE;
/*!40000 ALTER TABLE `citas_servicios` DISABLE KEYS */;
INSERT INTO `citas_servicios` VALUES (1,1,'12345678A'),(2,1,'12345678A'),(3,1,'12345678A'),(15,1,'12345678A'),(17,2,'12345678A'),(18,2,'12345678A'),(19,2,'12345678A'),(20,2,'12345678A'),(21,2,'12345678A'),(24,2,'12345678A'),(28,1,'12345678A'),(29,1,'12345678A'),(7,3,'12345678X'),(16,3,'12345678X'),(1,2,'87654321X'),(2,2,'87654321X'),(3,4,'87654321X'),(28,2,'87654321X'),(29,2,'87654321X');
/*!40000 ALTER TABLE `citas_servicios` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `asignar_empleado_disponible` BEFORE INSERT ON `citas_servicios` FOR EACH ROW BEGIN
    DECLARE fecha_cita DATE;
    DECLARE hora_inicio TIME;
    DECLARE duracion_servicio INT;
    DECLARE hora_fin_servicio TIME;
    DECLARE empleado_id VARCHAR(20);
    DECLARE rol_requerido INT;

    
    SELECT c.fecha_cita, c.hora_inicio 
    INTO fecha_cita, hora_inicio
    FROM citas c
    WHERE c.id_cita = NEW.id_cita;

    
    SELECT s.duracion INTO duracion_servicio
    FROM servicios s
    WHERE s.id_servicio = NEW.id_servicio;

    
    SET hora_fin_servicio = ADDTIME(hora_inicio, SEC_TO_TIME(duracion_servicio * 60));

    
    SELECT rs.id_rol INTO rol_requerido
    FROM roles_servicios rs
    WHERE rs.id_servicio = NEW.id_servicio
    LIMIT 1;

    
    SELECT e.dni INTO empleado_id
    FROM empleados e
    WHERE e.id_rol = rol_requerido
    AND e.activo = 1
    AND NOT EXISTS (
        SELECT 1
        FROM citas_servicios cs
        JOIN citas c ON cs.id_cita = c.id_cita
        WHERE cs.id_empleado = e.dni
        AND c.fecha_cita = fecha_cita
        AND c.estado = 'reservada'  
        AND (
            (c.hora_inicio < hora_fin_servicio AND c.hora_fin > hora_inicio)
        )
    )
    LIMIT 1;

    
    IF empleado_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No hay empleados disponibles para este servicio.';
    ELSE
        SET NEW.id_empleado = empleado_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`domingo`@`localhost`*/ /*!50003 TRIGGER `actualizar_duracion_precio` AFTER INSERT ON `citas_servicios` FOR EACH ROW BEGIN
    CALL ActualizarCita(NEW.id_cita);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`domingo`@`localhost`*/ /*!50003 TRIGGER `actualizar_al_eliminar` AFTER DELETE ON `citas_servicios` FOR EACH ROW BEGIN
    CALL ActualizarCita(OLD.id_cita);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `contraseña` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `telefono` (`telefono`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (5,'maribelmorenocalvo54@gmail.com','676451222','Maria Isabel ','Moreno','$2y$10$2nmva6efn06rxd.v7choMOrfE1MaW9fzo.OFUIy5mxfG64NkoKQ1a'),(7,'dominrodri5@gmail.com','671673501','Domingo','Rodriguez Moreno','$2y$10$1CtjMpxUjdj8FPcMOOo7SeEVEgw8w.MRIPsq0eHEQQcmhIPdZ44IO'),(12,'drodmor970@g.educaand.es','676451223','Domingo','Rodriguez Moreno','$2y$10$NwFLBQ5nm0KV.T1hcEdqb.kKhf8FffHt/AWBf2XgBQtQSct6sCULe');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `dni` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `apellidos` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_rol` int DEFAULT NULL,
  `contraseña` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `es_admin` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`dni`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES ('12345678A','Juan','Pérez García','600111222','juan@peluqueria.com',1,'$2y$10$eane7EyENEvum8sMvryDceDWOVmmbLrYk0OaJ8vh9NiuHhLQGtuOi',1,1),('12345678X','Laura','Calleja','666111666','laur@correo.com',2,'$2y$10$uE36He6rTd31jq1Fa5VbDeMgf0up03x3qY0Ty.MxkhbeeNpdsc0l2',1,1),('12345678Z','Francisco','Rene ','102304506','francis@correo.net',2,'$2y$10$Hft5Ij2223p7ISAyoHUAEunzEtb7dPbJCrSIRGw8xdKA9mcUhAWT6',0,1),('32091870Y','Pedro','Garcia','666111999','pedro@correo.es',4,'$2y$10$PX1DQDaGNWzrrxOiS8V2a.8KnIHRWz3GqDrc0A6oPHSBpvwTGjKE6',1,1),('32154687A','Domingo','Rodriguez Moreno','671673501','dominrodri5@gmail.com',4,'$2y$10$pM/451Ebl8fH.SfQ9pXptuuTmdUrQwten4K30aPmczUn26FfJT3cK',0,0),('87654321Q','Prueba','1','123456789','prueba@correo',1,'$2y$10$/VOhZPcZ3GxR1UiOFfEGTOMQGWIqse0YpA/1ZomvO9L2OTjGIXc1S',0,0),('87654321X','Cafca','lumne','123321123','cafca@lambda.com',1,'$2y$10$XUKwD7Du7b9S8OEmAR6UNOEc1aC/LTnP7niacfJFd1ltMI.Eu33P.',0,1);
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Peluquero'),(2,'Esteticien'),(4,'Recepcionista');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_servicios`
--

DROP TABLE IF EXISTS `roles_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_servicios` (
  `id_rol` int NOT NULL,
  `id_servicio` int NOT NULL,
  PRIMARY KEY (`id_rol`,`id_servicio`),
  KEY `id_servicio` (`id_servicio`),
  CONSTRAINT `roles_servicios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`),
  CONSTRAINT `roles_servicios_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_servicios`
--

LOCK TABLES `roles_servicios` WRITE;
/*!40000 ALTER TABLE `roles_servicios` DISABLE KEYS */;
INSERT INTO `roles_servicios` VALUES (1,1),(1,2),(2,3),(1,4),(1,10);
/*!40000 ALTER TABLE `roles_servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicios`
--

DROP TABLE IF EXISTS `servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servicios` (
  `id_servicio` int NOT NULL AUTO_INCREMENT,
  `nombre_servicio` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `duracion` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicios`
--

LOCK TABLES `servicios` WRITE;
/*!40000 ALTER TABLE `servicios` DISABLE KEYS */;
INSERT INTO `servicios` VALUES (1,'Corte de caballero','Corte moderno y personalizado.',30,20.01,1),(2,'Coloración','Tinte y mechas profesionales.',60,50.00,1),(3,'Depilación facial','Depilación con cera en zona facial.',45,30.00,1),(4,'Corte de mujer','corte de cabello para mujeres',50,20.50,1),(10,'Prueba 4','tratamiento de 4 horas',240,1.00,0);
/*!40000 ALTER TABLE `servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicios_tipos`
--

DROP TABLE IF EXISTS `servicios_tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servicios_tipos` (
  `id_servicio` int NOT NULL,
  `id_tipo` int NOT NULL,
  PRIMARY KEY (`id_servicio`,`id_tipo`),
  KEY `id_tipo` (`id_tipo`),
  CONSTRAINT `servicios_tipos_ibfk_1` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`),
  CONSTRAINT `servicios_tipos_ibfk_2` FOREIGN KEY (`id_tipo`) REFERENCES `tipos_tratamiento` (`id_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicios_tipos`
--

LOCK TABLES `servicios_tipos` WRITE;
/*!40000 ALTER TABLE `servicios_tipos` DISABLE KEYS */;
INSERT INTO `servicios_tipos` VALUES (1,1),(4,1),(2,2),(10,3),(3,4);
/*!40000 ALTER TABLE `servicios_tipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_tratamiento`
--

DROP TABLE IF EXISTS `tipos_tratamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_tratamiento` (
  `id_tipo` int NOT NULL AUTO_INCREMENT,
  `nombre_tipo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_tipo`),
  UNIQUE KEY `nombre_tipo` (`nombre_tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_tratamiento`
--

LOCK TABLES `tipos_tratamiento` WRITE;
/*!40000 ALTER TABLE `tipos_tratamiento` DISABLE KEYS */;
INSERT INTO `tipos_tratamiento` VALUES (3,'Barbas'),(1,'Cortes'),(4,'Depilación'),(2,'Tintes');
/*!40000 ALTER TABLE `tipos_tratamiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'TFGPeluqueria'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `actualizar_estado_citas` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8mb4 */ ;;
/*!50003 SET character_set_results = utf8mb4 */ ;;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = '+00:00' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`root`@`localhost`*/ /*!50106 EVENT `actualizar_estado_citas` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-03-31 16:47:43' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE citas
    SET estado = 'finalizada'
    WHERE estado = 'reservada'
    AND TIMESTAMP(fecha_cita, hora_fin) < UTC_TIMESTAMP();
END */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'TFGPeluqueria'
--
/*!50003 DROP PROCEDURE IF EXISTS `ActualizarCita` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`domingo`@`localhost` PROCEDURE `ActualizarCita`(IN `cita_id` INT)
BEGIN
    DECLARE total_duracion INT;
    DECLARE nueva_hora_fin TIME;
    DECLARE hora_inicio TIME;
    DECLARE fecha_cita DATE;

    
    SELECT c.hora_inicio, c.fecha_cita 
    INTO hora_inicio, fecha_cita
    FROM citas c
    WHERE c.id_cita = cita_id;

    
    SELECT SUM(s.duracion) INTO total_duracion
    FROM servicios s
    JOIN citas_servicios cs ON s.id_servicio = cs.id_servicio
    WHERE cs.id_cita = cita_id;

    
    SET nueva_hora_fin = ADDTIME(hora_inicio, SEC_TO_TIME(total_duracion * 60));

    
    UPDATE citas
    SET
        duracion_total = total_duracion,
        precio_final = (
            SELECT SUM(s.precio)
            FROM servicios s
            JOIN citas_servicios cs ON s.id_servicio = cs.id_servicio
            WHERE cs.id_cita = cita_id
        ),
        hora_fin = nueva_hora_fin
    WHERE id_cita = cita_id;

    
    IF NOT (
        (hora_inicio >= '09:00:00' AND nueva_hora_fin <= '14:00:00') OR
        (hora_inicio >= '16:00:00' AND nueva_hora_fin <= '19:00:00')
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Horario no válido (9:00-14:00 y 16:00-19:00)';
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-02  8:06:14
