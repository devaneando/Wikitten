FROM php:5.6-cli
RUN mkdir -p /var/www
WORKDIR /var/www
EXPOSE 9000
CMD [ "php", "-S", "0.0.0.0:9000", "routing.php" ]
