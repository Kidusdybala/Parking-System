# MikiPark - Smart Parking Management System

A comprehensive parking management system built with **Laravel 11** (Backend API) and **React 18** (Frontend) using **JWT Authentication**.

## 🚀 Features

### 🔐 Authentication & Authorization
- JWT-based authentication system
- Role-based access control (User/Admin)
- Secure token management with automatic refresh

### 🅿️ Parking Management
- Real-time parking spot availability
- Interactive parking spot visualization
- Location-based spot filtering
- Hourly rate management

### 📅 Reservation System
- Create, update, and cancel reservations
- Real-time conflict detection
- Automatic cost calculation
- Balance-based payment system

### 👤 User Management
- User profile management
- Balance management system
- Reservation history and statistics
- Password change functionality

### 🛠️ Admin Panel
- Complete system dashboard
- Parking spot management (CRUD)
- User management
- Reservation monitoring
- Revenue tracking

## 🏗️ Technology Stack

### Backend
- **Laravel 11** - PHP Framework
- **JWT Auth** - Authentication
- **MySQL** - Database
- **RESTful API** - Architecture

### Frontend
- **React 18** - UI Framework
- **React Router** - Navigation
- **Axios** - HTTP Client
- **Tailwind CSS** - Styling
- **Vite** - Build Tool

## 📋 Prerequisites

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0
- Git

## 🚀 Installation & Setup

### 1. Clone Repository
```bash
git clone <repository-url>
cd Parking-System
```

### 2. Backend Setup
```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Configure database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parking_system
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 3. Frontend Setup
```bash
# Install Node.js dependencies
npm install

# Build for production
npm run build

# Or run development server
npm run dev
```

### 4. Start Application
```bash
# Start Laravel server
php artisan serve

# The application will be available at:
# http://127.0.0.1:8000
```

## 🔑 Default Credentials

### Admin Account
- **Email:** admin@admin.com
- **Password:** admin123

### Test User Account
- **Email:** test@test.com
- **Password:** password

## 📚 API Documentation

### Authentication Endpoints
```
POST /api/auth/register    - User registration
POST /api/auth/login       - User login
GET  /api/auth/me          - Get current user
POST /api/auth/logout      - User logout
POST /api/auth/refresh     - Refresh token
```

### Parking Spots Endpoints
```
GET    /api/parking-spots           - Get all parking spots
GET    /api/parking-spots/{id}      - Get specific parking spot
GET    /api/parking-spots/available/list - Get available spots
POST   /api/parking-spots           - Create parking spot (Admin)
PUT    /api/parking-spots/{id}      - Update parking spot (Admin)
DELETE /api/parking-spots/{id}      - Delete parking spot (Admin)
```

### Reservations Endpoints
```
GET    /api/reservations            - Get user's reservations
GET    /api/reservations/all        - Get all reservations (Admin)
GET    /api/reservations/{id}       - Get specific reservation
POST   /api/reservations            - Create reservation
PUT    /api/reservations/{id}       - Update reservation
POST   /api/reservations/{id}/cancel - Cancel reservation
POST   /api/reservations/{id}/complete - Complete reservation (Admin)
```

### Users Endpoints
```
GET    /api/users                   - Get all users (Admin)
GET    /api/users/{id}              - Get specific user
PUT    /api/users/{id}              - Update user
DELETE /api/users/{id}              - Delete user (Admin)
POST   /api/users/{id}/add-balance  - Add balance to user
PUT    /api/users/{id}/password     - Update user password
```

## 🎨 Frontend Features

### User Interface
- **Responsive Design** - Works on all devices
- **Glass Morphism UI** - Modern, beautiful interface
- **Dark Theme** - Easy on the eyes
- **Interactive Components** - Smooth user experience

### Pages
- **Homepage** - Landing page for unauthenticated users
- **Dashboard** - User dashboard with parking overview
- **Parking** - Browse and reserve parking spots
- **Reservations** - Manage user reservations
- **Profile** - User profile and settings
- **Admin Panel** - Complete system management

## 🔧 Configuration

### Environment Variables
```env
# Application
APP_NAME="MikiPark"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=http://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parking_system
DB_USERNAME=your_username
DB_PASSWORD=your_password

# JWT
JWT_SECRET=your_jwt_secret
JWT_TTL=60
JWT_REFRESH_TTL=20160

# Mail (optional)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

## 🚀 Deployment

### Production Deployment

1. **Server Requirements**
   - PHP 8.2+
   - MySQL 8.0+
   - Nginx/Apache
   - SSL Certificate

2. **Deployment Steps**
   ```bash
   # Clone repository
   git clone <repository-url>
   cd Parking-System
   
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   npm install
   
   # Build frontend
   npm run build
   
   # Configure environment
   cp .env.example .env
   # Edit .env with production settings
   
   # Generate keys
   php artisan key:generate
   php artisan jwt:secret
   
   # Run migrations
   php artisan migrate --force
   
   # Optimize Laravel
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   
   # Set permissions
   chmod -R 755 storage bootstrap/cache
   ```

3. **Web Server Configuration**
   - Point document root to `public/` directory
   - Configure URL rewriting for Laravel
   - Set up SSL certificate

## 🧪 Testing

### Run Tests
```bash
# Backend tests
php artisan test

# Frontend tests (if configured)
npm test
```

### Test Accounts
Use the default credentials provided above for testing different user roles.

## 📱 Mobile Support

The application is fully responsive and works seamlessly on:
- Desktop computers
- Tablets
- Mobile phones
- All modern browsers

## 🔒 Security Features

- JWT token-based authentication
- CSRF protection
- SQL injection prevention
- XSS protection
- Role-based access control
- Secure password hashing

## 🐛 Troubleshooting

### Common Issues

1. **JWT Secret Not Set**
   ```bash
   php artisan jwt:secret
   ```

2. **Permission Errors**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

3. **Database Connection Issues**
   - Check database credentials in `.env`
   - Ensure MySQL service is running

4. **Frontend Build Issues**
   ```bash
   rm -rf node_modules package-lock.json
   npm install
   npm run build
   ```

## 📞 Support

For support and questions:
- Create an issue in the repository
- Check the documentation
- Review the API endpoints

## 📄 License

This project is licensed under the MIT License.

---

**MikiPark** - Smart Parking Management System
Built with ❤️ using Laravel & React