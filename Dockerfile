FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql mysqli

WORKDIR /var/www/html

COPY . /var/www/html

EXPOSE 80
