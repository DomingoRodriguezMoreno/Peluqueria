create database TFGPeluqueria;
use TFGPeluqueria;

-- Roles
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);

INSERT INTO roles (nombre_rol) VALUES
('Peluquero'),
('Esteticien');

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
('12345678A', 'Juan', 'Pérez García', '600111222', 'juan@peluqueria.com', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password123 
('87654321B', 'María', 'López Sánchez', '600333444', 'maria@peluqueria.com', 2, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- beauty456 

-- Servicios
CREATE TABLE servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_servicio VARCHAR(100) NOT NULL,
    descripcion TEXT,
    duracion INT NOT NULL,  -- Duración en minutos
    precio DECIMAL(10, 2) NOT NULL
);
INSERT INTO servicios (nombre_servicio, descripcion, duracion, precio) VALUES
('Corte de cabello', 'Corte moderno y personalizado.', 30, 20.00),
('Coloración', 'Tinte y mechas profesionales.', 60, 50.00),
('Depilación facial', 'Depilación con cera en zona facial.', 45, 30.00);

-- Roles_servicios
CREATE TABLE roles_servicios (
    id_rol INT,
    id_servicio INT,
    PRIMARY KEY (id_rol, id_servicio),
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol),
    FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio)
);
INSERT INTO roles_servicios (id_rol, id_servicio) VALUES
(1, 1), -- Peluquero: Corte de cabello
(1, 2), -- Peluquero: Coloración
(2, 3); -- Esteticien: Depilación facial

-- Clientes
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(15) UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    contraseña VARCHAR(255) NOT NULL
);

INSERT INTO clientes (email, telefono, nombre, apellidos, contraseña) VALUES
('cliente@example.com', '600555666', 'Ana', 'Gómez Martínez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- cliente789 

-- Citas
CREATE TABLE citas (
    id_cita INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    fecha_cita DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada') NOT NULL,
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
