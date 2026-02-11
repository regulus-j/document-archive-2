# Docker Setup Guide

This guide explains how to run the Document Archive application using Docker.

## Prerequisites

- Docker Engine (20.10 or later)
- Docker Compose (v2.0 or later)

## Quick Start

1. **Clone the repository** (if you haven't already):
   ```bash
   git clone <repository-url>
   cd document-archive-2
   ```

2. **Create environment file**:
   ```bash
   cp .env.example .env
   ```

3. **Generate application key**:
   ```bash
   # You can generate a key manually or use the following command after starting the container
   docker compose exec app php artisan key:generate
   ```

4. **Update the `.env` file** with secure values:
   - Set a strong `APP_KEY` (or generate it in step 3)
   - Update database credentials if needed (defaults are in docker-compose.yml)
   - Ensure `DB_HOST=mysql` (this is the service name in docker-compose.yml)

5. **Build and start the containers**:
   ```bash
   docker compose up -d --build
   ```

6. **Run database migrations**:
   ```bash
   docker compose exec app php artisan migrate
   ```

7. **Access the application**:
   Open your browser and navigate to `http://localhost:8080`

## Container Services

- **app**: The main Laravel application running on Apache (port 8080)
- **mysql**: MySQL 8.0 database with persistent storage

## Useful Commands

### View logs
```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f app
docker compose logs -f mysql
```

### Execute commands in the app container
```bash
docker compose exec app php artisan <command>
docker compose exec app composer <command>
```

### Stop containers
```bash
docker compose down
```

### Stop containers and remove volumes
```bash
docker compose down -v
```

### Rebuild containers
```bash
docker compose up -d --build
```

## Persistent Data

The setup uses Docker volumes for persistent data:
- `mysql-data`: MySQL database files
- `storage`: Application storage files

These volumes persist even when containers are stopped or removed.

## Environment Variables

Key environment variables for Docker setup:

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_KEY` | Laravel application key | (none - must be set) |
| `DB_ROOT_PASSWORD` | MySQL root password | `rootpassword` |
| `DB_DATABASE` | Database name | `doc_archive` |
| `DB_USERNAME` | Database user | `docarchive` |
| `DB_PASSWORD` | Database password | `password` |

**Security Note**: Always use strong passwords in production environments. The defaults are only for development.

## Troubleshooting

### MySQL connection issues
- Ensure the `DB_HOST` in `.env` is set to `mysql` (the service name)
- Wait for the MySQL container to be healthy: `docker compose ps`

### Permission issues
- The entrypoint script automatically sets proper permissions for storage directories

### Port already in use
If port 8080 is already in use, edit `docker-compose.yml` and change the port mapping:
```yaml
ports:
  - "8081:80"  # Change 8080 to your preferred port
```
