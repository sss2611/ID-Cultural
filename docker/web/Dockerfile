# ID-CULTURAL/docker/web/Dockerfile

# Usamos la imagen base de PHP con Apache que ya estabas usando
FROM php:8.1-apache

# Instalar las extensiones PHP necesarias
# 'pdo' es el módulo principal de PDO.
# 'pdo_mysql' es el controlador específico para MySQL/MariaDB.
# 'mysqli' es otra extensión para MySQL, a menudo útil.
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar el módulo rewrite de Apache (comúnmente necesario para URLs amigables)
RUN a2enmod rewrite

# Copiar la configuración de Apache personalizada
# Asumiendo que 000-default.conf está en ID-CULTURAL/docker/apache/
COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copiar todo el código de tu aplicación al DocumentRoot dentro del contenedor
# El '.' se refiere a la raíz de tu proyecto ID-CULTURAL/
COPY . /var/www/app/

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www/app/

# Opcional: Si tu 000-default.conf no establece el DocumentRoot a /var/www/app/public,
# puedes añadir una línea aquí para asegurarte de que Apache lo sepa.
# Sin embargo, si tu 000-default.conf ya lo hace, esta línea no es necesaria.
# RUN sed -i 's!/var/www/html!/var/www/app/public!g' /etc/apache2/sites-available/000-default.conf
