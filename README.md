# PARKING-SYSTEM

*Effortless Parking Solutions for Modern Living*

![Last Commit](https://img.shields.io/github/last-commit/username/PARKING-SYSTEM)
![Language Count](https://img.shields.io/github/languages/count/username/PARKING-SYSTEM)
![Top Language](https://img.shields.io/github/languages/top/username/PARKING-SYSTEM)
![License](https://img.shields.io/github/license/username/PARKING-SYSTEM)

---

## Built With

![Laravel](https://img.shields.io/badge/-Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/-PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![JavaScript](https://img.shields.io/badge/-JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![MySQL](https://img.shields.io/badge/-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/-Tailwind_CSS-06B6D4?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Vite](https://img.shields.io/badge/-Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![Alpine.js](https://img.shields.io/badge/-Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=black)

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Running the Application](#running-the-application)
- [API Endpoints](#api-endpoints)
- [Contributing](#contributing)
- [License](#license)

## Overview

A comprehensive parking management system built with Laravel that handles reservations, payments, and user management with role-based access control.

## Features

- **User Authentication**: Secure registration, login, and email verification
- **Role-Based Access Control**:
  - Admin: Full system control and analytics
  - Department: Parking management
  - Client: Reservation and payment
- **Parking Management**:
  - Real-time availability
  - Reservation system
  - Duration tracking
- **Payment System**:
  - Wallet integration
  - Payment processing
  - Receipt generation
- **User Profile Management**: Personal information and preferences
- **Real-time Chat**: Support communication

## Technologies Used

- **Backend**: Laravel 10, PHP 8.2
- **Frontend**: JavaScript, Alpine.js, Tailwind CSS
- **Database**: MySQL
- **Build Tool**: Vite
- **Additional**: Axios for HTTP requests

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/kidusdybala/PARKING-SYSTEM.git
   cd PARKING-SYSTEM
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```


3. Install Node.js dependencies:
```bash
   npm install
   # or
   yarn install
```


4. Create a .env file:
```bash
cp .env.example .env
```
5.Generate an application key
```bash
php artisan key:generate
```
6.Configure your database connection in .env:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```
7.Run database migrations:
```bash
php artisan migrate
```
Running the Application
```bash
Development
php artisan serve
npm run dev
```
Production

Use a proper web server pointing to the public directory.

Optimize for production:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
API Endpoints

/dashboard (Admin)

/department/dashboard (Department)

/client/dashboard, /client/map/manage, etc. (Client)

/profile, /wallet, /parking, /chat (Authenticated Users)

Check routes/web.php for a complete list.

Contributing

Fork the repo

Create a new branch: git checkout -b feature/YourFeature

Make changes

Commit: git commit -m "Add your feature"

Push: git push origin feature/YourFeature

Open a Pull Request

License

This project is licensed under the MIT License. See the LICENSE file for details.
