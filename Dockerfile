FROM php:8.3-apache

RUN apt-get update &&\
    apt-get install -y \
    mariadb-client \
    libicu-dev \
    git \
    zip \
    unzip \
    iputils-ping &&\
    apt-get clean &&\
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-install\
    pdo_mysql\
    intl

ENV CONFIG docker/config

RUN a2enmod rewrite
COPY ${CONFIG}/sites-enabled/default.conf /etc/apache2/sites-enabled/000-default.conf

RUN echo "assert.exception=1" >> /usr/local/etc/php/php.ini

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename composer \
    && php -r "unlink('composer-setup.php');"

RUN mkdir -p /var/www/project
WORKDIR /var/www/project
