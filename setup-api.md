# Parking System API Setup Guide

## Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL database
- Laravel 11.x

## Installation Steps

### 1. Install Dependencies
```bash
# Install Laravel Sanctum (if not already installed)
composer require laravel/sanctum

# Install other dependencies if needed
composer install
```

### 2. Environment Configuration
Update your `.env` file:
```env
# Database configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parking_system
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Sanctum configuration
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1
SANCTUM_TOKEN_EXPIRATION=null

# API configuration
API_PREFIX=api
API_VERSION=v1
```

### 3. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed the database with test data
php artisan db:seed --class=ApiDataSeeder
```

### 4. Publish Sanctum Configuration (Optional)
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 5. Register Middleware (if needed)
Add to `bootstrap/app.php` or `app/Http/Kernel.php`:
```php
// In bootstrap/app.php (Laravel 11)
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
    
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

### 6. Start the Development Server
```bash
php artisan serve
```

## Testing the API

### 1. Using cURL
```bash
# Register a new user
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'

# Use the token from login response
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 2. Using Postman
1. Import the `Parking_System_API.postman_collection.json` file
2. Set the `base_url` variable to `http://localhost:8000/api`
3. Use the Login request to get a token
4. The token will be automatically set for other requests

### 3. Test Credentials
After running the seeder, you can use these test accounts:

**Admin Account:**
- Email: `admin@parking.com`
- Password: `admin123`

**Client Accounts:**
- Email: `client1@example.com` / Password: `password123`
- Email: `client2@example.com` / Password: `password123`

## API Endpoints Overview

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `GET /api/auth/me` - Get current user info
- `POST /api/auth/logout` - Logout current session
- `POST /api/auth/change-password` - Change password

### Parking Spots
- `GET /api/parking-spots` - Get all parking spots
- `GET /api/parking-spots/{id}` - Get specific parking spot
- `GET /api/parking-spots/available/list` - Get available spots
- `GET /api/parking-spots/recommend/{userId}` - Get recommended spot
- `POST /api/parking-spots` - Create parking spot (Admin only)
- `PUT /api/parking-spots/{id}` - Update parking spot (Admin only)
- `DELETE /api/parking-spots/{id}` - Delete parking spot (Admin only)

### Reservations
- `GET /api/reservations` - Get user's reservations
- `GET /api/reservations/all` - Get all reservations (Admin only)
- `POST /api/reservations` - Create new reservation
- `GET /api/reservations/{id}` - Get specific reservation
- `PUT /api/reservations/{id}` - Update reservation
- `POST /api/reservations/{id}/cancel` - Cancel reservation
- `GET /api/reservations/statistics` - Get statistics (Admin only)

### User Management
- `GET /api/users` - Get all users (Admin only)
- `GET /api/users/{id}` - Get specific user
- `PUT /api/users/{id}` - Update user
- `POST /api/users/{id}/add-balance` - Add balance to user
- `GET /api/users/statistics` - Get user statistics (Admin only)

## Security Features

1. **JWT Token Authentication**: Secure token-based authentication using Laravel Sanctum
2. **Role-based Access Control**: Different access levels for admin and client roles
3. **Input Validation**: Comprehensive validation for all API endpoints
4. **Rate Limiting**: Built-in rate limiting to prevent abuse
5. **CORS Support**: Configurable CORS settings for frontend integration

## Error Handling

The API returns consistent error responses:
- `400` - Bad Request (business logic errors)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found (resource doesn't exist)
- `422` - Unprocessable Entity (validation errors)
- `500` - Internal Server Error

## Next Steps

1. **Frontend Integration**: Use the API with your frontend application (React, Vue, Angular, etc.)
2. **Mobile App**: Integrate with mobile applications using the same API endpoints
3. **Documentation**: The API is fully documented in `API_DOCUMENTATION.md`
4. **Testing**: Write automated tests for the API endpoints
5. **Deployment**: Deploy to production with proper environment configuration

## Troubleshooting

### Common Issues:

1. **Token not working**: Make sure to include `Bearer ` prefix in Authorization header
2. **CORS errors**: Update `SANCTUM_STATEFUL_DOMAINS` in `.env` file
3. **Database errors**: Run migrations and check database connection
4. **Permission errors**: Ensure user has correct role for protected endpoints

### Debug Mode:
Set `APP_DEBUG=true` in `.env` for detailed error messages during development.