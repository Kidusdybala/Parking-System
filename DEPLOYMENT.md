# MikiPark Deployment Guide

This guide provides step-by-step instructions for deploying the MikiPark Smart Parking Management System.

## 🎯 System Overview

**MikiPark** is a full-stack parking management system featuring:
- **Backend**: Laravel 11 with JWT Authentication
- **Frontend**: React 18 with modern UI
- **Database**: MySQL with comprehensive schema
- **Architecture**: RESTful API with SPA frontend

## 📋 Prerequisites

### Server Requirements
- **PHP**: 8.2 or higher
- **Node.js**: 18.0 or higher
- **MySQL**: 8.0 or higher
- **Composer**: Latest version
- **Web Server**: Apache/Nginx with SSL support

### PHP Extensions Required
```bash
php -m | grep -E "(openssl|pdo|mbstring|tokenizer|xml|ctype|json|bcmath|fileinfo|mysql)"
```

## 🚀 Production Deployment

### Step 1: Server Setup

```bash
# Clone the repository
git clone <repository-url> /var/www/mikipark
cd /var/www/mikipark

# Set proper ownership
sudo chown -R www-data:www-data /var/www/mikipark
sudo chmod -R 755 /var/www/mikipark
sudo chmod -R 775 /var/www/mikipark/storage
sudo chmod -R 775 /var/www/mikipark/bootstrap/cache
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install --production

# Build frontend assets
npm run build
```

### Step 3: Environment Configuration

```bash
# Copy production environment file
cp .env.production .env

# Edit environment variables
nano .env
```

**Required Environment Variables:**
```env
APP_NAME="MikiPark"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=parking_system
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

JWT_SECRET=your_jwt_secret_here
```

### Step 4: Generate Application Keys

```bash
# Generate Laravel application key
php artisan key:generate --force

# Generate JWT secret key
php artisan jwt:secret --force
```

### Step 5: Database Setup

```bash
# Run database migrations
php artisan migrate --force

# Seed initial data (optional)
php artisan db:seed --force
```

### Step 6: Create Admin User

```bash
php artisan tinker --execute="
User::create([
    'name' => 'System Administrator',
    'email' => 'admin@yourdomain.com',
    'password' => Hash::make('your_secure_admin_password'),
    'role' => 3,
    'balance' => 0
]);
echo 'Admin user created successfully!';
"
```

### Step 7: Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

## 🌐 Web Server Configuration

### Apache Configuration

Create `/etc/apache2/sites-available/mikipark.conf`:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/mikipark/public
    
    <Directory /var/www/mikipark/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mikipark_error.log
    CustomLog ${APACHE_LOG_DIR}/mikipark_access.log combined
</VirtualHost>

<VirtualHost *:443>
    ServerName your-domain.com
    DocumentRoot /var/www/mikipark/public
    
    SSLEngine on
    SSLCertificateFile /path/to/your/certificate.crt
    SSLCertificateKeyFile /path/to/your/private.key
    
    <Directory /var/www/mikipark/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mikipark_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/mikipark_ssl_access.log combined
</VirtualHost>
```

### Nginx Configuration

Create `/etc/nginx/sites-available/mikipark`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/mikipark/public;
    index index.php;

    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## 🔒 Security Configuration

### 1. File Permissions
```bash
# Set secure permissions
find /var/www/mikipark -type f -exec chmod 644 {} \;
find /var/www/mikipark -type d -exec chmod 755 {} \;
chmod -R 775 /var/www/mikipark/storage
chmod -R 775 /var/www/mikipark/bootstrap/cache
```

### 2. Environment Security
```bash
# Secure .env file
chmod 600 /var/www/mikipark/.env
chown www-data:www-data /var/www/mikipark/.env
```

### 3. Database Security
- Use strong passwords
- Create dedicated database user with minimal privileges
- Enable SSL connections if possible

## 📊 Monitoring & Maintenance

### Log Files
- **Laravel Logs**: `/var/www/mikipark/storage/logs/`
- **Web Server Logs**: `/var/log/apache2/` or `/var/log/nginx/`

### Regular Maintenance
```bash
# Clear application cache
php artisan cache:clear

# Clear view cache
php artisan view:clear

# Clear route cache
php artisan route:clear

# Optimize application
php artisan optimize
```

### Database Backup
```bash
# Create backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p parking_system > /backups/mikipark_$DATE.sql
```

## 🧪 Testing Deployment

### 1. Health Check
Visit: `https://your-domain.com/test.html`

### 2. API Endpoints Test
```bash
# Test API connectivity
curl -X GET "https://your-domain.com/api/parking-spots" \
     -H "Accept: application/json"

# Test authentication
curl -X POST "https://your-domain.com/api/auth/login" \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@yourdomain.com","password":"your_password"}'
```

### 3. Frontend Test
- Navigate to `https://your-domain.com`
- Test user registration and login
- Verify parking spot browsing
- Test reservation functionality

## 🔧 Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   ```bash
   # Check Laravel logs
   tail -f /var/www/mikipark/storage/logs/laravel.log
   
   # Check web server logs
   tail -f /var/log/apache2/error.log
   ```

2. **Database Connection Issues**
   ```bash
   # Test database connection
   php artisan tinker --execute="DB::connection()->getPdo();"
   ```

3. **Permission Issues**
   ```bash
   # Fix storage permissions
   sudo chown -R www-data:www-data /var/www/mikipark/storage
   sudo chmod -R 775 /var/www/mikipark/storage
   ```

4. **JWT Token Issues**
   ```bash
   # Regenerate JWT secret
   php artisan jwt:secret --force
   php artisan config:cache
   ```

## 📱 Mobile & Browser Support

The application supports:
- **Desktop**: Chrome, Firefox, Safari, Edge (latest versions)
- **Mobile**: iOS Safari, Chrome Mobile, Samsung Internet
- **Tablets**: All modern tablet browsers

## 🔄 Updates & Maintenance

### Updating the Application
```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev
npm install --production

# Run migrations
php artisan migrate --force

# Rebuild frontend
npm run build

# Clear and cache
php artisan optimize
```

## 📞 Support

For deployment support:
- Check the troubleshooting section
- Review Laravel and server logs
- Verify all prerequisites are met
- Test individual components

---

**MikiPark** - Smart Parking Management System  
Deployed with ❤️ using Laravel 11 + React 18