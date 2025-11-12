# 📱 Sistem Absensi Karyawan & PKL

Aplikasi absensi berbasis web untuk karyawan dan siswa PKL dengan fitur scan QR Code, geolocation, dan manajemen shift.

## ✨ Fitur Utama

### 👤 User (Karyawan/Siswa PKL)
- ✅ Absen masuk/pulang dengan scan QR Code
- ✅ Validasi lokasi menggunakan GPS
- ✅ Riwayat absensi
- ✅ Pengajuan cuti/izin
- ✅ Profile management

### 👨‍💼 Admin
- ✅ Dashboard dengan statistik real-time
- ✅ Manajemen karyawan & siswa PKL
- ✅ Manajemen shift kerja
- ✅ Generate QR Code absensi
- ✅ Laporan absensi (Excel/PDF)
- ✅ Import/Export data
- ✅ Master data (Divisi, Pendidikan, Jabatan)

## 🛠️ Tech Stack

- **Framework**: Laravel 11
- **Frontend**: Livewire 3, TailwindCSS, Alpine.js
- **Database**: MySQL/MariaDB
- **Authentication**: Laravel Jetstream
- **QR Code**: HTML5-QRCode Scanner
- **Map**: Leaflet.js

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB
- XAMPP/Laragon (recommended)

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/tplinktlwn722n/Absensi-pkl.git
cd Absensi-pkl
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

### 4. Konfigurasi Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi & Seeding Database
```bash
php artisan migrate:fresh --seed
```

### 6. Build Assets
```bash
npm run build
```

### 7. Jalankan Server
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## 👥 Default Login

### Superadmin
- Email: `superadmin@absensi.test`
- Password: `password`

### Admin
- Email: `admin@absensi.test`
- Password: `password`

### User/Karyawan
- Email: `user@absensi.test`
- Password: `password`

## 🌐 Sharing Project dengan Teman

### Menggunakan VS Code Dev Tunnels (Recommended)

1. **Start Laravel Server**:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

2. **Di VS Code**:
   - Buka Panel "PORTS" (View → Ports)
   - Forward port 8000
   - Set visibility ke "Public"
   - Copy link tunnel yang diberikan

3. **Update .env** (saat sharing):
```env
APP_URL=https://your-tunnel-url.devtunnels.ms
```

4. **Clear cache**:
```bash
php artisan config:clear
```

5. **Share link** ke teman Anda!

### Alternatif: Ngrok
```bash
ngrok http 8000
```

## 📁 Struktur Project

```
absensi-pkl/
├── app/
│   ├── Actions/         # Custom actions (Login, Fortify, Jetstream)
│   ├── Exports/         # Export Excel classes
│   ├── Http/
│   │   ├── Controllers/ # Controllers
│   │   └── Middleware/  # Custom middleware
│   ├── Imports/         # Import Excel classes
│   ├── Livewire/        # Livewire components
│   ├── Models/          # Eloquent models
│   └── View/            # View components
├── database/
│   ├── migrations/      # Database migrations
│   └── seeders/         # Database seeders
├── public/
│   └── assets/          # Static assets (images, js, css)
├── resources/
│   ├── css/             # Tailwind CSS
│   ├── js/              # JavaScript files
│   └── views/           # Blade templates
└── routes/
    ├── api.php          # API routes
    ├── console.php      # Console commands
    └── web.php          # Web routes
```

## 🔧 Development

### Compile Assets (Development)
```bash
npm run dev
```

### Compile Assets (Production)
```bash
npm run build
```

### Clear All Cache
```bash
php artisan optimize:clear
```

## 📝 License

This project is open-sourced software licensed under the MIT license.

## 👨‍💻 Developer

Developed by [tplinktlwn722n](https://github.com/tplinktlwn722n)
