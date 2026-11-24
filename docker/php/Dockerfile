# La cocina: Apache (el mozo) + PHP 8.3 (el cocinero) en un solo contenedor
FROM php:8.3-apache

# Le enseñamos al cocinero a hablar con la despensa (MySQL) mediante PDO
RUN docker-php-ext-install pdo_mysql

# Configuración de CLASE: mostrar todos los errores en pantalla
# (en un sitio real, en producción, esto se apaga)
COPY php-dev.ini /usr/local/etc/php/conf.d/php-dev.ini
