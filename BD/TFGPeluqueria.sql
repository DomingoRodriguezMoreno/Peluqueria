create database TFGPeluqueria;
use TFGPeluqueria;

-- Roles
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);

INSERT INTO roles (nombre_rol) VALUES
('Peluquero'),
('Esteticien'),
('Administrador');

-- Empleados
CREATE TABLE empleados (
    dni VARCHAR(20) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    email VARCHAR(100),
    id_rol INT,
    contraseña VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

INSERT INTO empleados (dni, nombre, apellidos, telefono, email, id_rol, contraseña) VALUES
('12345678A', 'Juan', 'Pérez García', '600111222', 'juan@peluqueria.com', 3, '$2y$10$j7zMPVbssrj4.wdaDDDBa.Z.u1l9VecAccwRWOAjlaRVP3ZG4ckYy');

-- Servicios
CREATE TABLE servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_servicio VARCHAR(100) NOT NULL,
    descripcion TEXT,
    duracion INT NOT NULL,  -- Duración en minutos
    precio DECIMAL(10, 2) NOT NULL
);
INSERT INTO servicios (nombre_servicio, descripcion, duracion, precio) VALUES
('Corte de caballero', 'Corte moderno y personalizado.', 30, 20.00),
('Coloración', 'Tinte y mechas profesionales.', 60, 50.00),
('Depilación facial', 'Depilación con cera en zona facial.', 45, 30.00),
('Corte de mujer', 'corte de cabello para mujeres', 50, 20.50);

-- Roles_servicios
CREATE TABLE roles_servicios (
    id_rol INT,
    id_servicio INT,
    PRIMARY KEY (id_rol, id_servicio),
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol),
    FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio)
);
INSERT INTO roles_servicios (id_rol, id_servicio) VALUES
(1, 1), -- Peluquero: Corte de caballero
(1, 2), -- Peluquero: Coloración
(2, 3), -- Esteticien: Depilación facial
(1, 4); -- Peluquero: Corte de mujer

-- Tipos_tratamiento
CREATE TABLE tipos_tratamiento (
    id_tipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre_tipo VARCHAR(100) NOT NULL UNIQUE
);

-- Insertar tipos
INSERT INTO tipos_tratamiento (nombre_tipo) VALUES
('Cortes'),
('Tintes'),
('Barbas'),
('Depilación');

-- Servicios_tipos
CREATE TABLE servicios_tipos (
    id_servicio INT,
    id_tipo INT,
    PRIMARY KEY (id_servicio, id_tipo),
    FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio),
    FOREIGN KEY (id_tipo) REFERENCES tipos_tratamiento(id_tipo)
);

-- Asignar tipos a servicios (ejemplo)
INSERT INTO servicios_tipos (id_servicio, id_tipo) VALUES
(1, 1), -- Corte de caballero -> Cortes
(2, 2), -- Coloración -> Tintes
(3, 4), -- Depilación facial -> Depilación
(4, 1); -- Corte de mujer -> Cortes

-- Clientes
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(15) UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    contraseña VARCHAR(255) NOT NULL
);

-- Citas
CREATE TABLE citas (
    id_cita INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    fecha_cita DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    estado ENUM('reservada', 'cancelada', 'finalizada') NOT NULL,
    duracion_total INT NOT NULL,  -- Duración total en minutos
    precio_final DECIMAL(10, 2),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);

-- Citas_servicios
CREATE TABLE citas_servicios (
    id_cita INT,
    id_servicio INT,
    id_empleado VARCHAR(20),
    PRIMARY KEY (id_cita, id_servicio),
    FOREIGN KEY (id_cita) REFERENCES citas(id_cita),
    FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio),
    FOREIGN KEY (id_empleado) REFERENCES empleados(dni)
);

-- Habilitar el planificador de eventos (solo si no está activado)
SET GLOBAL event_scheduler = ON;

-- Crear evento para actualizar el estado de las citas
CREATE EVENT actualizar_estado_citas
ON SCHEDULE EVERY 1 MINUTE
STARTS CURRENT_TIMESTAMP
DO
    UPDATE citas
    SET estado = 'finalizada'
    WHERE estado = 'reservada'
    AND CONCAT(fecha_cita, ' ', hora_fin) < NOW()