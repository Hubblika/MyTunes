# Use the official PHP image with FPM
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies, Node.js, and npm
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nano \
    supervisor \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/www

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Copy the entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

COPY php/local.ini /usr/local/etc/php/conf.d/local.ini

# Set the entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]

# Expose ports
EXPOSE 9000 5173

# We leave CMD empty because the entrypoint handles the startup