# Pertamina Fullstack Application

This is a fully integrated fullstack application combining:
- **Backend**: Laravel 12 with Filament v4 Admin Panel
- **Frontend**: React/TypeScript Next.js Application
- **Streaming**: Node Media Server with FFmpeg for CCTV streaming

## 🏗️ Architecture

```
.
├── backend-new/                 # Laravel 12 + Filament v4 Backend
│   ├── app/                     # Laravel application code
│   ├── frontend/                # Next.js standalone build
│   └── public/                  # Laravel public directory
├── streaming-server/            # Node Media Server for CCTV streaming
│   ├── server.js                # Streaming server implementation
│   └── streams/                 # HLS stream storage directory
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

3. **Streaming Server** (Ports 1935, 8000, 3002):
   - RTMP server on port 1935 for receiving streams
   - HTTP server on port 8000 for serving HLS streams
   - API server on port 3002 for stream management

4. **Unified Access** (Port 8000):
   - Both applications accessible through single URL: http://localhost:8000

## 🛠️ Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18 or higher
- npm or yarn
- MySQL or compatible database
- FFmpeg (for CCTV streaming)

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

3. **Install streaming server dependencies**:
   ```bash
   cd ../streaming-server
   npm install
   ```

4. **Install FFmpeg**:
   - Download FFmpeg from https://ffmpeg.org/
   - Add FFmpeg to your system PATH
   - Verify installation: `ffmpeg -version`

5. **Configure environment**:
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

6. **Run database migrations and seeders**:
   ```bash
   php artisan migrate
   php artisan db:seed --class=SuperAdminSeeder
   php artisan db:seed --class=RolePermissionSeeder
   ```

7. **Build the frontend**:
   ```bash
   cd ../v0-pertamina-frontend-build
   npm run build
   ```

8. **Copy standalone build to backend**:
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

2. **Start streaming server**:
   ```bash
   cd ../streaming-server
   node server.js
   ```

3. **Start Next.js frontend** (development mode):
   ```bash
   cd ../v0-pertamina-frontend-build
   npm run dev
   ```

> ⚠️ **Note on Turbopack Timeout Issues**: If you encounter "signal timed out" errors with Next.js 16.0.0, please refer to [TURBOPACK_TIMEOUT_FIX.md](TURBOPACK_TIMEOUT_FIX.md) for solutions.

4. **Access the application**: http://localhost:8000

### Option 3: Production mode
1. **Start Laravel backend**:
   ```bash
   cd backend-new
   php artisan serve
   ```

2. **Start streaming server**:
   ```bash
   cd ../streaming-server
   node server.js
   ```

3. **Start Next.js frontend** (production mode):
   ```bash
   cd backend-new/frontend
   node server.js
   ```

## 🌐 Access Points

- **Main Application**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **API Endpoints**: http://localhost:8000/api/*
- **Streaming Server API**: http://localhost:3002/api/*
- **HLS Streams**: http://localhost:8000/live/{cctv_id}/index.m3u8
- **Direct Frontend Access**: http://localhost:3001 (development only)

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

### Streaming Server
- Main server: `streaming-server/server.js`
- Stream storage: `streaming-server/streams/`
- API endpoints:
  - `GET /api/start-stream/:cctvId` - Start streaming for CCTV
  - `POST /api/stop-stream/:cctvId` - Stop streaming for CCTV
  - `GET /api/stream-status/:cctvId` - Check streaming status

## 🛠️ Development Workflow

1. **Frontend Development**:
   ```bash
   cd v0-pertamina-frontend-build
   npm run dev
   ```

   > ⚠️ **Note**: The development server now runs on port 3001 instead of 3000 to avoid conflicts.

2. **Backend Development**:
   ```bash
   cd backend-new
   php artisan serve
   ```

3. **Streaming Server Development**:
   ```bash
   cd streaming-server
   node server.js
   ```

4. **Rebuild Frontend** (after changes):
   ```bash
   cd v0-pertamina-frontend-build
   npm run build
   xcopy .next\standalone\v0-pertamina-frontend-build ..\backend-new\frontend /E /I /H
   ```

## 🔧 Troubleshooting

### Common Issues

1. **Port already in use**:
   - Kill processes using ports 8000, 3000, 1935, or 3002:
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
   - Verify Next.js server is accessible at http://localhost:3001

4. **API calls failing**:
   - Check CORS configuration
   - Verify API routes in `backend-new/routes/api.php`
   - Test endpoints directly with curl or Postman

5. **Streaming not working**:
   - Verify FFmpeg is installed and accessible
   - Check streaming server logs
   - Ensure CCTV cameras have valid RTSP URLs
   - Verify ports 1935, 8000, and 3002 are available

6. **HLS stream playback issues**:
   - Check if streams are being generated in `streaming-server/streams/`
   - Verify network connectivity to streaming server
   - Ensure browser supports HLS playback

7. **Turbopack timeout errors**:
   - If you encounter "signal timed out" errors, refer to [TURBOPACK_TIMEOUT_FIX.md](TURBOPACK_TIMEOUT_FIX.md)
   - Try increasing system resources or using Webpack instead of Turbopack
   - Clear the Next.js cache by deleting the `.next` folder

### Logs

- Laravel logs: `backend-new/storage/logs/laravel.log`
- Next.js server logs: Terminal output when running `node server.js`
- Streaming server logs: Terminal output when running `node streaming-server/server.js`

## 🎯 Features

### Backend (Laravel + Filament)
- RESTful API endpoints
- Filament admin panel with CRUD operations
- User authentication and authorization
- Database migrations and seeders
- Caching for performance optimization
- Error handling and logging
- CCTV management with RTSP URL configuration

### Frontend (Next.js + React)
- Responsive UI components
- API integration with error handling
- Dynamic routing
- Server-side rendering
- Static asset optimization
- TypeScript type safety
- Live CCTV streaming with HLS playback
- Interactive maps with Leaflet.js
- Real-time dashboard with charts

### Streaming Server (Node Media Server)
- RTMP to HLS conversion using FFmpeg
- Dynamic stream creation based on CCTV requests
- Stream management API
- Concurrent stream handling
- Automatic stream cleanup

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
- Stream access control

## 🚀 Performance

- Database query optimization
- API response caching
- Static asset compression
- Lazy loading components
- Code splitting
- Stream caching and reuse

## 📚 Additional Documentation

For more detailed information about the integration, see:
- [Integration Guide](INTEGRATION_GUIDE.md) - Detailed explanation of how Laravel, Filament, and Next.js work together