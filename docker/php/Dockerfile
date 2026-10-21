FROM php:8.3-fpm-alpine

ARG UID=1000
ARG GID=1000

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql intl zip opcache gd

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache git unzip bash icu-data-full

RUN (getent group ${GID} || addgroup -g ${GID} app) \
 && adduser -D -u ${UID} -G "$(getent group ${GID} | cut -d: -f1)" -s /bin/bash app

COPY php.ini /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www/html
USER app