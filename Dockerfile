FROM php:5.4-apache

RUN apt-get update &&\
    apt-get install -y \
    mariadb-client \
    git \
    zip \
    unzip \
    iputils-ping &&\
    apt-get clean &&\
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install mysqli

ENV CONFIG docker/config

RUN a2enmod rewrite
COPY ${CONFIG}/sites-enabled/default.conf /etc/apache2/sites-enabled/000-default.conf

RUN echo "assert.exception=1" >> /usr/local/etc/php/php.ini

RUN mkdir -p /var/www/project
WORKDIR /var/www/project
