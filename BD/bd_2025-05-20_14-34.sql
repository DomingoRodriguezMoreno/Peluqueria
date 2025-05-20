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
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citas`
--

LOCK TABLES `citas` WRITE;
/*!40000 ALTER TABLE `citas` DISABLE KEYS */;
INSERT INTO `citas` VALUES (23,1,'2025-05-06','09:01:00','13:01:00','finalizada',240,1.00),(24,1,'2025-05-07','09:13:00','13:13:00','finalizada',240,1.00),(25,1,'2025-05-08','09:18:00','13:18:00','finalizada',240,1.00),(26,1,'2025-05-06','09:07:00','13:07:00','finalizada',240,1.00),(27,1,'2025-05-06','09:07:00','09:57:00','finalizada',50,20.50),(28,1,'2025-05-06','09:07:00','09:37:00','finalizada',30,20.01),(39,1,'2025-05-09','09:30:00','10:20:00','cancelada',50,20.50),(40,1,'2025-05-08','09:00:00','13:00:00','finalizada',240,1.00),(43,1,'2025-05-14','09:00:00','10:00:00','cancelada',60,50.00),(44,1,'2025-05-13','10:01:00','11:01:00','finalizada',60,50.00),(45,2,'2025-05-20','10:51:00','11:21:00','cancelada',30,20.01),(46,2,'2025-05-26','11:07:00','12:07:00','cancelada',60,50.00),(47,1,'2025-05-25','11:12:00','12:12:00','cancelada',60,50.00),(48,2,'2025-05-23','09:31:00','10:51:00','cancelada',80,40.51),(51,2,'2025-05-30','10:34:00','11:34:00','cancelada',60,50.00),(53,3,'2025-05-30','09:29:00','10:49:00','cancelada',80,40.51),(55,3,'2025-05-28','10:46:00','11:46:00','reservada',60,50.00),(56,2,'2025-05-21','11:40:00','12:55:00','reservada',75,50.01),(58,1,'2025-05-29','13:07:00','13:37:00','cancelada',30,20.01),(59,1,'2025-05-26','11:07:00','12:07:00','cancelada',60,50.00),(62,1,'2025-06-19','10:00:00','10:30:00','cancelada',30,40.51),(66,1,'2025-05-28','09:18:00','09:48:00','cancelada',30,40.51),(67,1,'2025-06-19','09:20:00','09:50:00','cancelada',30,40.51),(71,1,'2025-05-26','09:08:00','10:43:00','reservada',95,50.50),(72,1,'2025-05-29','10:09:00','11:29:00','reservada',80,40.51);
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
INSERT INTO `citas_servicios` VALUES (23,10,'12345678A'),(24,10,'12345678A'),(25,10,'12345678A'),(26,10,'12345678A'),(27,4,'12345678A'),(28,1,'12345678A'),(39,4,'12345678A'),(43,2,'12345678A'),(44,2,'12345678A'),(45,1,'12345678A'),(46,2,'12345678A'),(47,2,'12345678A'),(48,1,'12345678A'),(51,2,'12345678A'),(53,1,'12345678A'),(55,2,'12345678A'),(56,1,'12345678A'),(58,1,'12345678A'),(59,2,'12345678A'),(62,1,'12345678A'),(62,4,'12345678A'),(66,1,'12345678A'),(66,4,'12345678A'),(67,1,'12345678A'),(67,4,'12345678A'),(71,4,'12345678A'),(72,1,'12345678A'),(48,4,'12345678B'),(53,4,'12345678B'),(56,3,'12345678X'),(71,3,'12345678X'),(40,10,'87654321X'),(72,4,'87654321X');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'dominrodri5@gmail.com','666777999','Domingo','Rodriguez MorenA','$2y$10$7f5PKdGT2OdjkVSphLN0G.CeoSvdeFKP5BMYXlJdHIsdr7k5Y7mZG'),(2,'drodmor970@g.educaand.es','671673501','Paco','Pepe','$2y$10$8z.OFRDgrsSMTTD006ogBerBpvTxxFYmYZVBsZ3ukQ1YVtFe5H2ua'),(3,'d@g','000000000','pedri','b','$2y$10$ef8QBpnoSL8PFlSaC11wf.zyyUMsT5H8rGF2CkHqyslI8Q9YwSqOy'),(4,'1@2','000000001','Domingo','Rodriguez Moreno','$2y$10$eat0tKZtOwKhO1IS1tgacO11XV8oiuikhfPjB70Rxw0v7QkVpY1f2');
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
INSERT INTO `empleados` VALUES ('12345678A','Juan','Pérez García','600111222','juan@peluqueria.com',1,'$2y$10$eane7EyENEvum8sMvryDceDWOVmmbLrYk0OaJ8vh9NiuHhLQGtuOi',1,1),('12345678B','Q','a','111111222','1@2',1,'$2y$10$48cFyhgQ/oVC9b8MBG/hsePMeV/nu1X8KLsqV2J0Z5DT9k948RE4C',0,0),('12345678J','A','B','123456789','K@c',1,'$2y$10$pKSdzxbej4FBFM0VusRRFeCCmH59Sj7JWDREy7zL4uR5AQkO4.C1y',0,0),('12345678X','Laura','Calleja','666111666','L@c',2,'$2y$10$Qb4dFFDDd8Omy.tyoRF8Ze5TpdLMTdmCcV55pYKSYbG/ELBYmQJr.',0,1),('12345678Z','Francisco','Rene ','102304506','1@Z',1,'$2y$10$Hft5Ij2223p7ISAyoHUAEunzEtb7dPbJCrSIRGw8xdKA9mcUhAWT6',0,0),('32154687A','Domingo','Rodriguez Moreno','671673501','aa@Z',4,'$2y$10$qKIWLqvEmHJWhDL0SRz44e0F0g.ZTxDTrHomaHd2BcnUZoE1c.Kyi',0,1),('87654321X','Cafca','lumne','123321123','cafca@L',1,'$2y$10$XUKwD7Du7b9S8OEmAR6UNOEc1aC/LTnP7niacfJFd1ltMI.Eu33P.',1,1);
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

-- Dump completed on 2025-05-20 14:34:44
