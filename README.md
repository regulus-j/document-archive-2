# DocTrack — Document Tracking & Archiving System

A Laravel 11 multi-tenant document management platform with workflow automation, urgency tracking, and intelligent notification system.

## Overview

DocTrack is a comprehensive document archiving and workflow management system designed for organizations to:

- **Organize & Archive** documents with multi-level classification (Public, Office Only, Confidential)
- **Automate Workflows** with step-by-step document routing and approval chains
- **Track Urgency** using AI-powered analysis and keyword matching
- **Receive Alerts** via email and in-app notifications for overdue documents
- **Control Access** through role-based permissions (super-admin, company-admin, office-lead, user)

## Technology Stack

| Component | Technology |
|-----------|-----------|
| **Backend** | Laravel 11, PHP 8.x |
| **Frontend** | Tailwind CSS 3, Alpine.js 3, Vite bundler |
| **Database** | MySQL 5.7+ |
| **Authentication** | Laravel Breeze + Spatie Permissions |
| **Scheduling** | Laravel Scheduler (Supervisor in Docker) |
| **Barcodes** | Code-128 barcode generation & overlay |
| **AI Analysis** | Ollama LLM (configurable) |
| **Deployment** | Docker + Dokploy (Ubuntu) or XAMPP (Windows dev) |

## Key Features

### 📋 Document Management
- **Multi-tenant** isolation by company
- **File upload** with barcode generation (PDF, images, documents)
- **Classification system** with visibility controls
- **Document history** and version tracking

### 🔄 Workflow Automation
- **Sequential routing** with automatic step advancement
- **Office-based forwarding** with receiver assignment
- **Custom workflows** per document type
- **Activity tracking** for compliance

### 🚨 Urgency & Notification System
- **AI-powered urgency analysis** (40% Ollama LLM, 25% keywords, 20% metadata, 15% temporal)
- **Automatic escalation** when deadlines are exceeded
- **Inactivity detection** for stalled workflows
- **Email alerts** for critical and high-priority documents
- **In-app notifications** with polling every 60 seconds
- **Cooldown periods** to prevent alert fatigue (6-24 hours based on priority)

### 🔐 Role-Based Access Control (RBAC)
| Role | Capabilities |
|------|-------------|
| `super-admin` | Platform dashboard, manage companies, subscriptions |
| `company-admin` | Manage all documents and users in company |
| `office-lead` | Lead office workflows, manage office documents |
| `user` | Upload, forward, view documents per classification |

## Quick Start

### Development (Windows + XAMPP)

```bash
# Clone and setup
git clone <repo-url> C:\xampp2\htdocs\document_archive
cd document_archive
cp .env.example .env
php artisan key:generate

# Install dependencies
composer install
npm install

# Database setup
php artisan migrate
php artisan storage:link

# Build assets
npm run build

# Test (on Windows, manually test scheduler)
php artisan documents:monitor-urgency
php artisan notifications:fast-forward --type=escalation
```

### Production (Ubuntu + Dokploy)

```bash
# Deploy with Docker
docker-compose up -d

# Run migrations
docker-compose exec php php artisan migrate --force

# Verify scheduler is running
docker-compose logs supervisor | grep laravel-scheduler
```

⚠️ **Important**: The **Scheduler must be running** for notifications to work. See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed setup.

## Notification System

The notification system automatically detects overdue documents and sends alerts every 30 minutes.

### How It Works

1. **Every 30 minutes**: `documents:monitor-urgency` runs via Laravel Scheduler
2. **Scans active workflows** and checks urgency thresholds
3. **Creates alerts**:
   - **WARNING**: Approaching deadline
   - **ESCALATION**: Deadline exceeded (sends email)
   - **INACTIVITY**: No activity for threshold hours
4. **Sends notifications**:
   - In-app notifications (polled every 60 seconds)
   - Email alerts (for escalations)

### Thresholds

Thresholds vary by urgency level (Critical, High, Medium, Low):

```
Critical: WARNING @ 2hrs, ESCALATION @ 4hrs, INACTIVITY @ 3hrs
High:     WARNING @ 4hrs, ESCALATION @ 8hrs, INACTIVITY @ 6hrs
Medium:   WARNING @ 8hrs, ESCALATION @ 16hrs, INACTIVITY @ 12hrs
Low:      WARNING @ 16hrs, ESCALATION @ 32hrs, INACTIVITY @ 24hrs
```

**Cooldown before re-notifying**: 6-24 hours (prevents notification fatigue)

