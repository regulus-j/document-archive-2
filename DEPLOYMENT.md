# DocTrack Deployment Guide

## Overview

DocTrack is a Laravel 11 document tracking and archiving system. This guide covers deployment on Ubuntu with Dokploy, as well as development setup on Windows with XAMPP.

## Table of Contents

- [Production Deployment (Ubuntu + Dokploy)](#production-deployment-ubuntu--dokploy)
- [Development Setup (Windows + XAMPP)](#development-setup-windows--xampp)
- [Key Services & Scheduler](#key-services--scheduler)
- [Troubleshooting](#troubleshooting)

---

## Production Deployment (Ubuntu + Dokploy)

### Prerequisites

- Ubuntu 20.04 or later
- Docker and Docker Compose installed
- Dokploy installed and configured
- Domain name and SSL certificate

### Deployment Steps

#### 1. Prepare the Environment

Clone the repository and configure environment variables:

```bash
git clone <repository-url> document_archive
cd document_archive
cp .env.example .env
# Edit .env with your production settings:
# - APP_KEY (generate with: php artisan key:generate)
# - DB_HOST, DB_PASSWORD, DB_NAME (production database)
# - MAIL_MAILER, MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD
# - APP_URL and APP_DEBUG settings
```

#### 2. Build and Deploy with Docker

The application uses a multi-stage Docker build:

```bash
# Option A: Using docker-compose
docker-compose -f docker-compose.yml up -d

# Option B: Using Dokploy CLI
dokploy deploy
```

The Docker setup automatically:
- Builds frontend assets (Node.js/Vite)
- Configures PHP-FPM, Nginx, and Supervisor
- Sets proper file permissions for storage/logs

#### 3. Critical: Enable the Laravel Scheduler

The **Laravel Scheduler is essential** for overdue document notifications and reminder alerts.

The scheduler is managed by **Supervisor** (`docker/supervisord.conf`):

- **Service**: `laravel-scheduler`
- **Command**: `php artisan schedule:run` executed every 60 seconds
- **Log**: `/var/log/supervisor/laravel-scheduler.log`

**The scheduler automatically runs in Docker containers.** Verify it's active:

```bash
# Inside the container
supervisor -c /etc/supervisor/supervisord.conf

# Check status
supervisorctl status laravel-scheduler
# Expected output: laravel-scheduler         RUNNING   pid 123, uptime 0:05:42
```

#### 4. Run Database Migrations

```bash
docker-compose exec php php artisan migrate --force
```

**CRITICAL**: The migration `2026_03_16_132138_ensure_permissions_exist` automatically creates 19 required permissions. These permissions are **essential** for user registration to work. Without them, registration will fail with a 500 error when the system tries to create company-specific roles.

The system now includes automatic safeguards:
- Missing permissions are auto-created during registration
- Role creation failures are logged but don't break registration
- See `DEPLOYMENT_CHECKLIST.md` for detailed verification steps

#### 5. Create Storage Symlink

```bash
docker-compose exec php php artisan storage:link
```

#### 6. Configure Mail

Update `.env` with your mail server credentials:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com          # or your mail provider
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-app-password   # Use app password, not regular password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="DocTrack"
```

**For Gmail:**
1. Enable 2-factor authentication
2. Generate an app password: https://myaccount.google.com/apppasswords
3. Use the app password in `MAIL_PASSWORD`

#### 7. Verify Notifications Are Working

Check the scheduler is running:

```bash
# View supervisor logs
docker-compose logs supervisor

# Check scheduler-specific logs
docker-compose exec php tail -f /var/log/supervisor/laravel-scheduler.log
```

Check Laravel logs for notification activities:

```bash
docker-compose exec php tail -f storage/logs/laravel.log | grep -i "urgency\|notification"
```

Verify database records:

```bash
docker-compose exec php php artisan tinker
# Inside tinker:
> DB::table('document_urgency_notifications')->count()
# Should show notification records being created
```

---

## Development Setup (Windows + XAMPP)

### Prerequisites

- XAMPP 2 (PHP 8.1+, MySQL 5.7+)
- Node.js 18+
- Git

### Setup Steps

#### 1. Clone and Configure

```bash
git clone <repository-url> C:\xampp2\htdocs\document_archive
cd C:\xampp2\htdocs\document_archive
cp .env.example .env
```

#### 2. Install Dependencies

```bash
composer install
npm install
```

#### 3. Generate Application Key

```bash
php artisan key:generate
```

#### 4. Run Migrations

```bash
php artisan migrate
```

#### 5. Build Frontend Assets

```bash
npm run build        # Production build
# OR
npm run dev         # Development with watch mode
```

#### 6. Create Storage Symlink

```bash
php artisan storage:link
```

#### 7. Manual Testing of the Scheduler (Windows Development)

Since Windows doesn't require the scheduler to run continuously during development, test commands manually:

```bash
# Test the monitoring command
php artisan documents:monitor-urgency

# Test notifications with fast-forward (triggers alerts immediately)
php artisan notifications:fast-forward --type=escalation

# View in-app notifications
php artisan tinker
> DB::table('notifications')->latest()->first()
```

Check logs:

```bash
# View latest log entries
tail -f storage/logs/laravel.log

# Or on Windows:
Get-Content storage/logs/laravel.log -Tail 50
```

---

## Key Services & Scheduler

### Document Urgency Monitoring

**Location**: [app/Console/Commands/MonitorDocumentUrgency.php](app/Console/Commands/MonitorDocumentUrgency.php)

**Scheduled**: Every 30 minutes via Laravel Scheduler

**Functionality**:
- Scans all active workflows (`pending`, `waiting`, `received` status)
- Detects overdue documents based on urgency thresholds
- Creates three types of alerts:
  1. **WARNING** - Approaching deadline
  2. **ESCALATION** - Deadline exceeded (sends email to recipient and sender)
  3. **INACTIVITY** - No activity for threshold hours

**Notification Flow**:
1. Command runs every 30 minutes (controlled by `$schedule` in [AppServiceProvider.php](app/Providers/AppServiceProvider.php#L43))
2. Creates records in `document_urgency_notifications` table
3. Creates in-app notifications in `notifications` table
4. Sends emails to affected users (for escalations)

### In-App Notification Polling

**Location**: [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php#L132-L171)

**Frequency**: Every 60 seconds

**Endpoint**: `GET /notifications/unread-count` → returns JSON with unread notification count

**Display**: Header badge updates with unread count (shows "99+" if >99)

---

## Troubleshooting

### Notifications Not Appearing

**Check 1: Is the scheduler running?**

```bash
# Production (Docker)
docker-compose logs supervisor | grep laravel-scheduler

# Development (Windows)
# Manually run: php artisan documents:monitor-urgency
```

**Check 2: Are alerts being created in the database?**

```bash
php artisan tinker
> DB::table('document_urgency_notifications')->count()
> DB::table('notifications')->latest()->first()
```

**Check 3: Are emails being sent?**

```bash
# Check logs for email entries
cat storage/logs/laravel.log | grep -i "Message-ID\|email"

# Or test with fast-forward command
php artisan notifications:fast-forward --type=escalation
```

**Check 4: Verify mail configuration**

```bash
php artisan tinker
> config('mail.default')   # Should be 'smtp' or 'log' (log for development)
> config('mail.mailers.smtp')  # Check SMTP settings
```

### Scheduler Not Running (Production)

**Symptoms**: Notifications never trigger, logs show no `documents:monitor-urgency` execution

**Solutions**:

1. **Check supervisor status**
   ```bash
   docker-compose exec php supervisorctl status laravel-scheduler
   ```
   If not running, restart:
   ```bash
   docker-compose restart supervisor
   ```

2. **Verify supervisor configuration**
   ```bash
   docker-compose exec php cat /etc/supervisor/conf.d/supervisord.conf
   ```
   Ensure `[program:laravel-scheduler]` section exists (added in [docker/supervisord.conf](docker/supervisord.conf))

3. **Check logs**
   ```bash
   docker-compose exec php tail -f /var/log/supervisor/laravel-scheduler.log
   docker-compose exec php tail -f /var/log/supervisor/supervisord.log
   ```

### Email Not Sending

**Check mail configuration in `.env`**:

```bash
MAIL_MAILER=smtp        # Must be 'smtp', not 'log' for production
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=app-password-not-regular-password
```

**Test with**:

```bash
php artisan notifications:fast-forward --type=escalation
# Watch laravel.log for delivery status
```

**Common Issues**:
- Using regular Google password instead of app password
- SMTP credentials wrong
- MAIL_FROM_ADDRESS not set or invalid
- Firewall blocking SMTP port 587

### Registration Returning 500 Errors

**Symptoms**: New users cannot register, receiving 500 internal server error

**Root Cause**: Missing permissions in the database prevent role creation during company account setup

**Solutions** (in order of preference):

1. **Run migrations** (includes automatic permission seeding):
   ```bash
   php artisan migrate
   ```

2. **Verify permissions exist**:
   ```bash
   php artisan tinker
   >>> \Spatie\Permission\Models\Permission::count()
   # Should return 19
   >>> exit
   ```

3. **Manual permission seeding** (if migrations already run):
   ```bash
   php artisan db:seed --class=PermissionTableSeeder
   ```

**Prevention**: The system now auto-creates missing permissions during registration. However, running migrations ensures optimal performance by creating all permissions upfront.

**Logs to check**:
```bash
grep "Failed to create default role" storage/logs/laravel.log
grep "Failed to create permission" storage/logs/laravel.log
```

See `DEPLOYMENT_CHECKLIST.md` for detailed verification steps.

---

## Environment Variables Reference

```env
# Application
APP_NAME="DocTrack"
APP_ENV=production          # 'local' for development, 'production' for prod
APP_KEY=base64:xxxxx        # Generate with: php artisan key:generate
APP_DEBUG=false             # Never true in production
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=database-host
DB_PORT=3306
DB_DATABASE=document_archive
DB_USERNAME=root
DB_PASSWORD=your-secure-password

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="DocTrack - Document Archive"

# Cache & Sessions
CACHE_DRIVER=redis          # or 'file'
SESSION_DRIVER=cookie       # or 'redis'
QUEUE_CONNECTION=database   # or 'redis'

# Ollama (for AI urgency analysis)
OLLAMA_BASE_URL=http://ollama:11434
OLLAMA_MODEL=mistral        # or other available model
```

---

## Additional Resources

- [AppServiceProvider.php](app/Providers/AppServiceProvider.php) - Scheduler registration
- [DocumentUrgencyAnalyzer.php](app/Services/DocumentUrgencyAnalyzer.php) - Urgency calculation logic
- [DocumentUrgencyAlert.php](app/Mail/DocumentUrgencyAlert.php) - Email template
- [Laravel Scheduler Docs](https://laravel.com/docs/11.x/scheduling)
- [Laravel Mail Docs](https://laravel.com/docs/11.x/mail)

---

## Support

For issues or questions about deployment, refer to the [PROGRESSNOTES.txt](PROGRESSNOTES.txt) file or check the [Revisions Documentation](REVISIONS_DOCUMENTATION_%20PLSS%20READ%20FOR%20UR%20BASIS%21%21%21/) directory.
