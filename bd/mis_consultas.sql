-- mis_consultas.sql · DescubreTarija (MySQL)
USE descubretarija;

-- 1) WHERE: destinos destacados
SELECT nombre, ubicacion FROM destinos WHERE destacado = TRUE;

-- 2) WHERE con AND: destinos de Naturaleza todavía sin verificar
SELECT nombre FROM destinos WHERE categoria_id = 1 AND verificado = FALSE;

-- 3) GROUP BY: cuántos destinos tiene cada categoría (por id)
SELECT categoria_id, COUNT(*) AS total
FROM destinos
GROUP BY categoria_id
ORDER BY total DESC;

-- 4) GROUP BY + HAVING: destinos guardados por 2 o más usuarios
SELECT destino_id, COUNT(*) AS veces
FROM favoritos
GROUP BY destino_id
HAVING COUNT(*) >= 2
ORDER BY veces DESC;

-- 5) JOIN de 3 tablas: qué usuario guardó qué destino
SELECT u.nombre AS usuario, d.nombre AS destino
FROM favoritos f
JOIN usuarios u ON u.id = f.usuario_id
JOIN destinos d ON d.id = f.destino_id
ORDER BY u.nombre, d.nombre;

-- 6) JOIN de 3 tablas: recorridos con sus destinos en orden
SELECT r.nombre AS recorrido, rd.orden, d.nombre AS destino
FROM recorridos r
JOIN recorrido_destinos rd ON rd.recorrido_id = r.id
JOIN destinos d ON d.id = rd.destino_id
ORDER BY r.id, rd.orden;

-- 7) LEFT JOIN: todas las categorías, aunque no tengan destinos (sale 0)
SELECT c.nombre AS categoria, COUNT(d.id) AS destinos
FROM categorias c
LEFT JOIN destinos d ON d.categoria_id = c.id
GROUP BY c.id, c.nombre
ORDER BY destinos DESC, c.nombre;

-- 8) UPDATE con WHERE: destacar un destino
UPDATE destinos SET destacado = TRUE WHERE nombre = 'Coimata';
-- (para deshacerlo después) UPDATE destinos SET destacado = FALSE WHERE nombre = 'Coimata';