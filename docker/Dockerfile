FROM php:7.3-apache
COPY ./php.ini "${PHP_INI_DIR}/php.ini"
RUN mkdir -p /var/log/php \
    && touch /var/log/php/php_errors.log \
    && chown -R www-data:www-data /var/log/php
RUN a2enmod rewrite
