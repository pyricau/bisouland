# syntax=docker/dockerfile:1

###
# PHP Dev Container
# Utility Tools: Apache, PHP-FPM, bash, Composer
###
FROM php:8.5-fpm-alpine AS php_dev_container

# Composer environment variables:
# * default user is superuser (root), so allow them
# * put cache directory in a readable/writable location
# _Note_: When running `composer` in container, use `--no-cache` option
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_CACHE_DIR=/tmp/.composer/cache

# Install dependencies:
# * apache for the web server
# * bash for shell access and scripting
# * PDO (PostgreSQL) extension for database access
# * zip for composer packages that use ZIP archives
# _Note (Alpine)_: `--no-cache` includes `--update` and keeps image size minimal
#
# Then install PHP extensions
#
# _Note (Hadolint)_: No version locking, since Alpine only ever provides one version
# hadolint ignore=DL3018
RUN apk add --update --no-cache \
        apache2 \
        apache2-proxy \
        apache2-ssl \
        bash \
        libzip-dev \
        postgresql-dev \
        zip \
    && sed -i 's/^#LoadModule rewrite_module/LoadModule rewrite_module/' /etc/apache2/httpd.conf \
    && docker-php-ext-install \
        bcmath \
        pdo \
        pdo_pgsql \
        zip

# Copy Composer binary from composer image
# _Note (Hadolint)_: False positive as `COPY` works with images too
# See: https://github.com/hadolint/hadolint/issues/197#issuecomment-1016595425
# hadolint ignore=DL3022
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /apps/monolith

# Caching `composer install`, as long as composer.{json,lock} don't change.
COPY composer.json composer.lock ./
RUN composer install \
    --no-cache \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --optimize-autoloader \
    && chmod -R o+rX vendor/

# Copy the remaining application files (excluding those listed in .dockerignore)
COPY . .

# Configure Apache
RUN sed -i 's|^DocumentRoot.*|DocumentRoot "/apps/monolith/web"|' /etc/apache2/httpd.conf \
    && sed -i 's|<Directory "/var/www/localhost/htdocs">|<Directory "/apps/monolith/web">|' /etc/apache2/httpd.conf \
    && sed -i 's|AllowOverride None|AllowOverride All|' /etc/apache2/httpd.conf \
    && sed -i 's|^#LoadModule proxy_module|LoadModule proxy_module|' /etc/apache2/httpd.conf \
    && sed -i 's|^#LoadModule proxy_fcgi_module|LoadModule proxy_fcgi_module|' /etc/apache2/httpd.conf \
    && echo "" >> /etc/apache2/httpd.conf \
    && echo "# PHP-FPM Configuration" >> /etc/apache2/httpd.conf \
    && echo "DirectoryIndex index.php index.html" >> /etc/apache2/httpd.conf \
    && echo '<FilesMatch "\.php$">' >> /etc/apache2/httpd.conf \
    && echo '    SetHandler "proxy:fcgi://127.0.0.1:9000"' >> /etc/apache2/httpd.conf \
    && echo '</FilesMatch>' >> /etc/apache2/httpd.conf

# Create startup script to run both PHP-FPM and Apache
RUN echo '#!/bin/sh' > /start.sh \
    && echo 'php-fpm -D' >> /start.sh \
    && echo 'exec httpd -D FOREGROUND' >> /start.sh \
    && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
