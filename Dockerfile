FROM node:22-bookworm-slim AS node
FROM composer:2 AS composer
FROM php:8.3-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libonig-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install mbstring pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=node /usr/local/ /usr/local/

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /workspace
