FROM php:7.4-fpm

ARG environment
ARG user
ARG uid

#ADD https://github.com/Yelp/dumb-init/releases/download/v1.2.0/dumb-init_1.2.0_amd64 /usr/local/bin/dumb-init
#RUN chmod +x /usr/local/bin/dumb-init

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    build-essential \
    apt-utils \
    libtool \
    autoconf \
    pkg-config \
    uuid-dev \
    libc-client-dev \
    libmcrypt-dev \
    libkrb5-dev \
    libbz2-dev \
    libzip-dev \
    libssl-dev \
    libsodium-dev \
    libicu-dev \
    libmemcached-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg-dev \
    libjpeg62-turbo-dev \
    libmagickwand-dev \
    libpng-dev \
    libonig-dev \
    libgmp-dev \
    libwebp-dev \
    mariadb-client \
    librabbitmq-dev \
    zlib1g-dev \
    ssh-client \
    msmtp \
    imagemagick \
    tar \
    git \
    curl \
    less \
    zip \
    unzip \
    vim \
    bash \
    mc

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql mbstring pcntl gmp bcmath zip mysqli gettext exif \
  && docker-php-ext-configure exif \
    --enable-exif
# && docker-php-ext-install opcache \
# && docker-php-ext-enable opcache \

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd

#RUN mkdir ~/.ssh
#RUN echo 'Host github.com\n\
#            StrictHostKeyChecking no\n\
#            IdentityFile ~/.ssh-local/id_rsa' > ~/.ssh/config
#RUN echo 'Host bitbucket.org\n\
#            StrictHostKeyChecking no\n\
#            IdentityFile ~/.ssh-local/id_rsa' >> ~/.ssh/config

RUN rm -f /usr/local/etc/www.conf.default && rm -f /usr/local/etc/docker.conf && rm -f /usr/local/etc/zz-docker.conf

RUN if [ $environment = "dev" ]; then pecl install xdebug-2.9.0 && docker-php-ext-enable xdebug; fi

RUN pecl install amqp && docker-php-ext-enable amqp

RUN pecl install mcrypt-1.0.3 \
  && docker-php-ext-enable mcrypt

RUN rm -rf /tmp/pear

COPY ./docker_files/php-fpm/php_dev.ini /usr/local/etc/php/php.ini
COPY ./docker_files/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY ./docker_files/php-fpm/www_dev.conf /usr/local/etc/php-fpm.d/www.conf

ENV COMPOSER_ALLOW_SUPERUSER 1

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

WORKDIR /app

#COPY . .

#RUN composer install --ignore-platform-reqs --no-interaction

#COPY .env.example .env

#RUN php artisan key:generate

USER $user

CMD ["php-fpm"]

EXPOSE 9000
