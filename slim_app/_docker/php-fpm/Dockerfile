# syntax=docker/dockerfile:1.4

FROM php:8.1-fpm

ARG DB_HOST
ARG DB_PORT
ARG DB_DATABASE
ARG DB_USERNAME
ARG DB_PASSWORD

RUN apt-get update && apt-get install -y \
    build-essential \
    curl git cron tar less zip unzip \
    jpegoptim optipng pngquant gifsicle \
    locales \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libmcrypt-dev \
    vim bash mc

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install bcmath exif mbstring mysqli pdo pdo_mysql

RUN pecl install apcu-5.1.21 mcrypt

RUN docker-php-ext-enable apcu opcache mcrypt

RUN rm -rf /tmp/pear

COPY --from=composer:2.1 /usr/bin/composer /usr/local/bin/composer

#COPY ./_docker/php-fpm/crontab /etc/cron.d/crontab
RUN touch /var/log/cron.log
#RUN chmod 0644 /etc/cron.d/crontab && crontab /etc/cron.d/crontab

COPY <<EOF /etc/php-app.env
APP_URL=http://localhost:8000
PUBLIC_DIR=/app/www
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
EOF

WORKDIR /app

#COPY . .

#RUN composer install --ignore-platform-reqs --no-interaction

CMD bash -c "/app/boot-app.sh && cron && php-fpm"
