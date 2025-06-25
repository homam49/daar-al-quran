# Quick Start Deployment Guide

This guide will help you quickly deploy the Daar Al Quran application to Namecheap shared hosting.

## 🚀 One-Command Deployment Preparation

### Step 1: Prepare Deployment Files

Run the deployment script to create Namecheap-ready files:

**On Windows (Git Bash or WSL):**
```bash
bash deploy-namecheap.sh
```

**On macOS/Linux:**
```bash
./deploy-namecheap.sh
```

This will create:
- `namecheap-deployment/app/` - Laravel application files
- `namecheap-deployment/public_html/` - Web-accessible files
- `daar-al-quran-namecheap.zip` - ZIP file for easy upload

## 📤 Upload to Namecheap

### Option 1: Upload ZIP File (Easiest)
1. Login to Namecheap cPanel
2. Go to **File Manager**
3. Upload `daar-al-quran-namecheap.zip` to your account root
4. Extract the ZIP file
5. Move contents:
   - Move `app/` folder to your account root (outside public_html)
   - Move `public_html/` contents to your domain's public_html folder

### Option 2: Upload Folders Separately
1. Upload `namecheap-deployment/app/` → `/app/` on your hosting
2. Upload `namecheap-deployment/public_html/` → `/public_html/` on your hosting

## ⚙️ Configure Your Application

### 1. Database Setup
1. In cPanel → **MySQL Databases**
2. Create database: `username_daar_al_quran`
3. Create database user and assign to database
4. Note the credentials

### 2. Environment Configuration
1. In File Manager, navigate to `/app/`
2. Rename `.env.production` to `.env`
3. Edit `.env` file:

```env
APP_NAME="Daar Al Quran"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_daar_al_quran
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Daar Al Quran"
```

### 3. Install Dependencies (If SSH Available)

```bash
cd app
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Set File Permissions

In File Manager or SSH:
- Set `app/storage/` permissions to 755
- Set `app/bootstrap/cache/` permissions to 755

## 🧪 Test Your Website

1. Visit your domain: `https://yourdomain.com`
2. You should see the Daar Al Quran homepage
3. Test admin login: `/admin/login`
4. Test teacher login: `/teacher/login`
5. Test student login: `/student/login`

## 🔧 Future Updates

When you make changes to your code:

1. **Push to GitHub:**
   ```bash
   git add .
   git commit -m "Your changes description"
   git push origin main
   ```

2. **Deploy Updates:**
   ```bash
   # Pull latest changes
   git pull origin main
   
   # Prepare new deployment
   bash deploy-namecheap.sh
   
   # Upload only changed files to your hosting
   ```

## 📋 Checklist

- [ ] Deployment script run successfully
- [ ] Files uploaded to Namecheap
- [ ] Database created and configured
- [ ] .env file configured with correct settings
- [ ] Dependencies installed (composer install)
- [ ] Laravel commands executed (key:generate, migrate, cache)
- [ ] File permissions set correctly
- [ ] Website accessible and functional
- [ ] Admin, teacher, and student logins working
- [ ] SSL certificate installed

## 🆘 Troubleshooting

### Common Issues:

**500 Internal Server Error:**
- Check `.env` configuration
- Verify file permissions (755 for storage and bootstrap/cache)
- Check error logs in cPanel

**Database Connection Error:**
- Verify database credentials in `.env`
- Ensure database user has proper permissions

**Missing CSS/JS:**
- Ensure all files from `public_html/` are in your domain's public_html
- Clear browser cache

**CSRF Token Mismatch:**
- Verify `APP_URL` in `.env` matches your domain exactly
- Run `php artisan config:clear` if you have SSH access

## 📞 Support

- **Namecheap Support:** For hosting-specific issues
- **Laravel Documentation:** For framework questions
- **GitHub Issues:** For application-specific problems

---

🎉 **Congratulations!** Your Daar Al Quran application should now be live on Namecheap hosting! 