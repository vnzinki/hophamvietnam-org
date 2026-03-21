# Họ Phạm Việt Nam — WordPress Dev Environment

## Quick Start

```powershell
# 1. Start containers
docker compose up -d

# 2. Wait ~30s for MySQL to initialize

# 3. One-time setup: imports DB, fixes URLs, installs Advanced Media Offloader, activates theme
.\import-db.ps1
```

> **Media images** are served from the production cloud storage via the **Advanced Media Offloader** plugin.
> Its bucket credentials are restored automatically from the production DB dump — no extra configuration needed.

## URLs

| Service    | URL                        |
|------------|----------------------------|
| WordPress  | http://localhost:8080       |
| WP Admin   | http://localhost:8080/wp-admin |
| phpMyAdmin | http://localhost:8081       |
| MySQL      | localhost:3306              |

## Local Admin Credentials

| Field    | Value     |
|----------|-----------|
| Username | `trungpq` |
| Password | `admin123` |

> These are local-only credentials set by `import-db.ps1`. Production passwords are unchanged.

## Theme Development

Theme source lives in `./theme/` — changes are reflected instantly (volume mount, no rebuild needed).

## Common Commands

```powershell
# Stop containers (preserves data)
docker compose stop

# Destroy everything including DB data
docker compose down -v

# View WordPress logs
docker compose logs -f wordpress

# Run WP-CLI command
docker compose exec wordpress wp --allow-root <command>
```
