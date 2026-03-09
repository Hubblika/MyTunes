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

# We ask PHP to check if the 'migrations' table exists and has at least 1 row.
# '1' means it's already set up. '0' means it's empty.
HAS_TABLES=$(php -r "include 'vendor/autoload.php'; \$app = include 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo Schema::hasTable('migrations') && DB::table('migrations')->count() > 0 ? '1' : '0';")
echo "$HAS_TABLES"

if [ "$HAS_TABLES" = "0" ]; then
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