-- =====================================================================
--  OPERACIÓN FOGÓN · la despensa (base de datos "fogon")
--  Se carga sola la PRIMERA vez que arranca el contenedor de MySQL.
--  Para volver a cargarla desde cero:  docker compose down -v
-- =====================================================================
SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS fogon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fogon;

-- ---------- El menú del restaurante FORJA ----------
CREATE TABLE menu (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  plato     VARCHAR(80)  NOT NULL,
  categoria ENUM('Entrada','Fondo','Postre','Bebida') NOT NULL,
  precio    DECIMAL(6,2) NOT NULL,
  picante   TINYINT(1)   NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO menu (plato, categoria, precio, picante) VALUES
  ('Salteña de carne',        'Entrada', 10.00, 1),
  ('Empanada blanqueada',     'Entrada',  8.00, 0),
  ('Queso humacha',           'Entrada', 22.00, 1),
  ('Sopa de maní',            'Entrada', 18.00, 0),
  ('Saice tarijeño',          'Fondo',   38.00, 1),
  ('Ranga ranga',             'Fondo',   32.00, 1),
  ('Chancho a la cruz',       'Fondo',   55.00, 0),
  ('Pique macho',             'Fondo',   60.00, 1),
  ('Tojorí',                  'Postre',  12.00, 0),
  ('Helado de canela',        'Postre',  10.00, 0),
  ('Mocochinchi',             'Bebida',   7.00, 0),
  ('Somó',                    'Bebida',   8.00, 0),
  ('Api morado',              'Bebida',   9.00, 0);

-- ---------- El libro de visitas ----------
CREATE TABLE visitas (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  nombre  VARCHAR(60)  NOT NULL,
  mensaje VARCHAR(280) NOT NULL,
  creado  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO visitas (nombre, mensaje) VALUES
  ('Profe', '¡Bienvenidos a la cocina! Dejen su mensaje.'),
  ('FORJA WEB', 'Este mensaje está guardado en MySQL, no en el HTML.');

-- ---------- Para el reto final: formulario de contacto ----------
CREATE TABLE contactos (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  nombre  VARCHAR(80)  NOT NULL,
  correo  VARCHAR(120) NOT NULL,
  mensaje TEXT         NOT NULL,
  creado  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
