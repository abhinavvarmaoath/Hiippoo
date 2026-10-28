FROM php:8.3-cli
RUN docker-php-ext-install mysqli
WORKDIR /var/www/html
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/html"]
