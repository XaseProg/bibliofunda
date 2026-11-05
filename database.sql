-- ============================================================
-- Base de datos: Biblioteca ISFT N° 240
-- ============================================================


-- ------------------------------------------------------------
-- Tabla: libros
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS libros (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  autor VARCHAR(255) NOT NULL,
  carrera VARCHAR(255) DEFAULT NULL,
  curso VARCHAR(255) DEFAULT NULL,
  materia VARCHAR(255) DEFAULT NULL,
  cantidad_total INT NOT NULL DEFAULT 1,
  cantidad_disponible INT NOT NULL DEFAULT 1,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabla: reservas
-- ------------------------------------------------------------
-- Estado: 'reservado' | 'prestado' | 'devuelto' | 'cancelado'
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  libro_id INT NOT NULL,
  nombre_completo VARCHAR(255) NOT NULL,
  telefono VARCHAR(100) NOT NULL,
  carrera_curso VARCHAR(255) NOT NULL,
  estado ENUM('reservado','prestado','devuelto','cancelado') NOT NULL DEFAULT 'reservado',
  fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_prestamo DATETIME DEFAULT NULL,
  fecha_devolucion DATETIME DEFAULT NULL,
  fecha_real_devolucion DATETIME DEFAULT NULL,
  FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabla: administradores
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS administradores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------

