# Parking Management System

A full-stack parking management system with separated backend and frontend architecture.

## Project Structure

```
Parking-System/
├── backend/          # Laravel API Backend
│   ├── app/         # Laravel application files
│   ├── config/      # Configuration files
│   ├── database/    # Migrations, seeders, factories
│   ├── routes/      # API routes
│   ├── storage/     # File storage
│   ├── tests/       # Backend tests
│   ├── .env         # Backend environment variables
│   ├── artisan      # Laravel CLI
│   └── composer.json # PHP dependencies
├── frontend/         # React Frontend Application
│   ├── src/         # React source files
│   ├── public/      # Static assets
│   ├── .env         # Frontend environment variables
│   ├── package.json # Node.js dependencies
│   └── vite.config.js # Vite configuration
└── package.json     # Root package.json for scripts
```

## Quick Start

### Prerequisites
- PHP 8.1+
- Composer
- Node.js 18+
- npm

### Installation

1. **Install all dependencies:**
   ```bash
   npm run install:all
   ```

2. **Set up the database:**
   ```bash
   npm run migrate:fresh
   ```

3. **Start both backend and frontend:**
   ```bash
   npm run dev
   ```

This will start:
- Backend API on http://127.0.0.1:8000
- Frontend on http://localhost:3000

### Individual Commands

**Backend only:**
```bash
npm run dev:backend
npm run install:backend
npm run test:backend
```

**Frontend only:**
```bash
npm run dev:frontend
npm run install:frontend
npm run test:frontend
```

## API Configuration

The frontend is configured to connect to the backend API at `http://127.0.0.1:8000`. This is set in:
- `frontend/.env` - VITE_API_BASE_URL
- `frontend/src/bootstrap.js` - axios configuration

## Environment Variables

### Backend (.env)
- Database configuration
- JWT settings
- Mail configuration
- App settings

### Frontend (.env)
- VITE_API_BASE_URL=http://127.0.0.1:8000
- VITE_APP_NAME=MikiPark
- Feature flags

## Development

1. Make sure both backend and frontend are running
2. Backend serves API endpoints at `/api/*`
3. Frontend handles all UI and user interactions
4. Authentication uses JWT tokens stored in cookies

## Troubleshooting

If you encounter issues:

1. **Backend not starting:** Check PHP version and composer dependencies
2. **Frontend not connecting:** Verify VITE_API_BASE_URL in frontend/.env
3. **Database issues:** Run `npm run migrate:fresh` to reset database
4. **CORS issues:** Check backend CORS configuration in config/cors.php