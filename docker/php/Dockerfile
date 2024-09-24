FROM php:8.3-apache

RUN a2enmod ssl

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libedit-dev \
    libxml2-dev \
    libzip-dev \
    imagemagick \
    librabbitmq-dev

RUN docker-php-ext-install intl gd curl opcache zip bcmath pdo_mysql soap sockets pcntl

RUN pecl channel-update pecl.php.net \
    && pecl install apcu-5.1.22 amqp redis-5.3.7 xdebug \
    && docker-php-ext-enable apcu amqp redis xdebug pcntl

RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.enable_cli=1'; \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.interned_strings_buffer=16'; \
        echo 'opcache.max_accelerated_files=32531'; \
        echo 'opcache.max_wasted_percentage=10'; \
        echo 'opcache.validate_timestamps=1'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.enable_cli=1'; \
        echo 'realpath_cache_size=4096K'; \
        echo 'realpath_cache_ttl=600'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

RUN { \
        echo 'memory_limit=128M'; \
        echo 'upload_max_filesize=20M'; \
        echo 'post_max_size=20M'; \
    } > /usr/local/etc/php/conf.d/zz-overrides.ini

RUN sed -i 's/SECLEVEL=2/SECLEVEL=1/g' /etc/ssl/openssl.cnf

COPY --from=composer:2.4 /usr/bin/composer /usr/bin/composer
