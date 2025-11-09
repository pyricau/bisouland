# syntax=docker/dockerfile:1
# Dockerfile for BisouLand (2005 LAMP project)
# Uses PHP 8.4 with PDO MySQL driver

FROM php:8.4-apache

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


