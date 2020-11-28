FROM php:7.4-fpm-alpine

RUN apk --update add --virtual build-dependencies build-base openssl-dev autoconf openssh sqlite sqlite-dev \
    jpeg-dev libpng-dev freetype-dev libxslt-dev icu-dev libzip-dev bash supervisor bzip2-dev icu-libs git \
  && docker-php-ext-configure gd \
  && docker-php-ext-configure intl --enable-intl

RUN docker-php-ext-install gd bcmath intl xsl pdo pdo_mysql soap zip bz2 calendar exif sockets

# COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    mkdir /usr/local/.composer && chmod 777 -R /usr/local/.composer

# Set default locale for the environment
ENV LC_ALL C.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US.UTF-8

RUN docker-php-ext-install opcache

#INSTALL APCU
RUN pecl install apcu && docker-php-ext-enable apcu
#APCU

# Uncomment this line and run `bin/dev build` to enable xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN composer self-update
