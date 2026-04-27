# Usamos la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalamos extensiones necesarias para Laravel (PDO, MySQL, etc.)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Habilitamos el módulo de reescritura de Apache (importante para Laravel)
RUN a2enmod rewrite

# Instalamos las extensiones de PHP para MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copiamos el código de nuestro Sistema TAC al contenedor
COPY . /var/www/html

# Ajustamos permisos para que Laravel pueda escribir en storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponemos el puerto 80
EXPOSE 80