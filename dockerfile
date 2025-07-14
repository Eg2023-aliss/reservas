FROM php:8.2-apache

# Copiar los archivos al servidor
COPY . /var/www/html/

# Exponer el puerto 80
EXPOSE 80

