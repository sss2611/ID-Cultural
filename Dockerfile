FROM php:8.2-apache

# Copiar public y también config.php
COPY public/ /var/www/html/
COPY config.php /var/www/config.php
COPY backend/ /var/www/backend/
COPY components/ /var/www/components/

RUN chown -R www-data:www-data /var/www/html /var/www/config.php /var/www/backend /var/www/components \
    && chmod -R 755 /var/www/html /var/www/config.php /var/www/backend /var/www/components

RUN a2enmod rewrite

EXPOSE 80
