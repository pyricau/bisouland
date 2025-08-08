# syntax=docker/dockerfile:1
# Dockerfile for SkySwoon (2004 LAMP project)
# Uses PHP 5.6 to support legacy mysql_* functions

FROM php:5.6-apache

# Update sources.list to use archive repositories for Debian Stretch
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i 's/security.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i '/stretch-updates/d' /etc/apt/sources.list

# Install system dependencies and PHP extensions in single layer
RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    apt-get update && apt-get install -y --no-install-recommends --allow-unauthenticated \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install mysql gd \
    && a2enmod rewrite

# Create non-root user for better security
RUN groupadd -r appuser && useradd -r -g appuser appuser

# Set working directory
WORKDIR /var/www/html

# Copy application files with proper ownership
COPY --chown=www-data:www-data web/ /var/www/html/

# Configure application
RUN touch /var/www/html/news/.htpasswd \
    && chmod -R 755 /var/www/html \
    && chmod 644 /var/www/html/news/.htpasswd


