@echo off
echo Starting Backend and Frontend Servers...
echo ======================================

cd /d "d:\backend-and-frontend-master\backend-new"
start "Laravel Server" php artisan serve --port=8001

cd /d "d:\backend-and-frontend-master\v0-pertamina-frontend-build"
start "Next.js Server" npm run dev

cd /d "d:\backend-and-frontend-master\backend-new"
node server.js

echo Servers started successfully!
pause