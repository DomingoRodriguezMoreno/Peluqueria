-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-05-2025 a las 11:38:28
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
-- Base de datos: `tfgpeluqueria`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ActualizarCita` (IN `cita_id` INT)   BEGIN
    DECLARE total_duracion INT;
    DECLARE nueva_hora_fin TIME;
    DECLARE hora_inicio TIME;
    DECLARE fecha_cita DATE;

    -- Obtener datos de la cita
    SELECT c.hora_inicio, c.fecha_cita 
    INTO hora_inicio, fecha_cita
    FROM citas c
    WHERE c.id_cita = cita_id;

    -- Calcular duración total
    SELECT SUM(s.duracion) INTO total_duracion
    FROM servicios s
    JOIN citas_servicios cs ON s.id_servicio = cs.id_servicio
    WHERE cs.id_cita = cita_id;

    -- Calcular hora_fin
    SET nueva_hora_fin = ADDTIME(hora_inicio, SEC_TO_TIME(total_duracion * 60));

    -- Actualizar campos en citas
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

    -- Validar horario
    IF NOT (
        (hora_inicio >= '09:00:00' AND nueva_hora_fin <= '14:00:00') 
        OR 
        (hora_inicio >= '16:00:00' AND nueva_hora_fin <= '19:00:00')
    ) THEN
        -- Eliminar la cita si no cumple el horario
        DELETE FROM citas WHERE id_cita = cita_id;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Horario no válido (9:00-14:00 y 16:00-19:00)';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id_cita` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `fecha_cita` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time DEFAULT NULL,
  `estado` enum('reservada','cancelada','finalizada') NOT NULL,
  `duracion_total` int(11) NOT NULL,
  `precio_final` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id_cita`, `id_cliente`, `fecha_cita`, `hora_inicio`, `hora_fin`, `estado`, `duracion_total`, `precio_final`) VALUES
(23, 1, '2025-05-06', '09:01:00', '13:01:00', 'reservada', 240, 1.00),
(24, 1, '2025-05-07', '09:13:00', '13:13:00', 'reservada', 240, 1.00),
(25, 1, '2025-05-08', '09:18:00', '13:18:00', 'reservada', 240, 1.00),
(26, 1, '2025-05-06', '09:07:00', '13:07:00', 'reservada', 240, 1.00),
(27, 1, '2025-05-06', '09:07:00', '09:57:00', 'reservada', 50, 20.50),
(28, 1, '2025-05-06', '09:07:00', '09:37:00', 'reservada', 30, 20.01);

--
-- Disparadores `citas`
--
DELIMITER $$
CREATE TRIGGER `validar_precio_final` BEFORE UPDATE ON `citas` FOR EACH ROW BEGIN
    DECLARE total_precio DECIMAL(10,2);

    SELECT SUM(s.precio) INTO total_precio
    FROM servicios s
    JOIN citas_servicios cs ON s.id_servicio = cs.id_servicio
    WHERE cs.id_cita = NEW.id_cita;

    IF NEW.precio_final != total_precio THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El precio final no coincide con la suma de los servicios.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas_servicios`
--

