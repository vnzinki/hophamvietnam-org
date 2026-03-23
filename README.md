# Họ Phạm Việt Nam — WordPress Dev Environment

## Prerequisites

- Docker & Docker Compose
- Git LFS (`brew install git-lfs && git lfs install`)

## Quick Start

```bash
# 1. Pull LFS files (database dump)
git lfs pull

# 2. Start containers (builds WordPress image with WP-CLI)
docker compose up -d --build

# 3. Import the production database dump
docker compose exec -T db mysql -uwordpress -pwordpress wordpress \
  < database/*.dmp

# 4. Fix URLs for local development
docker compose exec wordpress wp --allow-root \
  search-replace 'https://hophamvietnam.org' 'http://localhost:8080' --all-tables

# 5. Install Advanced Media Offloader (serves images from S3)
docker compose exec wordpress wp --allow-root plugin install advanced-media-offloader --activate

# 6. Activate theme & set local admin password
docker compose exec wordpress wp --allow-root theme activate hopham-vietnam
docker compose exec wordpress wp --allow-root user update trungpq --user_pass=admin123 --skip-email
```

> **Media images** are served from the production cloud storage via the **Advanced Media Offloader** plugin.
> Its bucket credentials are restored automatically from the production DB dump — no extra configuration needed.

## URLs

| Service    | URL                            |
|------------|--------------------------------|
| WordPress  | http://localhost:8080           |
| WP Admin   | http://localhost:8080/wp-admin  |
| phpMyAdmin | http://localhost:8081           |
| MySQL      | localhost:3306                  |

## Local Admin Credentials

| Field    | Value      |
|----------|------------|
| Username | `trungpq`  |
| Password | `admin123` |

> These are local-only credentials. Production passwords are unchanged.

## Theme Development

Theme source lives in `./theme/` — changes are reflected instantly (volume mount, no rebuild needed).

## Common Commands

```bash
# Stop containers (preserves data)
docker compose stop

# Destroy everything including DB data
docker compose down -v

# View WordPress logs
docker compose logs -f wordpress

# Run WP-CLI command
docker compose exec wordpress wp --allow-root <command>
```
