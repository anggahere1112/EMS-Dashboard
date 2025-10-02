# EMS Dashboard

Environment Monitoring System dashboard for monitoring power, temperature, humidity, fire sensors, and smart devices in real time.

## 📋 About

EMS Dashboard adalah sistem monitoring lingkungan yang dirancang untuk memantau berbagai parameter lingkungan secara real-time, termasuk:

- 🔌 **Power Monitoring** - Pemantauan konsumsi daya listrik
- 🌡️ **Temperature Sensors** - Monitoring suhu ruangan
- 💧 **Humidity Sensors** - Pemantauan kelembaban udara
- 🔥 **Fire Detection** - Sistem deteksi kebakaran
- 📱 **Smart Devices** - Integrasi dengan perangkat IoT

## 🛠️ Tech Stack

- **Framework**: Laravel 11
- **Frontend**: Tailwind CSS, Livewire 3
- **Database**: MySQL
- **Authentication**: Laravel Jetstream
- **Real-time**: Laravel Broadcasting
- **Testing**: PHPUnit, Pest

## 📦 Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL >= 8.0
- Git

## 🚀 Installation

1. **Clone repository**
   ```bash
   git clone https://github.com/anggahere1112/EMS-Dashboard.git
   cd EMS-Dashboard
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   
   Edit file `.env` dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ems_dashboard
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Build assets**
   ```bash
   npm run build
   # atau untuk development
   npm run dev
   ```

7. **Start server**
   ```bash
   php artisan serve
   ```

## 🔧 Configuration

### Database Setup
Buat database MySQL baru:
```sql
CREATE DATABASE ems_dashboard;
```

### Environment Variables
Konfigurasi utama di file `.env`:
```env
APP_NAME="EMS Dashboard"
APP_ENV=local
APP_KEY=base64:your-app-key
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ems_dashboard
DB_USERNAME=root
DB_PASSWORD=

# Broadcasting (untuk real-time monitoring)
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your-pusher-app-id
PUSHER_APP_KEY=your-pusher-key
PUSHER_APP_SECRET=your-pusher-secret
PUSHER_APP_CLUSTER=mt1
```

## 📊 Features

### Dashboard Utama
- Real-time monitoring semua sensor
- Grafik historis data sensor
- Alert system untuk kondisi abnormal
- Status overview semua perangkat

### Sensor Management
- Konfigurasi sensor baru
- Kalibrasi sensor
- Maintenance scheduling
- Data logging

### User Management
- Multi-level user access
- Team collaboration
- Activity logging
- Permission management

### Reporting
- Export data ke Excel/PDF
- Scheduled reports
- Custom date range
- Automated alerts

## 🔄 Development

### Running Tests
```bash
# PHPUnit
php artisan test

# Pest
./vendor/bin/pest
```

### Code Style
```bash
# Laravel Pint
./vendor/bin/pint
```

### Asset Development
```bash
# Watch for changes
npm run dev

# Build for production
npm run build
```

## 📱 API Documentation

API endpoints tersedia di `/api/` dengan dokumentasi lengkap:

- `GET /api/sensors` - List semua sensor
- `GET /api/sensors/{id}/data` - Data sensor specific
- `POST /api/alerts` - Create alert baru
- `GET /api/dashboard/summary` - Dashboard summary

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

Project ini menggunakan [MIT License](LICENSE).

## 👥 Team

- **Developer**: [@anggahere1112](https://github.com/anggahere1112)
- **Email**: angga1201putra@gmail.com

## 🆘 Support

Jika mengalami masalah atau memiliki pertanyaan:

1. Check [Issues](https://github.com/anggahere1112/EMS-Dashboard/issues)
2. Create new issue jika belum ada
3. Contact developer via email

---

**EMS Dashboard** - Monitoring lingkungan yang cerdas dan real-time 🌟
