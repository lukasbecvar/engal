FROM php:8.3-apache

# set default apache working directory
WORKDIR /var/www

# update package index and install necessary dependencies
RUN apt-get update && apt-get install -y \
    rpm \
    libzip-dev \
    unzip \
    libicu-dev \ 
    && docker-php-ext-install zip pdo_mysql mysqli pdo intl

# enable apache rewrite module
RUN a2enmod rewrite
