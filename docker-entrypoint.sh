#!/bin/bash
set -e

echo "🚀 Starting Monypo2 application..."

# Install Composer dependencies if vendor directory doesn't exist
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-interaction
fi

# Install NPM dependencies if node_modules directory doesn't exist
if [ ! -d "node_modules" ]; then
    echo "📦 Installing NPM dependencies..."
    npm install
fi

# Wait for database to be ready
echo "⏳ Waiting for database..."
max_retries=30
counter=0
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
    counter=$((counter + 1))
    if [ $counter -gt $max_retries ]; then
        echo "❌ Database connection timeout!"
        exit 1
    fi
    echo "Database is unavailable - attempt $counter/$max_retries"
    sleep 2
done

echo "✅ Database is ready!"

# Copy .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    # Update database settings for Docker
    sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
    sed -i 's/^# *DB_HOST=.*/DB_HOST=db/' .env
    sed -i 's/^# *DB_PORT=.*/DB_PORT=3306/' .env
    sed -i 's/^# *DB_DATABASE=.*/DB_DATABASE=monypo2/' .env
    sed -i 's/^# *DB_USERNAME=.*/DB_USERNAME=monypo2_user/' .env
    sed -i 's/^# *DB_PASSWORD=.*/DB_PASSWORD=secret/' .env
fi

# Generate application key if not set
if ! grep -q '^APP_KEY=.\+' .env 2>/dev/null; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
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
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

echo "✨ Application is ready!"
echo "📱 Laravel: http://localhost:8000"
echo "🎨 Vite: http://localhost:5173"

# Start Laravel development server and Vite in parallel
php artisan serve --host=0.0.0.0 --port=8000 &
npm run dev -- --host 0.0.0.0 &

# Wait for any process to exit
wait -n

# Exit with status of process that exited first
exit $?
