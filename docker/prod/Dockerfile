FROM php:8.1-fpm

LABEL maintainer="Diego Carlos"

RUN apt-get update
RUN apt-get install -y libpng-dev
RUN apt-get install -y libjpeg62-turbo-dev
RUN apt-get install -y libfreetype6-dev
RUN apt-get install -y zip
RUN apt-get install -y jpegoptim
RUN apt-get install -y optipng
RUN apt-get install -y pngquant
RUN apt-get install -y gifsicle
RUN apt-get install -y vim
RUN apt-get install -y curl
RUN apt-get install -y unzip
RUN apt-get install -y nano
RUN apt-get install -y libmagickwand-dev
RUN apt-get install -y libzip-dev
RUN apt-get install -y libpq-dev
RUN apt-get install -y nodejs
RUN apt-get install -y npm

RUN npm install -g pnpm

RUN docker-php-ext-install pdo_mysql exif gd opcache
RUN docker-php-ext-install zip

COPY --from=composer:2.6.6 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /usr/share/nginx/html

RUN chown -R www-data:www-data /usr/share/nginx/html \
    && chmod -R 755 /usr/share/nginx/html \
    && chmod -R 755 /usr/share/nginx/html/storage \
    && chmod -R 755 /usr/share/nginx/html/bootstrap/cache

COPY package.json .
COPY pnpm-lock.yaml .

RUN pnpm install

RUN pnpm run build

EXPOSE 9000
