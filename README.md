# Daar Al Quran - Islamic Education Management System 🕌

A comprehensive Laravel-based application for managing Islamic educational institutions, focusing on Quran memorization tracking, classroom management, and student-teacher communication.

## ✨ Features

### 📚 **Student Management**
- Student registration and profile management
- Attendance tracking with detailed reports
- Quran memorization progress tracking (581 pages + 37 surahs)
- Personal messaging system
- Dashboard with performance insights

### 👨‍🏫 **Teacher Tools**
- Classroom and session management
- Student attendance marking
- Memorization progress evaluation
- Communication tools (messaging, notes)
- Performance analytics and reporting

### 👨‍💼 **Administrative Features**
- Multi-school support
- User role management (Admin, Teacher, Student)
- School-wide reporting and analytics
- System configuration and settings

### 🎯 **Core Functionality**
- **Memorization Tracking**: Track 618 total items (581 Quran pages + 37 surahs 78-114)
- **Attendance System**: Comprehensive attendance tracking with reports
- **Messaging Platform**: Internal communication between teachers and students
- **Responsive Design**: Modern, RTL-friendly interface for Arabic content
- **Real-time Notifications**: Dynamic badge system for unread messages

## 🚀 Quick Deployment

### For Namecheap Shared Hosting

**Windows Users:**
```cmd
.\deploy-namecheap.bat
```

**Mac/Linux Users:**
```bash
./deploy-namecheap.sh
```

This creates a `daar-al-quran-namecheap.zip` file ready for upload to your hosting account.

📖 **Detailed Guides:**
- [📋 Quick Start Deployment](QUICK_START_DEPLOYMENT.md) - Get up and running in minutes
- [🏗️ Namecheap Hosting Guide](NAMECHEAP_DEPLOYMENT.md) - Complete shared hosting setup
- [🚀 Production Deployment](PRODUCTION_DEPLOYMENT.md) - VPS/dedicated server setup

## 🛠️ Local Development Setup

### Prerequisites
- PHP 8.0+ with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- MySQL 5.7+ or MariaDB 10.2+
- Composer 2.0+
- Node.js 14+ and npm (for asset compilation)

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/homam49/daar-al-quran.git
   cd daar-al-quran/daar-al-quran
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install && npm run dev
   ```

3. **Environment setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration:**
   ```bash
   # Configure your database settings in .env
   php artisan migrate
   php artisan db:seed
   ```

5. **Start development server:**
   ```bash
   php artisan serve
   ```

## 📁 Project Structure

```
daar-al-quran/
├── app/                    # Laravel application core
│   ├── Http/Controllers/   # Request controllers
│   ├── Models/            # Eloquent models
│   ├── Services/          # Business logic services
│   └── Policies/          # Authorization policies
├── resources/
│   ├── views/             # Blade templates (RTL-friendly)
│   └── css/               # Stylesheets
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── routes/                # Application routes
└── public/                # Web-accessible files
```

## 🔐 Default Credentials

The application comes with seeded accounts for testing:

**Admin:**
- Email: admin@daaralquran.com
- Password: admin123

**Teacher:**
- Email: teacher@daaralquran.com  
- Password: teacher123

**Student:**
- Username: STUD01
- Password: STUD01

## 🌟 Key Technologies

- **Backend:** Laravel 8+ with PHP 8.0+
- **Frontend:** Bootstrap 5 with RTL support
- **Database:** MySQL with optimized indexing
- **Authentication:** Multi-guard system (Admin, Teacher, Student)
- **UI/UX:** Responsive design with Arabic/RTL interface
- **Icons:** Font Awesome 6
- **Charts:** Chart.js for analytics (planned)

## 📊 Memorization System

The system tracks Quran memorization with precision:

- **581 Pages**: Individual Quran pages (1-581)
- **37 Surahs**: Last chapters (Surahs 78-114)
- **Total**: 618 trackable memorization items
- **Status Tracking**: Not started, In progress, Memorized
- **Progress Analytics**: Visual progress tracking and reports

## 🎯 User Roles & Permissions

### 👨‍💼 **Admin**
- Full system access and configuration
- Multi-school management
- User account management
- System-wide analytics and reporting

### 👨‍🏫 **Teacher**
- Classroom and student management
- Attendance and memorization tracking
- Communication with students
- Performance reporting

### 👨‍🎓 **Student**  
- Personal dashboard and progress tracking
- Attendance records viewing
- Messaging with teachers
- Memorization progress monitoring

## 🚀 Deployment Options

### Shared Hosting (Namecheap, etc.)
Use our automated deployment scripts:
- `deploy-namecheap.bat` (Windows)
- `deploy-namecheap.sh` (Mac/Linux)

### VPS/Dedicated Servers
Follow the [Production Deployment Guide](PRODUCTION_DEPLOYMENT.md)

### Development
Standard Laravel development setup with `php artisan serve`

## 🔧 Configuration

### Environment Variables
Key settings in `.env`:
```env
APP_NAME="Daar Al Quran"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: Check our deployment guides for detailed instructions
- **Issues**: Report bugs and request features via GitHub Issues
- **Hosting Support**: Contact your hosting provider for server-specific issues

## 🙏 Acknowledgments

- Built with Laravel framework
- Bootstrap for responsive UI
- Font Awesome for icons
- Designed for Islamic educational institutions

---

Made with ❤️ for the Islamic education community 