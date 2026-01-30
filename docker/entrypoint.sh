#!/usr/bin/env sh
set -e

if [ ! -f /var/www/html/vendor/autoload.php ]; then
  echo "Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -f /var/www/html/.env ]; then
  echo "Creating .env from .env.example..."
  cp /var/www/html/.env.example /var/www/html/.env
fi

if ! grep -q "^APP_KEY=base64:" /var/www/html/.env; then
  echo "Generating APP_KEY..."
  php /var/www/html/artisan key:generate --force
fi

mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

exec "$@"
