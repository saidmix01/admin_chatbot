FROM php:8.1-apache

# Enable Apache modules
RUN a2enmod rewrite headers

# Install PostgreSQL driver and dependencies
RUN apt-get update && apt-get install -y \
        libpq-dev \
        libzip-dev \
        zip \
        unzip \
    && docker-php-ext-install pdo_pgsql pgsql zip \
    && apt-get clean

# Enable PostgreSQL driver for CodeIgniter
RUN docker-php-ext-enable pdo_pgsql

# Set document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html
