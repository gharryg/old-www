FROM php:5.4-apache

RUN mv /usr/src/php/php.ini-production "$PHP_INI_DIR/php.ini"
RUN echo "date.timezone = America/Chicago" >> "$PHP_INI_DIR/php.ini"
RUN docker-php-ext-install pdo_mysql mysql
RUN a2enmod rewrite

COPY ./ /var/www/html/
