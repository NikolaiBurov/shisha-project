FROM php:8.1-apache

USER root

WORKDIR /var/www/html

RUN apt update && docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

RUN apt-get install -y default-mysql-client

RUN apt-get clean
RUN apt-get update && apt-get install -y \
        zlib1g-dev libicu-dev g++ \
        libjpeg62-turbo-dev \
        libzip-dev \
        libpng-dev \
        libwebp-dev \
        libfreetype6-dev \
    	libxml2-dev \
    	git \
    	zip \
    	unzip \
        nano \
        nodejs \
        npm \
        curl \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure gd --with-webp=/usr/include/webp --with-jpeg=/usr/include --with-freetype=/usr/include/freetype2/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \ 
    && docker-php-ext-install exif \
    && docker-php-ext-enable exif \
    && docker-php-source delete \
    && docker-php-ext-install mysqli 


RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install  pcntl bcmath gd

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

COPY .env.example  /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www-data:www-data /var/www/html && a2enmod rewrite