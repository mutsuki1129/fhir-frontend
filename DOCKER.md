# Docker Dev Guide

## 1) Prepare env
Copy Docker env file and set app key:

```bash
# PowerShell
Copy-Item .env.docker .env

# Bash
cp .env.docker .env
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
```

## 2) Start containers

```bash
docker compose up -d
```

## 3) Prepare database

```bash
docker compose exec app php artisan migrate --seed
```

## 4) Access app
- Laravel app: http://localhost:8080
- Vite dev server: http://localhost:5173
- phpMyAdmin: http://localhost:8081

## Useful commands

```bash
docker compose exec app php artisan test
docker compose exec app php artisan tinker
docker compose exec app composer install
docker compose exec node npm run build
docker compose down
```

## E2E scripts

```bash
# Quick smoke
powershell -ExecutionPolicy Bypass -File scripts/e2e-smoke.ps1

# Full regression for rekam/dokters/pasiens
powershell -ExecutionPolicy Bypass -File scripts/e2e-full.ps1
```

## Windows performance tips
- This compose setup now keeps `vendor`, `storage`, and `bootstrap/cache` in Docker volumes to reduce slow bind-mount IO on Windows.
- `node` service now installs npm packages only when `node_modules` is missing, reducing startup time.
- PHP `opcache` and `realpath_cache` are enabled in `docker/php/opcache.ini` for faster page load in dev.
- For best performance, keep the project inside WSL2 filesystem (example: `/home/<user>/project/fhir`) and run Docker/terminal from WSL.

## phpMyAdmin login
- Server/Host: `db`
- Username: `fhir` (or `root`)
- Password: `123456` (or `root`)
