#!/bin/bash

# MikiPark Deployment Script
# This script sets up the complete parking management system

echo "🚀 Starting MikiPark Deployment..."
echo "=================================="

# Step 1: Install Dependencies
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

echo "📦 Installing Node.js dependencies..."
npm install

# Step 2: Environment Setup
echo "⚙️ Setting up environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Environment file created"
fi

# Step 3: Generate Keys
echo "🔑 Generating application keys..."
php artisan key:generate --force
php artisan jwt:secret --force

# Step 4: Database Setup
echo "🗄️ Setting up database..."
php artisan migrate --force
php artisan db:seed --force

# Step 5: Build Frontend
echo "🎨 Building frontend..."
npm run build

# Step 6: Optimize Laravel
echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 7: Set Permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage bootstrap/cache

# Step 8: Create Admin User
echo "👤 Creating admin user..."
php artisan tinker --execute="
try {
    \$admin = User::where('email', 'admin@admin.com')->first();
    if (!\$admin) {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'role' => 3,
            'balance' => 1000
        ]);
        echo 'Admin user created successfully!' . PHP_EOL;
    } else {
        echo 'Admin user already exists!' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Error creating admin user: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "🎉 Deployment Complete!"
echo "======================"
echo ""
echo "📋 System Information:"
echo "- Frontend URL: http://127.0.0.1:8000"
echo "- API Base URL: http://127.0.0.1:8000/api"
echo "- Admin Email: admin@admin.com"
echo "- Admin Password: admin123"
echo ""
echo "🚀 To start the server:"
echo "php artisan serve"
echo ""
echo "📚 Available Features:"
echo "- User Registration & Authentication"
echo "- Parking Spot Management"
echo "- Reservation System"
echo "- Balance Management"
echo "- Admin Dashboard"
echo "- Real-time Updates"
echo ""
echo "✅ MikiPark is ready for use!"