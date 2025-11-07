@echo off
echo Starting Backend Server...
echo =======================

cd /d "d:\backend-and-frontend-master\backend-new"
php artisan serve --port=8001

pause