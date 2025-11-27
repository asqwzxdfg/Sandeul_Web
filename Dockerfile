FROM php:8.2-apache

RUN docker-php-ext-install mysqli

WORKDIR /var/www/html

COPY ./src/ /var/www/html/

RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80