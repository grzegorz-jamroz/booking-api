FROM php:8.1-fpm

RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

VOLUME /var/www/html/booking
WORKDIR /var/www/html/booking
COPY . /var/www/html/booking

RUN chmod 777 -R /var/www/html/booking
