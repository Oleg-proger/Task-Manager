FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ARG UID=1000
ARG GID=1000

RUN groupadd -g ${GID} app \
    && useradd -u ${UID} -g app -m app

USER app
