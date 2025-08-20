# 🅿️ MikiPark - Smart Parking Management System

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![React](https://img.shields.io/badge/React-18.x-61DAFB?style=for-the-badge&logo=react&logoColor=black)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JWT](https://img.shields.io/badge/JWT-Authentication-000000?style=for-the-badge&logo=jsonwebtokens&logoColor=white)

**A modern, full-stack parking management system with real-time spot tracking, comprehensive admin dashboard, and seamless user experience.**

[🚀 Live Demo](#) • [📖 Documentation](#-api-documentation) • [🐛 Report Bug](https://github.com/Kidusdybala/Parking-System/issues) • [✨ Request Feature](https://github.com/Kidusdybala/Parking-System/issues)

</div>

---

## 📋 Table of Contents

- [✨ Features](#-features)
- [🏗️ Technology Stack](#️-technology-stack)
- [📋 Prerequisites](#-prerequisites)
- [🚀 Quick Start](#-quick-start)
- [🔑 Default Credentials](#-default-credentials)
- [📚 API Documentation](#-api-documentation)
- [🎨 Screenshots](#-screenshots)
- [🚀 Deployment](#-deployment)
- [🧪 Testing](#-testing)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)

---

## ✨ Features

### 🔐 **Authentication & Security**
- **JWT-based Authentication** - Secure token-based auth system
- **Role-based Access Control** - User/Admin role management
- **Automatic Token Refresh** - Seamless session management
- **Password Security** - Bcrypt hashing with validation

### 🅿️ **Parking Management**
- **Real-time Spot Tracking** - Live availability updates
- **Interactive Spot Visualization** - Visual parking layout
- **Location-based Filtering** - Find spots by section/area
- **Dynamic Pricing** - Flexible hourly rate management
- **Spot Status Management** - Available/Occupied/Maintenance states

### 📅 **Reservation System**
- **Smart Booking** - Conflict detection and prevention
- **Automatic Cost Calculation** - Real-time pricing updates
- **Balance-based Payments** - Integrated wallet system
- **Reservation History** - Complete booking records
- **Status Tracking** - Reserved/Active/Completed states

### 👤 **User Management**
- **Profile Management** - Complete user settings
- **Balance System** - Add funds and track spending
- **Booking History** - Detailed reservation records
- **Usage Statistics** - Personal parking analytics
- **Account Security** - Password change functionality

### 🛠️ **Admin Dashboard**
- **System Analytics** - Revenue, usage, and performance metrics
- **User Management** - Complete CRUD operations
- **Spot Management** - Add, edit, delete parking spots
- **Payment History** - Transaction tracking and reporting
- **Reservation Oversight** - Monitor all bookings
- **Real-time Statistics** - Live system metrics

---

## 🏗️ Technology Stack

<table>
<tr>
<td>

### **Backend**
- **Laravel 11** - Modern PHP framework
- **MySQL 8.0** - Reliable database system
- **JWT Authentication** - Secure token management
- **RESTful API** - Clean API architecture
- **Eloquent ORM** - Database abstraction

</td>
<td>

### **Frontend**
- **React 18** - Modern UI framework
- **React Router v6** - Client-side routing
- **Axios** - HTTP client library
- **Tailwind CSS** - Utility-first styling
- **Vite** - Fast build tool

</td>
</tr>
</table>

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.0
- **MySQL** >= 8.0
- **Git** >= 2.0

---

## 🚀 Quick Start

### 1️⃣ **Clone Repository**
```bash
git clone https://github.com/Kidusdybala/Parking-System.git
cd Parking-System
```

### 2️⃣ **Backend Setup**
```bash
# Install PHP dependencies
composer install

# Copy environment configuration
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret key
php artisan jwt:secret
```

### 3️⃣ **Database Configuration**
Edit your `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parking_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4️⃣ **Database Setup**
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE parking_system;"

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed

# Create admin user
php artisan db:seed --class=AdminUserSeeder
```

### 5️⃣ **Frontend Setup**
```bash
# Install Node.js dependencies
npm install

# Build for production
npm run build

# Or run development server
npm run dev
```

### 6️⃣ **Start Application**
```bash
# Start Laravel development server
php artisan serve

# Application will be available at:
# http://127.0.0.1:8000
```

---

## 🔑 Default Credentials

### 👨‍💼 **Admin Account**
```
Email: admin@parking.com
Password: admin123
```

### 👤 **Test User Account**
```
Email: user@example.com
Password: password
```

> **Note:** Change these credentials in production!

---

## 📚 API Documentation

### 🔐 **Authentication Endpoints**
| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/auth/register` | User registration |
| `POST` | `/api/auth/login` | User login |
| `GET` | `/api/auth/me` | Get current user |
| `POST` | `/api/auth/logout` | User logout |
| `POST` | `/api/auth/refresh` | Refresh JWT token |

### 🅿️ **Parking Spots Endpoints**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/api/parking-spots` | Get all parking spots | ✅ |
| `GET` | `/api/parking-spots/{id}` | Get specific spot | ✅ |
| `GET` | `/api/parking-spots/available/list` | Get available spots | ✅ |
| `POST` | `/api/parking-spots` | Create parking spot | 👨‍💼 Admin |
| `PUT` | `/api/parking-spots/{id}` | Update parking spot | 👨‍💼 Admin |
| `DELETE` | `/api/parking-spots/{id}` | Delete parking spot | 👨‍💼 Admin |

### 📅 **Reservations Endpoints**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/api/reservations` | Get user's reservations | ✅ |
| `GET` | `/api/reservations/all` | Get all reservations | 👨‍💼 Admin |
| `POST` | `/api/reservations` | Create reservation | ✅ |
| `POST` | `/api/reservations/{id}/start` | Start parking session | ✅ |
| `POST` | `/api/reservations/{id}/end` | End parking session | ✅ |
| `POST` | `/api/reservations/{id}/cancel` | Cancel reservation | ✅ |

### 👥 **Users Endpoints**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/api/users` | Get all users | 👨‍💼 Admin |
| `GET` | `/api/users/{id}` | Get specific user | ✅ |
| `PUT` | `/api/users/{id}` | Update user profile | ✅ |
| `POST` | `/api/users/{id}/add-balance` | Add balance to user | 👨‍💼 Admin |
| `DELETE` | `/api/users/{id}` | Delete user | 👨‍💼 Admin |

---

## 🎨 Screenshots

<div align="center">

### 🏠 **Homepage**
*Clean, modern landing page with clear call-to-action*

### 📊 **User Dashboard**
*Comprehensive overview of parking activity and statistics*

### 🅿️ **Parking Spots**
*Interactive parking layout with real-time availability*

### 👨‍💼 **Admin Dashboard**
*Complete system management with analytics and controls*

</div>

---

## 🚀 Deployment

### 🌐 **Production Deployment**

#### **Server Requirements**
- Ubuntu 20.04+ / CentOS 8+
- PHP 8.2+ with extensions
- MySQL 8.0+
- Nginx/Apache
- SSL Certificate

#### **Deployment Steps**
```bash
# 1. Clone and setup
git clone https://github.com/Kidusdybala/Parking-System.git
cd Parking-System

# 2. Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 3. Configure environment
cp .env.example .env
# Edit .env with production settings

# 4. Generate keys and migrate
php artisan key:generate
php artisan jwt:secret
php artisan migrate --force

# 5. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### **Nginx Configuration**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/parking-system/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 🧪 Testing

### **Run Backend Tests**
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### **Test Database**
```bash
# Create test database
mysql -u root -p -e "CREATE DATABASE parking_system_test;"

# Run migrations for testing
php artisan migrate --env=testing
```

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/AmazingFeature`)
3. **Commit** your changes (`git commit -m 'Add some AmazingFeature'`)
4. **Push** to the branch (`git push origin feature/AmazingFeature`)
5. **Open** a Pull Request

### **Development Guidelines**
- Follow PSR-12 coding standards for PHP
- Use ESLint and Prettier for JavaScript
- Write tests for new features
- Update documentation as needed

---

## 🐛 Troubleshooting

<details>
<summary><strong>Common Issues & Solutions</strong></summary>

### **JWT Secret Not Set**
```bash
php artisan jwt:secret
```

### **Permission Errors**
```bash
sudo chmod -R 755 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### **Database Connection Issues**
- Verify database credentials in `.env`
- Ensure MySQL service is running
- Check firewall settings

### **Frontend Build Issues**
```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

### **CORS Issues**
- Check `config/cors.php` configuration
- Verify allowed origins and methods

</details>

---

## 📞 Support

- 📧 **Email**: support@mikipark.com
- 🐛 **Issues**: [GitHub Issues](https://github.com/Kidusdybala/Parking-System/issues)
- 📖 **Documentation**: [Wiki](https://github.com/Kidusdybala/Parking-System/wiki)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/Kidusdybala/Parking-System/discussions)

---

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

<div align="center">

**⭐ Star this repository if you find it helpful!**

**MikiPark** - Smart Parking Management System  
Built with ❤️ using Laravel & React

[⬆ Back to Top](#️-mikipark---smart-parking-management-system)

</div>