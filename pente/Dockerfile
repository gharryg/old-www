FROM php:5.4

RUN mv /usr/src/php/php.ini-production "$PHP_INI_DIR/php.ini"
RUN echo "date.timezone = America/Chicago" >> "$PHP_INI_DIR/php.ini"
RUN docker-php-ext-install sockets
RUN docker-php-ext-install pdo_mysql mysql

WORKDIR /code
COPY *.php ./
RUN mkdir logs

CMD ["php", "PenteServer.php"]
