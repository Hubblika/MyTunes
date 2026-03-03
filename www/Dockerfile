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
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    # Install Node.js 20.x and npm
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

# Expose port 9000 for PHP-FPM and 5173 for Vite
EXPOSE 9000 5173

# Start PHP-FPM and Vite in development mode
CMD bash -c "php-fpm & npm install && npm run dev"