## Common Commands

### Development

```bash
php artisan migrate                 # Run migrations
php artisan documents:monitor-urgency                    # Check for overdue docs
php artisan notifications:fast-forward --type=escalation # Test notifications
npm run dev                         # Vite dev server with hot reload
npm run build                       # Production build
```

### Production (Docker)

```bash
docker-compose exec php php artisan migrate --force
docker-compose logs supervisor | grep laravel-scheduler  # Verify scheduler
docker-compose exec php tail -f storage/logs/laravel.log # View logs
```

## Architecture Highlights

### Multi-Tenant Design
- All data scoped to `company_id`
- `DocumentAccessService` enforces RBAC + classification visibility
- Complete data isolation between tenants

### Barcode System
- Migrated from QR codes to **Code-128 barcodes**
- Supported on **PDF & images** (via FPDI & GD)
- Generated with company/document metadata

### Custom Notifications
- **Custom model** (not Laravel's built-in)
- **Polling via JavaScript** (in-app badge updates every 60 seconds)
- **Rich data storage** (document info in JSON)

### Scheduler-Based Alerts
- Runs **every 30 minutes** (configurable in [AppServiceProvider.php](app/Providers/AppServiceProvider.php#L43))
- Managed by **Supervisor** in Docker (configured in [docker/supervisord.conf](docker/supervisord.conf#L24))
- **Cooldown tracking** prevents duplicate alerts

## Troubleshooting

### Notifications Not Working?

1. **Is the scheduler running?** (Production)
   ```bash
   docker-compose logs supervisor | grep laravel-scheduler
   ```

2. **Are alerts being created?**
   ```bash
   php artisan tinker
   > DB::table('document_urgency_notifications')->count()
   ```

3. **Check logs**
   ```bash
   tail -f storage/logs/laravel.log | grep urgency
   ```

👉 **Full troubleshooting guide in [DEPLOYMENT.md#troubleshooting](DEPLOYMENT.md#troubleshooting)**

## Project Structure

```
document_archive/
├── app/
│   ├── Console/Commands/
│   │   ├── MonitorDocumentUrgency.php    # 🔴 Runs every 30 min
│   │   └── FastForwardNotifications.php  # For testing
│   ├── Services/
│   │   ├── DocumentAccessService.php
│   │   ├── DocumentUrgencyAnalyzer.php
│   │   └── BarcodeService.php
│   └── Providers/
│       └── AppServiceProvider.php        # 🔴 Register scheduler
├── database/migrations/                  # Database schema
├── docker/
│   ├── supervisord.conf                  # 🔴 Scheduler service
│   └── nginx.conf
├── resources/views/layouts/app.blade.php # 🔴 Notification polling
├── DEPLOYMENT.md                         # 📚 Setup guide
└── storage/logs/laravel.log              # Application logs
```

## Database Schema Highlights

| Table | Purpose |
|-------|---------|
| `documents` | Core document records with urgency_level |
| `workflows` | Document routing steps (pending → waiting → received) |
| `document_urgency_notifications` | Escalation tracking (prevents duplicate alerts) |
| `notifications` | In-app notifications for users |
| `document_workflows_transitions` | Audit trail of workflow actions |

## Resources

- **[DEPLOYMENT.md](DEPLOYMENT.md)** — Full deployment & setup guide
- **[PROGRESSNOTES.txt](PROGRESSNOTES.txt)** — Development progress
- **[Revisions Documentation](REVISIONS_DOCUMENTATION_%20PLSS%20READ%20FOR%20UR%20BASIS%21%21%21/)** — System changes
- **[Laravel 11 Docs](https://laravel.com/docs/11.x)**

## Environment Variables

```env
# Mail (required for email alerts)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=app-password

# Database
DB_HOST=localhost
DB_DATABASE=document_archive
DB_USERNAME=root
DB_PASSWORD=your-password

# Ollama (for AI urgency analysis)
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_MODEL=mistral
```

See [DEPLOYMENT.md](DEPLOYMENT.md#environment-variables-reference) for complete reference.

## File Storage

- **Location**: `storage/app/public/{company_id}/documents/`
- **Access**: Via `storage/` symlink at `public/storage/`
- **Temp files**: `storage/app/temp/` (for barcode generation)

## License

This project is proprietary software. All rights reserved.

---

**Last Updated**: March 12, 2026  
**Scheduler Status**: ✅ Enabled (Docker Supervisor + Laravel Scheduler)
