-- MySQL dump 10.13  Distrib 8.4.13, for Linux (x86_64)
--
-- Host: localhost    Database: descubretarija
-- ------------------------------------------------------
-- Server version	8.4.13

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
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Naturaleza','Paisajes y áreas naturales'),(2,'Cultura e historia','Museos y sitios históricos'),(3,'Gastronomía','Comida y bebidas típicas'),(4,'Aventura','Actividades al aire libre'),(5,'Turismo rural','Experiencias en el campo'),(6,'Festividades y tradiciones','Fiestas y costumbres');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `destinos`
--

DROP TABLE IF EXISTS `destinos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `destinos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `ubicacion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitud` decimal(9,6) DEFAULT NULL,
  `longitud` decimal(9,6) DEFAULT NULL,
  `categoria_id` int DEFAULT NULL,
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `verificado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `fk_destino_categoria` (`categoria_id`),
  CONSTRAINT `fk_destino_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `destinos`
--

LOCK TABLES `destinos` WRITE;
/*!40000 ALTER TABLE `destinos` DISABLE KEYS */;
INSERT INTO `destinos` VALUES (1,'Casa Dorada','POR VERIFICAR','POR VERIFICAR',-21.532572,-64.734182,2,1,1),(2,'Coimata','POR VERIFICAR','POR VERIFICAR',NULL,NULL,1,0,0),(3,'San Lorenzo','POR VERIFICAR','POR VERIFICAR',-21.418303,-64.749536,5,1,1),(4,'Valle de la Concepción','POR VERIFICAR','POR VERIFICAR',NULL,NULL,3,1,0),(5,'Represa de San Jacinto','POR VERIFICAR','POR VERIFICAR',NULL,NULL,1,0,0),(6,'Rincón de la Victoria','POR VERIFICAR','POR VERIFICAR',NULL,NULL,5,0,0),(7,'Reserva Biológica Cordillera de Sama','POR VERIFICAR','POR VERIFICAR',NULL,NULL,1,0,0),(8,'Cañón de la Angostura','POR VERIFICAR','POR VERIFICAR',NULL,NULL,4,0,0),(9,'Museo Paleontológico de Tarija','POR VERIFICAR','POR VERIFICAR',NULL,NULL,2,0,0);
/*!40000 ALTER TABLE `destinos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favoritos`
--

DROP TABLE IF EXISTS `favoritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favoritos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `destino_id` int NOT NULL,
  `guardado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_favorito` (`usuario_id`,`destino_id`),
  KEY `fk_favorito_destino` (`destino_id`),
  CONSTRAINT `fk_favorito_destino` FOREIGN KEY (`destino_id`) REFERENCES `destinos` (`id`),
  CONSTRAINT `fk_favorito_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favoritos`
--

LOCK TABLES `favoritos` WRITE;
/*!40000 ALTER TABLE `favoritos` DISABLE KEYS */;
INSERT INTO `favoritos` VALUES (1,2,1,'2026-11-24 08:04:53'),(2,2,4,'2026-11-24 08:04:53'),(3,2,8,'2026-11-24 08:04:53'),(4,3,1,'2026-11-24 08:04:53'),(5,3,3,'2026-11-24 08:04:53'),(6,4,4,'2026-11-24 08:04:53'),(7,4,9,'2026-11-24 08:04:53'),(8,5,2,'2026-11-24 08:04:53');
/*!40000 ALTER TABLE `favoritos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagenes`
--

DROP TABLE IF EXISTS `imagenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imagenes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `destino_id` int NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_imagen_destino` (`destino_id`),
  CONSTRAINT `fk_imagen_destino` FOREIGN KEY (`destino_id`) REFERENCES `destinos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagenes`
--

LOCK TABLES `imagenes` WRITE;
/*!40000 ALTER TABLE `imagenes` DISABLE KEYS */;
INSERT INTO `imagenes` VALUES (1,1,'assets/images/casa-dorada-1.jpg','Fachada principal'),(2,1,'assets/images/casa-dorada-2.jpg','Vista interior'),(3,2,'assets/images/coimata-1.jpg','Vista general'),(4,3,'assets/images/san-lorenzo-1.jpg','Plaza principal'),(5,4,'assets/images/valle-concepcion-1.jpg','Viñedos'),(6,5,'assets/images/san-jacinto-1.jpg','Vista de la represa'),(7,9,'assets/images/museo-paleo-1.jpg','Entrada del museo');
/*!40000 ALTER TABLE `imagenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recorrido_destinos`
--

DROP TABLE IF EXISTS `recorrido_destinos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recorrido_destinos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `recorrido_id` int NOT NULL,
  `destino_id` int NOT NULL,
  `orden` smallint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rd` (`recorrido_id`,`destino_id`),
  KEY `fk_rd_destino` (`destino_id`),
  CONSTRAINT `fk_rd_destino` FOREIGN KEY (`destino_id`) REFERENCES `destinos` (`id`),
  CONSTRAINT `fk_rd_recorrido` FOREIGN KEY (`recorrido_id`) REFERENCES `recorridos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recorrido_destinos`
--

LOCK TABLES `recorrido_destinos` WRITE;
/*!40000 ALTER TABLE `recorrido_destinos` DISABLE KEYS */;
INSERT INTO `recorrido_destinos` VALUES (1,1,1,1),(2,1,9,2),(3,1,3,3),(4,2,4,1),(5,2,3,2),(6,3,5,1),(7,3,2,2),(8,4,9,1),(9,4,1,2),(10,5,8,1),(11,5,7,2);
/*!40000 ALTER TABLE `recorrido_destinos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recorridos`
--

DROP TABLE IF EXISTS `recorridos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recorridos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_visita` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_recorrido_usuario` (`usuario_id`),
  CONSTRAINT `fk_recorrido_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recorridos`
--

LOCK TABLES `recorridos` WRITE;
/*!40000 ALTER TABLE `recorridos` DISABLE KEYS */;
INSERT INTO `recorridos` VALUES (1,2,'Fin de semana en Tarija','2026-12-05'),(2,2,'Ruta del vino',NULL),(3,3,'Paseo familiar','2026-12-12'),(4,4,'Cultura e historia',NULL),(5,5,'Aventura y naturaleza','2027-01-10');
/*!40000 ALTER TABLE `recorridos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('usuario','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'usuario',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador DescubreTarija','admin@descubretarija.bo','$2y$10$44NHzl6vKhaa4JdDoGDV6e8aOOBQ7Qp7WhWsNRYv7luNcJa./5OBK','admin','2026-11-24 08:04:53'),(2,'María Peñaranda','maria@correo.com','$2y$10$lL7MnEAxa.Bjbm14PYXhguI1ohoV/G34rivdlFGSxdJq/ns2IAEtC','usuario','2026-11-24 08:04:53'),(3,'José Ñanco','jose@correo.com','$2y$10$ollqEAFB19ZDSP/N8hbHVOxsK7B9p4BDDdRdNk0TOOYfYiTJwMsJW','usuario','2026-11-24 08:04:53'),(4,'Lucía Vargas','lucia@correo.com','$2y$10$X0X7UNam4sD/CF7MIhFmkOBLAdN8gzrkWAIth6T3arf1E4jJGtmGu','usuario','2026-11-24 08:04:53'),(5,'Andrés Condori','andres@correo.com','$2y$10$O.2lAET./FvGD1JSaj1SPOUhEshriGRgPvnK5N3i9.eFg95zC0TGS','usuario','2026-11-24 08:04:53'),(6,'AlexGT','rodrigozenteno7295@gmail.com','$2y$10$6sUGNTktUxaMmsO8KHpFUuDWxqv6xUJXfRmdBPq9e9svU7kAA7HBW','usuario','2026-11-24 09:24:33');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-24 22:36:45
