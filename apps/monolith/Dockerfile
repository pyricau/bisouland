# syntax=docker/dockerfile:1
# Dockerfile for SkySwoon (2005 LAMP project)
# Uses PHP 5.6 with PDO MySQL driver

FROM php:5.6-apache

# Update sources.list to use archive repositories for Debian Stretch
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i 's/security.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i '/stretch-updates/d' /etc/apt/sources.list

# Install system dependencies and PHP extensions in single layer
RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files with proper ownership
COPY --chown=www-data:www-data web/ /var/www/html/

# Configure application
RUN touch /var/www/html/news/.htpasswd \
    && chmod 644 /var/www/html/news/.htpasswd


