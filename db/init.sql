-- =====================================================================
-- DescubreTarija - init.sql (MySQL 8.4)
-- Se carga SOLO la primera vez que arranca el contenedor tarija-db.
-- Cambiaste este archivo?  .\scripts\reiniciar-base.ps1
-- =====================================================================
SET NAMES utf8mb4;
USE descubretarija;

CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(40) NOT NULL UNIQUE,
  descripcion VARCHAR(200) NULL
);

CREATE TABLE destinos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL UNIQUE,
  descripcion TEXT NULL,
  ubicacion VARCHAR(100) NULL,
  latitud DECIMAL(9,6) NULL,
  longitud DECIMAL(9,6) NULL,
  categoria_id INT NULL,
  destacado BOOLEAN NOT NULL DEFAULT FALSE,
  verificado BOOLEAN NOT NULL DEFAULT FALSE,
  CONSTRAINT fk_destino_categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE imagenes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  destino_id INT NOT NULL,
  url VARCHAR(255) NOT NULL,
  descripcion VARCHAR(100) NULL,
  CONSTRAINT fk_imagen_destino
    FOREIGN KEY (destino_id) REFERENCES destinos(id)
);

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol ENUM('usuario','admin') NOT NULL DEFAULT 'usuario',
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE favoritos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  destino_id INT NOT NULL,
  guardado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_favorito_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  CONSTRAINT fk_favorito_destino
    FOREIGN KEY (destino_id) REFERENCES destinos(id),
  CONSTRAINT uq_favorito UNIQUE (usuario_id, destino_id)
);

CREATE TABLE recorridos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  nombre VARCHAR(80) NOT NULL,
  fecha_visita DATE NULL,
  CONSTRAINT fk_recorrido_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE recorrido_destinos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recorrido_id INT NOT NULL,
  destino_id INT NOT NULL,
  orden SMALLINT NOT NULL,
  CONSTRAINT fk_rd_recorrido
    FOREIGN KEY (recorrido_id) REFERENCES recorridos(id),
  CONSTRAINT fk_rd_destino
    FOREIGN KEY (destino_id) REFERENCES destinos(id),
  CONSTRAINT uq_rd UNIQUE (recorrido_id, destino_id)
);

-- ===== DATOS =====
INSERT INTO categorias (nombre, descripcion) VALUES
('Naturaleza', 'Paisajes y áreas naturales'),
('Cultura e historia', 'Museos y sitios históricos'),
('Gastronomía', 'Comida y bebidas típicas'),
('Aventura', 'Actividades al aire libre'),
('Turismo rural', 'Experiencias en el campo'),
('Festividades y tradiciones', 'Fiestas y costumbres');

-- coords NULL y textos POR VERIFICAR: no inventar datos
-- categorias asignadas = tentativas, revisar
INSERT INTO destinos (nombre, descripcion, ubicacion, categoria_id, destacado) VALUES
('Casa Dorada', 'POR VERIFICAR', 'POR VERIFICAR', 2, TRUE),
('Coimata', 'POR VERIFICAR', 'POR VERIFICAR', 1, FALSE),
('San Lorenzo', 'POR VERIFICAR', 'POR VERIFICAR', 5, TRUE),
('Valle de la Concepción', 'POR VERIFICAR', 'POR VERIFICAR', 3, TRUE),
('Represa de San Jacinto', 'POR VERIFICAR', 'POR VERIFICAR', 1, FALSE),
('Rincón de la Victoria', 'POR VERIFICAR', 'POR VERIFICAR', 5, FALSE),
('Reserva Biológica Cordillera de Sama', 'POR VERIFICAR', 'POR VERIFICAR', 1, FALSE),
('Cañón de la Angostura', 'POR VERIFICAR', 'POR VERIFICAR', 4, FALSE),
('Museo Paleontológico de Tarija', 'POR VERIFICAR', 'POR VERIFICAR', 2, FALSE);

INSERT INTO imagenes (destino_id, url, descripcion) VALUES
(1, 'assets/images/casa-dorada-1.jpg', 'Fachada principal'),
(1, 'assets/images/casa-dorada-2.jpg', 'Vista interior'),
(2, 'assets/images/coimata-1.jpg', 'Vista general'),
(3, 'assets/images/san-lorenzo-1.jpg', 'Plaza principal'),
(4, 'assets/images/valle-concepcion-1.jpg', 'Viñedos'),
(5, 'assets/images/san-jacinto-1.jpg', 'Vista de la represa'),
(9, 'assets/images/museo-paleo-1.jpg', 'Entrada del museo');

-- hashes falsos: se reemplazan por password_hash() real en la Fase 4
INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES
('Administrador DescubreTarija', 'admin@descubretarija.bo', 'HASH_DEMO_1', 'admin'),
('María Peñaranda', 'maria@correo.com', 'HASH_DEMO_2', 'usuario'),
('José Ñanco', 'jose@correo.com', 'HASH_DEMO_3', 'usuario'),
('Lucía Vargas', 'lucia@correo.com', 'HASH_DEMO_4', 'usuario'),
('Andrés Condori', 'andres@correo.com', 'HASH_DEMO_5', 'usuario');

INSERT INTO favoritos (usuario_id, destino_id) VALUES
(2, 1), (2, 4), (2, 8),
(3, 1), (3, 3),
(4, 4), (4, 9),
(5, 2);

INSERT INTO recorridos (usuario_id, nombre, fecha_visita) VALUES
(2, 'Fin de semana en Tarija', '2026-12-05'),
(2, 'Ruta del vino', NULL),
(3, 'Paseo familiar', '2026-12-12'),
(4, 'Cultura e historia', NULL),
(5, 'Aventura y naturaleza', '2027-01-10');

INSERT INTO recorrido_destinos (recorrido_id, destino_id, orden) VALUES
(1, 1, 1), (1, 9, 2), (1, 3, 3),
(2, 4, 1), (2, 3, 2),
(3, 5, 1), (3, 2, 2),
(4, 9, 1), (4, 1, 2),
(5, 8, 1), (5, 7, 2);