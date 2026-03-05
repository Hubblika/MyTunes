#!/bin/bash
set -e

if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

if [ ! -d "vendor" ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --optimize-autoloader
    php artisan key:generate
fi

if [ ! -d "node_modules" ]; then
    echo "Installing npm dependencies..."
    npm install
fi

# OPTIONAL: Wait for MySQL to be ready before moving on
 echo "Waiting for database..."
until php -r "try { new PDO('mysql:host=db;port=3306', 'laravel', 'secret'); exit(0); } catch (Exception \$e) { exit(1); }"; do
  echo "Waiting for database connection..."
  sleep 5
done

# Run migrations and seeders
echo "Checking database state..."
# This checks if the 'users' table exists (common Laravel indicator)
# If it doesn't exist, we assume a fresh install
if ! php artisan tinker --execute="Schema::hasTable('users')" | grep -q "true"; then
    echo "Fresh database detected. Running migrations and seeders..."
    php artisan migrate:fresh --seed --force
else
    echo "Database already exists. Running any pending migrations..."
    php artisan migrate --force
fi

echo "Creating storage link..."
php artisan storage:link --force

echo "Fixing permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Start Vite in background
npm run dev -- --host &

# Start PHP
exec php-fpm