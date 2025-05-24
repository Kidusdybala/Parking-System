# Smart Parking System

A responsive and modern Smart Parking System web application frontend built with HTML, CSS, and Tailwind CSS.

## Features

- **User Authentication**: Login, registration, and email verification
- **Real-time Parking Management**: View available slots, make reservations
- **Reservation System**: 30-minute countdown for pending reservations
- **User Dashboard**: View booking history, manage active reservations
- **Admin Dashboard**: Manage parking slots, view reservations, edit user profiles
- **Dynamic Payment Calculation**: Based on hourly parking rates

## Project Structure

```
├── css/                    # CSS files
│   ├── styles.css          # Compiled Tailwind CSS
│   └── tailwind.css        # Tailwind source CSS
├── js/                     # JavaScript files (if any)
├── pages/                  # HTML pages
│   ├── admin-dashboard.html
│   ├── login.html
│   ├── register.html
│   ├── user-dashboard.html
│   └── verify-email.html
├── assets/                 # Assets directory
│   └── images/             # Image files
├── index.html              # Homepage
├── tailwind.config.js      # Tailwind configuration
├── package.json            # Project dependencies
└── README.md               # Project documentation
```

## How to Use

1. **Installation**:

   ```
   npm install
   ```

2. **Build CSS**:

   ```
   npx tailwindcss -i ./css/tailwind.css -o ./css/styles.css
   ```

3. **Development**:

   ```
   npx tailwindcss -i ./css/tailwind.css -o ./css/styles.css --watch
   ```

4. **Open the Application**:
   - Open `index.html` in your browser to start using the application

## Pages

- **Homepage**: Overview of the parking system and its benefits
- **Login/Register**: User authentication
- **Email Verification**: Verify user emails with a 6-digit code
- **User Dashboard**: Manage parking reservations and view history
- **Admin Dashboard**: Comprehensive admin panel for system management

## Demo Credentials

- **User**:

  - Email: john.doe@example.com
  - Password: password123

- **Admin**:
  - Email: admin@example.com
  - Password: admin123

## Technologies Used

- HTML5
- CSS3
- Tailwind CSS
- JavaScript (vanilla)
- Font Awesome Icons

## Notes

- This is a frontend-only implementation with mock data
- The reservation system uses a 30-minute countdown timer to simulate the actual system behavior
- The payment system is calculated at $2 per hour
