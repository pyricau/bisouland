# syntax=docker/dockerfile:1

###
# PHP Dev Container
# Utility Tools: PHP, bash, Composer
###
FROM php:8.4-cli-alpine AS php_dev_container

# Composer environment variables:
# * default user is superuser (root), so allow them
# * put cache directory in a readable/writable location
# _Note_: When running `composer` in container, use `--no-cache` option
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_CACHE_DIR=/tmp/.composer/cache

# Install dependencies:
# * bash for shell access and scripting
# * zip for composer packages that use ZIP archives
# _Note (Alpine)_: `--no-cache` includes `--update` and keeps image size minimal
#
# Then install PHP extensions
#
# _Note (Hadolint)_: No version locking, since Alpine only ever provides one version
# hadolint ignore=DL3018
RUN apk add --update --no-cache \
        bash \
        libzip-dev \
        zip \
    && docker-php-ext-install \
        bcmath \
        zip

# Copy Composer binary from composer image
# _Note (Hadolint)_: False positive as `COPY` works with images too
# See: https://github.com/hadolint/hadolint/issues/197#issuecomment-1016595425
# hadolint ignore=DL3022
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /apps/qa

# Caching `composer install`, as long as composer.{json,lock} don't change.
COPY composer.json composer.lock ./
RUN composer install \
    --no-cache \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --optimize-autoloader

# Copy the remaining application files (excluding those listed in .dockerignore)
COPY . .
