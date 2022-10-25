FROM php:8.1-apache
COPY ./php.ini "${PHP_INI_DIR}/php.ini"
RUN mkdir -p /var/log/php \
    && touch /var/log/php/php_errors.log \
    && chown -R www-data:www-data /var/log/php \
    && a2enmod rewrite
