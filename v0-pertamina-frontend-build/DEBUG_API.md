# Debug API Connection

## Cara Debug Data Backend Tidak Muncul

### 1. Cek Backend Server Berjalan
```powershell
# Pastikan Laravel backend berjalan di port 8000
cd backend-new
php artisan serve
```

### 2. Test API Endpoint Langsung
Buka browser dan test endpoint:
- http://127.0.0.1:8000/api/stats
- http://127.0.0.1:8000/api/buildings
- http://127.0.0.1:8000/api/rooms
- http://127.0.0.1:8000/api/cctvs

### 3. Cek Browser Console
1. Buka aplikasi di browser (http://localhost:3000)
2. Buka Developer Tools (F12)
3. Lihat tab **Console** - akan ada log `[API]` yang menunjukkan:
   - URL yang di-fetch
   - Status response
   - Data yang diterima
   - Error jika ada

### 4. Cek Network Tab
1. Di Developer Tools, buka tab **Network**
2. Filter by "XHR" atau "Fetch"
3. Lihat request ke `/api/*`
4. Cek:
   - Status code (harus 200)
   - Response body
   - Headers (CORS)

### 5. Cek CORS Configuration
Pastikan di `backend-new/config/cors.php` atau middleware CORS mengizinkan:
- Origin: `http://localhost:3000`
- Methods: GET, POST, etc.
- Headers: Content-Type, etc.

### 6. Common Issues

#### Backend Tidak Berjalan
- Error: "Cannot connect to backend server"
- Solusi: Jalankan `php artisan serve` di folder `backend-new`

#### CORS Error
- Error di console: "CORS policy" atau "Access-Control-Allow-Origin"
- Solusi: Cek CORS middleware di Laravel

#### API Endpoint Salah
- Error: "404 Not Found"
- Solusi: Pastikan route di `backend-new/routes/api.php` benar

#### Database Kosong
- API berhasil tapi data kosong
- Solusi: Cek database, mungkin perlu seed data

### 7. Logging yang Sudah Ditambahkan

Sekarang semua API call akan log:
- `[API] Fetching: <url>` - URL yang di-fetch
- `[API] Response status: <status>` - Status HTTP
- `[API] Success response: <data>` - Data yang diterima
- `[API] Error response body: <error>` - Error jika ada

Lihat console browser untuk detail lengkap!

