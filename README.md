# Pertamina Fullstack Application

This is a fully integrated fullstack application combining:
- **Backend**: Laravel 12 with Filament v4 Admin Panel
- **Frontend**: React/TypeScript Next.js Application

## 🏗️ Architecture

```
.
├── backend-new/                 # Laravel 12 + Filament v4 Backend
│   ├── app/                     # Laravel application code
│   ├── frontend/                # Next.js standalone build
│   └── public/                  # Laravel public directory
└── v0-pertamina-frontend-build/ # Next.js frontend source code
```

## 🚀 How It Works

1. **Laravel Backend** (Port 8000):
   - Serves API endpoints at `/api/*`
   - Serves Filament admin panel at `/admin/*`
   - Proxies all other requests to Next.js frontend

2. **Next.js Frontend** (Port 3000):
   - Serves the React/TypeScript frontend application
   - Communicates with Laravel backend via API calls

3. **Unified Access** (Port 8000):
   - Both applications accessible through single URL: http://localhost:8000

## 🛠️ Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18 or higher
- npm or yarn
- MySQL or compatible database

## 📦 Installation

1. **Install Laravel dependencies**:
   ```bash
   cd backend-new
   composer install
   ```

2. **Install frontend dependencies**:
   ```bash
   cd ../v0-pertamina-frontend-build
   npm install
   ```

3. **Configure environment**:
   ```bash
   cd ../backend-new
   cp .env.example .env
   php artisan key:generate
   ```
   
   Update database configuration in `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Run database migrations and seeders**:
   ```bash
   php artisan migrate
   php artisan db:seed --class=SuperAdminSeeder
   php artisan db:seed --class=RolePermissionSeeder
   ```

5. **Build the frontend**:
   ```bash
   cd ../v0-pertamina-frontend-build
   npm run build
   ```

6. **Copy standalone build to backend**:
   ```bash
   # From v0-pertamina-frontend-build directory
   xcopy .next\standalone\v0-pertamina-frontend-build ..\backend-new\frontend /E /I /H
   ```

## ▶️ Running the Application

### Option 1: Using the startup script (Recommended)
```bash
# From the root directory
npm start
```

### Option 2: Manual start
1. **Start Laravel backend**:
   ```bash
   cd backend-new
   php artisan serve
   ```

2. **Start Next.js frontend** (development mode):
   ```bash
   cd v0-pertamina-frontend-build
   npm run dev
   ```

3. **Access the application**: http://localhost:8000

### Option 3: Production mode
1. **Start Laravel backend**:
   ```bash
   cd backend-new
   php artisan serve
   ```

2. **Start Next.js frontend** (production mode):
   ```bash
   cd backend-new/frontend
   node server.js
   ```

## 🌐 Access Points

- **Main Application**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **API Endpoints**: http://localhost:8000/api/*
- **Direct Frontend Access**: http://localhost:3000 (development only)

## 🧪 Testing API Endpoints

You can test the API endpoints directly:
- http://localhost:8000/api/stats
- http://localhost:8000/api/buildings
- http://localhost:8000/api/rooms
- http://localhost:8000/api/cctvs
- http://localhost:8000/api/contacts

## 📁 Key Components

### Routing
- API routes: `backend-new/routes/api.php`
- Web routes: `backend-new/routes/web.php`

### Filament Admin Panel
- Configuration: `backend-new/app/Providers/Filament/AdminPanelProvider.php`
- Resources: `backend-new/app/Filament/Resources/`
- Pages: `backend-new/app/Filament/Pages/`
- Widgets: `backend-new/app/Filament/Widgets/`

### Frontend API Integration
- API configuration: `v0-pertamina-frontend-build/lib/api.ts`
- Base URL: `http://127.0.0.1:8000/api` (direct connection)

## 🛠️ Development Workflow

1. **Frontend Development**:
   ```bash
   cd v0-pertamina-frontend-build
   npm run dev
   ```

2. **Backend Development**:
   ```bash
   cd backend-new
   php artisan serve
   ```

3. **Rebuild Frontend** (after changes):
   ```bash
   cd v0-pertamina-frontend-build
   npm run build
   xcopy .next\standalone\v0-pertamina-frontend-build ..\backend-new\frontend /E /I /H
   ```

## 🔧 Troubleshooting

### Common Issues

1. **Port already in use**:
   - Kill processes using ports 8000 or 3000:
     ```bash
     netstat -ano | findstr :8000
     taskkill /PID <process_id> /F
     ```

2. **Database connection errors**:
   - Verify database credentials in `.env`
   - Ensure MySQL service is running
   - Check if database exists

3. **Frontend not loading**:
   - Ensure both servers are running
   - Check Laravel logs: `backend-new/storage/logs/laravel.log`
   - Verify Next.js server is accessible at http://localhost:3000

4. **API calls failing**:
   - Check CORS configuration
   - Verify API routes in `backend-new/routes/api.php`
   - Test endpoints directly with curl or Postman

### Logs

- Laravel logs: `backend-new/storage/logs/laravel.log`
- Next.js server logs: Terminal output when running `node server.js`

## 🎯 Features

### Backend (Laravel + Filament)
- RESTful API endpoints
- Filament admin panel with CRUD operations
- User authentication and authorization
- Database migrations and seeders
- Caching for performance optimization
- Error handling and logging

### Frontend (Next.js + React)
- Responsive UI components
- API integration with error handling
- Dynamic routing
- Server-side rendering
- Static asset optimization
- TypeScript type safety

## 📱 Responsive Design

The application is fully responsive and works on:
- Desktop browsers
- Tablet devices
- Mobile phones

All pages maintain proper layout across different screen sizes and orientations.

## 🔒 Security

- CSRF protection for forms
- SQL injection prevention
- XSS attack prevention
- Secure API endpoints
- Role-based access control

## 🚀 Performance

- Database query optimization
- API response caching
- Static asset compression
- Lazy loading components
- Code splitting

## 📚 Additional Documentation

For more detailed information about the integration, see:
- [Integration Guide](INTEGRATION_GUIDE.md) - Detailed explanation of how Laravel, Filament, and Next.js work together