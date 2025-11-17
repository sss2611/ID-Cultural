FROM php:8.2-apache

# Copiar public y también config.php
COPY public/ /var/www/html/
COPY config.php /var/www/html/config.php

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN a2enmod rewrite

EXPOSE 80
