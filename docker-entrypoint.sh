#!/bin/bash
set -e

echo "🚀 Starting Monypo2 application..."

# Wait for database to be ready
echo "⏳ Waiting for database..."
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
    echo "Database is unavailable - sleeping"
    sleep 2
done

echo "✅ Database is ready!"

# Copy .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
fi

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Seed database if needed
if [ "$DB_SEED" = "true" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
fi

# Clear and cache config
echo "⚙️  Optimizing configuration..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "✨ Application is ready!"
echo "📱 Laravel: http://localhost:8000"
echo "🎨 Vite: http://localhost:5173"

# Start Laravel development server and Vite in parallel
php artisan serve --host=0.0.0.0 --port=8000 &
npm run dev -- --host &

# Wait for any process to exit
wait -n

# Exit with status of process that exited first
exit $?
