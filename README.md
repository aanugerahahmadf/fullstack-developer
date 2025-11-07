# Fullstack Developer Project

This repository contains a fullstack web application built with modern technologies including Next.js for the frontend and Laravel for the backend.

## Project Overview

A comprehensive dashboard application for monitoring and managing refinery operations with real-time data visualization, interactive maps, and CCTV monitoring capabilities.

## Features

### Frontend (Next.js)
- **Dashboard**: Real-time monitoring of production metrics and system status
- **Interactive Maps**: Building and room visualization with Leaflet.js
- **CCTV Monitoring**: Live stream viewing for security cameras
- **Playlist Management**: Organized content playback system
- **Responsive Design**: Works on all device sizes
- **Modern UI**: Clean, professional interface with dark theme

### Backend (Laravel)
- **RESTful API**: Comprehensive endpoints for all frontend needs
- **Data Management**: CRUD operations for buildings, rooms, and CCTV cameras
- **Authentication**: Secure user access control
- **Database**: Efficient data storage and retrieval

## Technology Stack

### Frontend
- **Next.js 13+** - React framework with App Router
- **TypeScript** - Type-safe JavaScript
- **Tailwind CSS** - Utility-first CSS framework
- **React Leaflet** - Interactive maps
- **Recharts** - Data visualization
- **Lucide React** - Icon library

### Backend
- **Laravel 10+** - PHP framework
- **MySQL** - Database management
- **Sanctum** - API authentication
- **Filament** - Admin panel

## Installation

### Prerequisites
- Node.js 16+
- PHP 8.1+
- MySQL
- Composer

### Frontend Setup
```bash
cd v0-pertamina-frontend-build
npm install
npm run dev
```

### Backend Setup
```bash
cd backend-new
composer install
cp .env.example .env
php artisan key:generate
# Configure your database in .env
php artisan migrate
php artisan serve
```

## Project Structure

```
├── backend-new/           # Laravel backend
│   ├── app/              # Application logic
│   ├── config/           # Configuration files
│   ├── database/         # Migrations and seeds
│   └── routes/           # API routes
├── v0-pertamina-frontend-build/  # Next.js frontend
│   ├── app/              # Pages and components
│   ├── components/       # Reusable components
│   ├── lib/              # API and utility functions
│   └── public/           # Static assets
```

## Key Components

### Dashboard (`/`)
- Production trends visualization
- Unit performance metrics
- System status monitoring
- Building/Room/CCTV statistics

### Maps (`/maps`)
- Interactive building visualization
- Room and CCTV location mapping
- Live stream access

### Playlist (`/playlist`)
- Hierarchical content organization
- Building → Room → CCTV navigation
- Media playback interface

## API Endpoints

- `GET /api/stats` - System statistics
- `GET /api/buildings` - Building data
- `GET /api/rooms` - Room data
- `GET /api/cctvs` - CCTV camera data
- `GET /api/chart/production-trends` - Production data
- `GET /api/chart/unit-performance` - Performance metrics

## Development

### Frontend Development
```bash
npm run dev     # Development server
npm run build   # Production build
npm run start   # Production server
```

### Backend Development
```bash
php artisan serve           # Development server
php artisan migrate         # Run migrations
php artisan db:seed         # Seed database
php artisan migrate:fresh --seed  # Reset database
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a pull request

## License

This project is proprietary and confidential. All rights reserved.

## Contact

For questions or support, please contact the repository owner.