FROM php:8.3-apache

# set default apache working directory
WORKDIR /var/www

# update package index and install necessary dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \ 
    unzip \
    ffmpeg \
    rpm 

# install PHP main extensions
RUN docker-php-ext-install zip pdo_mysql mysqli pdo intl

# install phpGD
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd

# install OPCache
RUN docker-php-ext-install opcache

# install APCu
RUN pecl install apcu \
    && docker-php-ext-enable apcu

# enable apache rewrite module
RUN a2enmod rewrite
