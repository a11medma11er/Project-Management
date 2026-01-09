# Deployment Guide

## Table of Contents
1. [System Requirements](#system-requirements)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [Environment Setup](#environment-setup)
4. [Database Configuration](#database-configuration)
5. [Web Server Configuration](#web-server-configuration)
6. [Application Deployment](#application-deployment)
7. [Security Configuration](#security-configuration)
8. [Performance Optimization](#performance-optimization)
9. [Monitoring and Logging](#monitoring-and-logging)
10. [Backup Strategy](#backup-strategy)
11. [Troubleshooting](#troubleshooting)

---

## System Requirements

### Minimum Requirements

**Server Specifications**:
- CPU: 2 cores
- RAM: 4GB
- Storage: 20GB SSD
- Operating System: Ubuntu 20.04+ / CentOS 8+ / Debian 11+

**Software Requirements**:
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Nginx 1.18+ or Apache 2.4+
- Composer 2.x
- Node.js 18.x or higher
- npm 9.x or higher
- Git 2.x

### Recommended Production Specifications

**Server Specifications**:
- CPU: 4 cores
- RAM: 8GB
- Storage: 50GB SSD
- Operating System: Ubuntu 22.04 LTS

**Additional Components**:
- Redis 6.x or higher (for caching and sessions)
- Supervisor (for queue workers)
- SSL Certificate (Let's Encrypt)
- CDN (optional, for static assets)

### Required PHP Extensions

```bash
php -m | grep -E 'openssl|pdo|mbstring|tokenizer|xml|ctype|json|bcmath|fileinfo|gd'
```

Required extensions:
- OpenSSL
- PDO
- PDO MySQL
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD or Imagick
- Zip

---

## Pre-Deployment Checklist

### Code Preparation

- [ ] All tests passing
- [ ] Code formatted with Laravel Pint
- [ ] Environment variables documented
- [ ] Database migrations tested
- [ ] Seeders prepared
- [ ] Assets built for production
- [ ] Dependencies updated

### Infrastructure Preparation

- [ ] Server provisioned
- [ ] Domain name configured
- [ ] DNS records updated
- [ ] SSL certificate obtained
- [ ] Database server ready
- [ ] Backup solution configured
- [ ] Monitoring tools set up

### Security Preparation

- [ ] Firewall configured
- [ ] SSH key authentication enabled
- [ ] Database user with minimum privileges
- [ ] Application secrets generated
- [ ] Rate limiting configured
- [ ] CSRF protection enabled

---

## Environment Setup

### 1. Server Setup (Ubuntu 22.04)

#### Update System

```bash
sudo apt update
sudo apt upgrade -y
```

#### Install PHP 8.2

```bash
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring \
  php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl php8.2-redis -y
```

#### Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

#### Install Node.js and npm

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
node --version
npm --version
```

#### Install MySQL 8.0

```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

#### Install Redis (Optional but Recommended)

```bash
sudo apt install redis-server -y
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

#### Install Nginx

```bash
sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx
```

#### Install Supervisor (for Queue Workers)

```bash
sudo apt install supervisor -y
sudo systemctl enable supervisor
```

#### Install Git

```bash
sudo apt install git -y
```

---

## Database Configuration

### 1. Create Database and User

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE project_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'pmadmin'@'localhost' IDENTIFIED BY 'your_secure_password';

GRANT ALL PRIVILEGES ON project_management.* TO 'pmadmin'@'localhost';

FLUSH PRIVILEGES;

EXIT;
```

### 2. Verify Connection

```bash
mysql -u pmadmin -p project_management
```

### 3. Configure MySQL for Production

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
[mysqld]
# Performance settings
max_connections = 200
innodb_buffer_pool_size = 2G
innodb_log_file_size = 512M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Query cache (MySQL 5.7 only)
# query_cache_type = 1
# query_cache_size = 128M

# Slow query log
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2

# Character set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
```

Restart MySQL:
```bash
sudo systemctl restart mysql
```

---

## Web Server Configuration

### Nginx Configuration

Create site configuration:

```bash
sudo nano /etc/nginx/sites-available/project-management
```

Add configuration:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    root /var/www/project-management/public;
    index index.php index.html;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # Logging
    access_log /var/log/nginx/project-management-access.log;
    error_log /var/log/nginx/project-management-error.log;
    
    # Client body size (for file uploads)
    client_max_body_size 64M;
    
    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        
        # Increase timeout for long-running requests
        fastcgi_read_timeout 300;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/project-management /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### PHP-FPM Configuration

Edit `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
[www]
user = www-data
group = www-data

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

php_admin_value[upload_max_filesize] = 64M
php_admin_value[post_max_size] = 64M
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 300
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
```

---

## Application Deployment

### 1. Clone Repository

```bash
cd /var/www
sudo git clone https://github.com/a11medma11er/Project-Management.git project-management
cd project-management
```

### 2. Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/project-management
sudo chmod -R 755 /var/www/project-management
sudo chmod -R 775 /var/www/project-management/storage
sudo chmod -R 775 /var/www/project-management/bootstrap/cache
```

### 3. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 4. Environment Configuration

```bash
cp .env.example .env
nano .env
```

Configure production environment:

```env
APP_NAME="Project Management AI"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=pmadmin
DB_PASSWORD=your_secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# AI Configuration
AI_SYSTEM_ENABLED=true
AI_DEFAULT_PROVIDER=local
AI_MIN_CONFIDENCE=0.7
AI_CACHE_TTL=3600

# Optional: External AI Providers
OPENAI_API_KEY=
OPENAI_MODEL=gpt-4
CLAUDE_API_KEY=
CLAUDE_MODEL=claude-3-sonnet-20240229

# Optional: Slack Integration
SLACK_WEBHOOK_URL=
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Database Migrations

```bash
php artisan migrate --force
```

### 7. Seed Database

```bash
php artisan db:seed --force
php artisan db:seed --class=AIPermissionsSeeder --force
```

### 8. Create Storage Link

```bash
php artisan storage:link
```

### 9. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## Security Configuration

### 1. Firewall Configuration (UFW)

```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
sudo ufw status
```

### 2. Fail2Ban (Protection Against Brute Force)

```bash
sudo apt install fail2ban -y
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo nano /etc/fail2ban/jail.local
```

Add configuration:

```ini
[nginx-http-auth]
enabled = true
port = http,https
filter = nginx-http-auth
logpath = /var/log/nginx/project-management-error.log
maxretry = 3
bantime = 3600

[nginx-limit-req]
enabled = true
port = http,https
filter = nginx-limit-req
logpath = /var/log/nginx/project-management-error.log
maxretry = 10
findtime = 60
bantime = 3600
```

Restart Fail2Ban:

```bash
sudo systemctl restart fail2ban
```

### 3. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

Auto-renewal:

```bash
sudo certbot renew --dry-run
```

### 4. Application Security

Edit `.env`:

```env
# Disable debug in production
APP_DEBUG=false

# Secure session
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# HTTPS enforcement
APP_URL=https://yourdomain.com
```

### 5. File Permissions Hardening

```bash
# Remove write permissions from www-data on config files
sudo chmod 640 /var/www/project-management/.env
sudo chown root:www-data /var/www/project-management/.env

# Protect sensitive directories
sudo chmod 750 /var/www/project-management/storage
sudo chmod 750 /var/www/project-management/bootstrap/cache
```

---

## Performance Optimization

### 1. PHP OpCache Configuration

Edit `/etc/php/8.2/fpm/php.ini`:

```ini
[opcache]
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
opcache.enable_cli=1
```

### 2. Redis Configuration

Edit `/etc/redis/redis.conf`:

```conf
maxmemory 512mb
maxmemory-policy allkeys-lru
save ""
```

### 3. Queue Workers with Supervisor

Create supervisor configuration:

```bash
sudo nano /etc/supervisor/conf.d/project-management-worker.conf
```

Add configuration:

```ini
[program:project-management-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/project-management/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/project-management/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start project-management-worker:*
```

### 4. Laravel Horizon (Alternative to Supervisor)

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

Configure Horizon in supervisor:

```ini
[program:horizon]
process_name=%(program_name)s
command=php /var/www/project-management/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/project-management/storage/logs/horizon.log
stopwaitsecs=3600
```

### 5. Database Query Caching

In `.env`:

```env
AI_ENABLE_QUERY_CACHE=true
AI_CACHE_TTL=3600
```

---

## Monitoring and Logging

### 1. Application Logging

Laravel logs are in `storage/logs/laravel.log`

View real-time logs:

```bash
tail -f /var/www/project-management/storage/logs/laravel.log
```

### 2. Log Rotation

Create logrotate configuration:

```bash
sudo nano /etc/logrotate.d/project-management
```

```
/var/www/project-management/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    missingok
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### 3. Nginx Logging

Logs location:
- Access: `/var/log/nginx/project-management-access.log`
- Error: `/var/log/nginx/project-management-error.log`

### 4. MySQL Slow Query Log

```bash
sudo tail -f /var/log/mysql/slow-query.log
```

### 5. Application Monitoring (Recommended)

Install monitoring tools:
- **New Relic**: Application performance monitoring
- **Sentry**: Error tracking
- **Laravel Telescope**: Development debugging (disable in production)

For Sentry:

```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=your_sentry_dsn
```

---

## Backup Strategy

### 1. Database Backup

Create backup script:

```bash
sudo nano /usr/local/bin/backup-db.sh
```

```bash
#!/bin/bash

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/var/backups/mysql"
DB_NAME="project_management"
DB_USER="pmadmin"
DB_PASS="your_secure_password"

mkdir -p $BACKUP_DIR

mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$TIMESTAMP.sql.gz

# Delete backups older than 30 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete
```

Make executable:

```bash
sudo chmod +x /usr/local/bin/backup-db.sh
```

Schedule with cron:

```bash
sudo crontab -e
```

Add daily backup at 2 AM:

```
0 2 * * * /usr/local/bin/backup-db.sh >> /var/log/backup.log 2>&1
```

### 2. Application Files Backup

```bash
sudo nano /usr/local/bin/backup-files.sh
```

```bash
#!/bin/bash

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/var/backups/application"
APP_DIR="/var/www/project-management"

mkdir -p $BACKUP_DIR

tar -czf $BACKUP_DIR/app_backup_$TIMESTAMP.tar.gz \
  --exclude='$APP_DIR/vendor' \
  --exclude='$APP_DIR/node_modules' \
  --exclude='$APP_DIR/storage/logs' \
  $APP_DIR

# Delete backups older than 7 days
find $BACKUP_DIR -name "app_backup_*.tar.gz" -mtime +7 -delete
```

Make executable and schedule:

```bash
sudo chmod +x /usr/local/bin/backup-files.sh
```

Add to cron (weekly):

```
0 3 * * 0 /usr/local/bin/backup-files.sh >> /var/log/backup.log 2>&1
```

### 3. Remote Backup (Recommended)

Use rsync to copy backups to remote server:

```bash
rsync -avz /var/backups/ user@remote-server:/backups/project-management/
```

Or use cloud storage (AWS S3, Google Cloud Storage, etc.):

```bash
# Install AWS CLI
sudo apt install awscli

# Upload to S3
aws s3 sync /var/backups/ s3://your-bucket/project-management-backups/
```

---

## Troubleshooting

### Common Issues and Solutions

#### 1. Permission Errors

```bash
sudo chown -R www-data:www-data /var/www/project-management
sudo chmod -R 755 /var/www/project-management
sudo chmod -R 775 /var/www/project-management/storage
sudo chmod -R 775 /var/www/project-management/bootstrap/cache
```

#### 2. 500 Internal Server Error

Check logs:

```bash
tail -f /var/www/project-management/storage/logs/laravel.log
tail -f /var/log/nginx/project-management-error.log
```

Common causes:
- `.env` file missing or misconfigured
- APP_KEY not generated
- Storage permissions incorrect
- Database connection failed

#### 3. Database Connection Errors

Test connection:

```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

Check MySQL is running:

```bash
sudo systemctl status mysql
```

#### 4. Queue Not Processing

Check worker status:

```bash
sudo supervisorctl status
```

Restart workers:

```bash
sudo supervisorctl restart project-management-worker:*
```

Check queue jobs:

```bash
php artisan queue:work --once
```

#### 5. Cache Issues

Clear all caches:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
redis-cli FLUSHALL
```

#### 6. File Upload Issues

Check PHP settings:

```bash
php -i | grep -E 'upload_max_filesize|post_max_size|memory_limit'
```

Check Nginx settings:

```bash
grep client_max_body_size /etc/nginx/sites-available/project-management
```

#### 7. SSL Certificate Issues

Renew certificate manually:

```bash
sudo certbot renew --force-renewal
sudo systemctl reload nginx
```

Check certificate expiry:

```bash
sudo certbot certificates
```

---

## Zero-Downtime Deployment

### Using Deployer

Install Deployer:

```bash
composer require deployer/deployer --dev
```

Create `deploy.php`:

```php
<?php
namespace Deployer;

require 'recipe/laravel.php';

set('application', 'Project Management AI');
set('repository', 'git@github.com:a11medma11er/Project-Management.git');
set('keep_releases', 5);

host('production')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '/var/www/project-management')
    ->set('branch', 'main');

task('artisan:optimize', function () {
    run('{{bin/php}} {{release_or_current_path}}/artisan config:cache');
    run('{{bin/php}} {{release_or_current_path}}/artisan route:cache');
    run('{{bin/php}} {{release_or_current_path}}/artisan view:cache');
    run('{{bin/php}} {{release_or_current_path}}/artisan optimize');
});

after('deploy:vendors', 'artisan:optimize');
after('deploy:failed', 'deploy:unlock');
```

Deploy:

```bash
vendor/bin/dep deploy production
```

---

## Health Checks

Create health check endpoint:

```bash
php artisan make:controller HealthController
```

```php
public function check()
{
    $health = [
        'status' => 'healthy',
        'database' => DB::connection()->getDatabaseName() ? 'connected' : 'disconnected',
        'cache' => Cache::get('health_check') ? 'working' : 'not working',
        'queue' => Queue::size() !== null ? 'working' : 'not working',
    ];
    
    return response()->json($health);
}
```

Add route in `web.php`:

```php
Route::get('/health', [HealthController::class, 'check']);
```

Monitor with external service (UptimeRobot, Pingdom, etc.)

---

## Conclusion

This deployment guide provides comprehensive steps for deploying the AI-Powered Project Management System to a production environment with:
- Secure configuration
- Optimized performance
- Automated backups
- Monitoring and logging
- Zero-downtime deployment capability

Always test deployment procedures in a staging environment before applying to production.

For additional support, refer to:
- Laravel Documentation: https://laravel.com/docs
- Server Administration Best Practices
- Security Hardening Guides
