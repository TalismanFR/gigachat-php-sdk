FROM php:8.3-cli

MAINTAINER  A. Egorov <anat-gor@ya.ru>

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions


RUN install-php-extensions xdebug intl pcntl @composer

WORKDIR /app

RUN rm  -rf /tmp/*
