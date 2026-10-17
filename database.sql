-- Sistema de Control de Asistencia
CREATE DATABASE IF NOT EXISTS control_asistencia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE control_asistencia;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS empleados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cedula VARCHAR(20) NOT NULL UNIQUE,
  nombre VARCHAR(120) NOT NULL,
  correo VARCHAR(120) NOT NULL,
  telefono VARCHAR(20) NOT NULL,
  codigo_acceso VARCHAR(6) NOT NULL UNIQUE,
  estado TINYINT(1) NOT NULL DEFAULT 1,
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS asistencias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  empleado_id INT NOT NULL,
  fecha DATE NOT NULL,
  hora_entrada TIME NOT NULL,
  hora_salida TIME NULL,
  FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
  INDEX idx_fecha (fecha)
) ENGINE=InnoDB;

-- Usuario admin: admin / password  (hash bcrypt, cambiar con generar_hash.php)
INSERT INTO usuarios (nombre, usuario, password, estado) VALUES
('Administrador', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

INSERT INTO empleados (cedula, nombre, correo, telefono, codigo_acceso, estado) VALUES
('1-1111-1111', 'Juan Pérez', 'juan@empresa.com', '8888-8888', 'A7F294', 1),
('2-2222-2222', 'María Rojas', 'maria@empresa.com', '7777-7777', 'B3C851', 1);