CREATE TABLE `citas_servicios` (
  `id_cita` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `id_empleado` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas_servicios`
--

INSERT INTO `citas_servicios` (`id_cita`, `id_servicio`, `id_empleado`) VALUES
(23, 10, '12345678A'),
(24, 10, '12345678A'),
(25, 10, '12345678A'),
(26, 10, '12345678A'),
(27, 4, '12345678A'),
(28, 1, '12345678A');

--
-- Disparadores `citas_servicios`
--
DELIMITER $$
CREATE TRIGGER `actualizar_duracion_precio` AFTER INSERT ON `citas_servicios` FOR EACH ROW BEGIN
    CALL ActualizarCita(NEW.id_cita);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `actualizar_duracion_precio_update` AFTER UPDATE ON `citas_servicios` FOR EACH ROW BEGIN
    CALL ActualizarCita(NEW.id_cita);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `validar_disponibilidad_empleado` BEFORE INSERT ON `citas_servicios` FOR EACH ROW BEGIN
    DECLARE nueva_hora_inicio TIME;
    DECLARE nueva_fecha DATE;
    DECLARE total_duracion INT;
    DECLARE nueva_hora_fin TIME;
    DECLARE overlap_exists INT;

    -- Paso 1: Obtener hora_inicio y fecha de la nueva cita
    SELECT c.hora_inicio, c.fecha_cita 
    INTO nueva_hora_inicio, nueva_fecha
    FROM citas c
    WHERE c.id_cita = NEW.id_cita;

    -- Paso 2: Calcular duración total de los servicios de la nueva cita
    SELECT SUM(s.duracion) 
    INTO total_duracion
    FROM servicios s
    JOIN citas_servicios cs ON s.id_servicio = cs.id_servicio
    WHERE cs.id_cita = NEW.id_cita;

    -- Paso 3: Calcular hora_fin de la nueva cita
    SET nueva_hora_fin = ADDTIME(nueva_hora_inicio, SEC_TO_TIME(total_duracion * 60));

    -- Paso 4: Buscar solapamientos con otras citas del mismo empleado
    SELECT COUNT(*) INTO overlap_exists
    FROM citas c
    JOIN citas_servicios cs ON c.id_cita = cs.id_cita
    WHERE cs.id_empleado = NEW.id_empleado
    AND c.fecha_cita = nueva_fecha
    AND c.id_cita != NEW.id_cita  -- Excluir la cita actual
    AND (
        (c.hora_inicio < nueva_hora_fin) AND 
        (c.hora_fin > nueva_hora_inicio)
    );

    -- Paso 5: Lanzar error si hay solapamiento
    IF overlap_exists > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El empleado ya tiene una cita en este horario.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `email`, `telefono`, `nombre`, `apellidos`, `contraseña`) VALUES
(1, 'dominrodri5@gmail.com', '666777999', 'Domingo', 'Rodriguez MorenA', '$2y$10$7f5PKdGT2OdjkVSphLN0G.CeoSvdeFKP5BMYXlJdHIsdr7k5Y7mZG'),
(2, 'dominrodri@gmail.com', '671673501', 'Paco', 'Pepe', '$2y$10$8z.OFRDgrsSMTTD006ogBerBpvTxxFYmYZVBsZ3ukQ1YVtFe5H2ua'),
(3, 'd@g', '000000000', 'pedri', 'a', '$2y$10$XIkUjtfyhi5lYNW8GK/RXeJNvOCXXYZFbFl9RzBFlhZyvRiKSSPDy'),
(4, '1@2', '000000001', 'Domingo', 'Rodriguez Moreno', '$2y$10$eat0tKZtOwKhO1IS1tgacO11XV8oiuikhfPjB70Rxw0v7QkVpY1f2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `dni` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `contraseña` varchar(255) NOT NULL,
  `es_admin` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`dni`, `nombre`, `apellidos`, `telefono`, `email`, `id_rol`, `contraseña`, `es_admin`, `activo`) VALUES
('12345678A', 'Juan', 'Pérez García', '600111222', 'juan@peluqueria.com', 1, '$2y$10$eane7EyENEvum8sMvryDceDWOVmmbLrYk0OaJ8vh9NiuHhLQGtuOi', 1, 1),
('12345678B', 'Q', 'a', '111111222', '1@2', 1, '$2y$10$48cFyhgQ/oVC9b8MBG/hsePMeV/nu1X8KLsqV2J0Z5DT9k948RE4C', 0, 0),
('32154687A', 'Domingo', 'Rodriguez Moreno', '671673501', 'aa@Z', 4, '$2y$10$qKIWLqvEmHJWhDL0SRz44e0F0g.ZTxDTrHomaHd2BcnUZoE1c.Kyi', 0, 1),
('87654321X', 'Cafca', 'lumne', '123321123', 'cafca@L', 1, '$2y$10$XUKwD7Du7b9S8OEmAR6UNOEc1aC/LTnP7niacfJFd1ltMI.Eu33P.', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'Peluquero'),
(2, 'Esteticien'),
(4, 'Recepcionista');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_servicios`
--

CREATE TABLE `roles_servicios` (
  `id_rol` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles_servicios`
--

INSERT INTO `roles_servicios` (`id_rol`, `id_servicio`) VALUES
(1, 1),
(1, 2),
(1, 4),
(1, 10),
(2, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `nombre_servicio` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `duracion` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre_servicio`, `descripcion`, `duracion`, `precio`, `activo`) VALUES
(1, 'Corte de caballero', 'Corte moderno y personalizado.', 30, 20.01, 1),
(2, 'Coloración', 'Tinte y mechas profesionales.', 60, 50.00, 1),
(3, 'Depilación facial', 'Depilación con cera en zona facial.', 45, 30.00, 1),
(4, 'Corte de mujer', 'corte de cabello para mujeres', 50, 20.50, 1),
(10, 'Prueba 4', 'tratamiento de 4 horas', 240, 1.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_tipos`
--

CREATE TABLE `servicios_tipos` (
  `id_servicio` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios_tipos`
--

INSERT INTO `servicios_tipos` (`id_servicio`, `id_tipo`) VALUES
(1, 1),
(2, 2),
(3, 4),
(4, 1),
(10, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_tratamiento`
--

CREATE TABLE `tipos_tratamiento` (
  `id_tipo` int(11) NOT NULL,
  `nombre_tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_tratamiento`
--

INSERT INTO `tipos_tratamiento` (`id_tipo`, `nombre_tipo`) VALUES
(3, 'Barbas'),
(1, 'Cortes'),
(4, 'Depilación'),
(2, 'Tintes');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id_cita`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `idx_fecha_cita` (`fecha_cita`);

--
-- Indices de la tabla `citas_servicios`
--
ALTER TABLE `citas_servicios`
  ADD PRIMARY KEY (`id_cita`,`id_servicio`),
  ADD KEY `id_servicio` (`id_servicio`),
  ADD KEY `idx_citas_empleado` (`id_empleado`),
  ADD KEY `idx_id_empleado` (`id_empleado`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telefono` (`telefono`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`dni`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `roles_servicios`
--
ALTER TABLE `roles_servicios`
  ADD PRIMARY KEY (`id_rol`,`id_servicio`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `servicios_tipos`
--
ALTER TABLE `servicios_tipos`
  ADD PRIMARY KEY (`id_servicio`,`id_tipo`),
  ADD KEY `id_tipo` (`id_tipo`);

--
-- Indices de la tabla `tipos_tratamiento`
--
ALTER TABLE `tipos_tratamiento`
  ADD PRIMARY KEY (`id_tipo`),
  ADD UNIQUE KEY `nombre_tipo` (`nombre_tipo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id_cita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tipos_tratamiento`
--
ALTER TABLE `tipos_tratamiento`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`);

--
-- Filtros para la tabla `citas_servicios`
--
ALTER TABLE `citas_servicios`
  ADD CONSTRAINT `citas_servicios_ibfk_1` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id_cita`),
  ADD CONSTRAINT `citas_servicios_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`),
  ADD CONSTRAINT `citas_servicios_ibfk_3` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`dni`);

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);

--
-- Filtros para la tabla `roles_servicios`
--
ALTER TABLE `roles_servicios`
  ADD CONSTRAINT `roles_servicios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`),
  ADD CONSTRAINT `roles_servicios_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`);

--
-- Filtros para la tabla `servicios_tipos`
--
ALTER TABLE `servicios_tipos`
  ADD CONSTRAINT `servicios_tipos_ibfk_1` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`),
  ADD CONSTRAINT `servicios_tipos_ibfk_2` FOREIGN KEY (`id_tipo`) REFERENCES `tipos_tratamiento` (`id_tipo`);

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `actualizar_estado_citas` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-03-31 16:47:43' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE citas
    SET estado = 'finalizada'
    WHERE estado = 'reservada'
    AND TIMESTAMP(fecha_cita, hora_fin) < UTC_TIMESTAMP();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
