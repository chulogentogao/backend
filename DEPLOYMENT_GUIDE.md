# Laravel Backend Deployment Guide

## 🚨 Fix Cache Path Error (HTTP 500)

If you're seeing `InvalidArgumentException: Please provide a valid cache path`, follow these steps:

### Quick Fix (SSH into your server)

```bash
# Navigate to your Laravel root directory
cd /var/www/html

# Create all required cache directories
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Set proper permissions (replace www-data with your web server user)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Clear and rebuild cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
```

---

## 📋 Complete Deployment Checklist

### 1. **Environment Setup**

```bash
# Copy environment file
cp .env.example .env

# Edit .env with your production settings
nano .env
```

**Required .env variables:**
```env
APP_NAME="Inventory System"
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
SESSION_DOMAIN=.yourdomain.com
FRONTEND_URL=https://yourfrontend.com
```

### 2. **Install Dependencies**

```bash
composer install --optimize-autoloader --no-dev
```

### 3. **Generate Application Key**

```bash
php artisan key:generate
```

### 4. **Create Required Directories**

```bash
# Storage directories
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs

# Bootstrap cache
mkdir -p bootstrap/cache
```

### 5. **Set Permissions**

```bash
# For Apache/Nginx (www-data)
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# For shared hosting
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 6. **Database Setup**

```bash
# Run migrations
php artisan migrate --force

# Seed database (creates admin user)
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=AdminSeeder
```

### 7. **Optimize for Production**

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 8. **Create Storage Link**

```bash
php artisan storage:link
```

---

## 🔧 Server Configuration

### Apache (.htaccess)

Make sure your `.htaccess` in the `public` directory contains:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Nginx

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/html/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🐛 Troubleshooting

### Error: "Please provide a valid cache path"

**Solution:**
```bash
mkdir -p storage/framework/cache/data
chmod -R 775 storage/framework/cache
php artisan config:clear
```

### Error: "The stream or file could not be opened"

**Solution:**
```bash
chmod -R 775 storage/logs
chown -R www-data:www-data storage
```

### Error: "No application encryption key has been specified"

**Solution:**
```bash
php artisan key:generate
php artisan config:cache
```

### Error: "SQLSTATE[HY000] [1045] Access denied"

**Solution:**
- Check your `.env` database credentials
- Ensure database exists
- Verify user has proper permissions

### CORS Issues

**Solution:**
Update `config/cors.php`:
```php
'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
'supports_credentials' => true,
```

---

## 📦 Git Deployment Workflow

### Initial Deployment

```bash
# Clone repository
git clone https://github.com/yourusername/your-repo.git /var/www/html
cd /var/www/html

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup environment
cp .env.example .env
nano .env

# Generate key
php artisan key:generate

# Create directories and set permissions
mkdir -p storage/framework/{cache/data,sessions,testing,views}
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Database setup
php artisan migrate --force
php artisan db:seed --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Updates/Redeployment

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and rebuild cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services (if needed)
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## 🔐 Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Set `APP_ENV=production`
- [ ] Use strong `APP_KEY`
- [ ] Secure database credentials
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Enable HTTPS/SSL
- [ ] Configure CORS properly
- [ ] Set up firewall rules
- [ ] Regular backups
- [ ] Keep Laravel and dependencies updated

---

## 📊 Performance Optimization

```bash
# Enable OPcache (php.ini)
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60

# Queue workers (for background jobs)
php artisan queue:work --daemon

# Use Redis for cache (optional)
# Update .env:
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## 📝 Admin Account

After seeding, you can login with:
- **Email:** `admin@gmail.com`
- **Password:** `admin123`

**⚠️ Change this password immediately in production!**

---

## 🆘 Need Help?

If you continue to have issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server error logs
3. Verify PHP version (requires PHP 8.1+)
4. Ensure all PHP extensions are installed:
   - BCMath
   - Ctype
   - Fileinfo
   - JSON
   - Mbstring
   - OpenSSL
   - PDO
   - Tokenizer
   - XML

---

## 🎯 Quick Commands Reference

```bash
# Clear all cache
php artisan optimize:clear

# Rebuild all cache
php artisan optimize

# Check routes
php artisan route:list

# Check config
php artisan config:show

# Run migrations
php artisan migrate --force

# Rollback migrations
php artisan migrate:rollback

# Fresh database
php artisan migrate:fresh --seed --force
```
