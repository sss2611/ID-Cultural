FROM php:8.2-apache

# Copiar solo la carpeta public al DocumentRoot
COPY public/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN a2enmod rewrite

EXPOSE 80
