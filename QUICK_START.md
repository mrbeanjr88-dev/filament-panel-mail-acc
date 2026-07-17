# Email Customs: Quick Start Guide

**🚀 Get started in 5 minutes**

---

## Local Development

### 1. Start Everything
```bash
docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan key:generate
```

### 2. Access the System
- **Admin Panel**: http://localhost:8000
- **Health Check**: http://localhost:8000/health
- **Database**: `docker-compose exec db psql -U postgres -d email_customs`
- **Logs**: `docker-compose logs -f app`

### 3. Stop Services
```bash
docker-compose down
```

---

## Production Deployment

### Prerequisites
- [ ] GitHub repository set up
- [ ] Deployment server with Docker/Docker Compose
- [ ] SSH access to server

### Setup (15 minutes)

**Step 1: Generate SSH Key**
```bash
# On your deployment server:
ssh-keygen -t ed25519 -N "" -f ~/.ssh/github-actions
cat ~/.ssh/github-actions                    # Copy this
cat ~/.ssh/github-actions.pub >> ~/.ssh/authorized_keys
```

**Step 2: Add GitHub Secrets**
In GitHub → Settings → Secrets and variables → Actions, add:
- `DEPLOY_HOST` → your-server.com
- `DEPLOY_USER` → deploy
- `DEPLOY_PRIVATE_KEY` → (paste the key from Step 1)
- `DEPLOY_PATH` → /var/www/email-customs

**Step 3: Configure Server**
```bash
# On your server:
mkdir -p /var/www/email-customs
cd /var/www/email-customs
git clone https://github.com/your-org/email-customs.git .
cp .env.example .env
# Edit .env with your database password, API keys, etc.
```

**Step 4: Pre-Deploy Check**
```bash
bash scripts/pre-deploy-check.sh
```

**Step 5: Deploy**
- Push to `main` branch
- GitHub Actions CI/CD runs automatically
- On success, deployment starts
- Check: http://your-server.com/health

### Verify Deployment
```bash
# SSH into server
ssh deploy@your-server.com

# Check containers
docker-compose ps

# View logs
docker-compose logs -f app

# Check health
curl http://localhost:8000/health
```

---

## Testing

### Run Tests Locally
```bash
docker-compose exec app php artisan test
```

### Run Specific Test
```bash
docker-compose exec app php artisan test tests/Unit/Services/BankDataExtractorTest.php
```

---

## Database Management

### Run Migrations
```bash
docker-compose exec app php artisan migrate
```

### Rollback Latest Migration
```bash
docker-compose exec app php artisan migrate:rollback
```

### Connect to Database
```bash
docker-compose exec db psql -U postgres -d email_customs
```

### Common Queries
```sql
-- List tables
\dt

-- Check pending emails
SELECT id, subject, status, created_at FROM pending_emails LIMIT 10;

-- Check bank accounts
SELECT id, name, current_balance FROM bank_accounts;

-- Exit
\q
```

---

## Monitoring

### Health Check
```bash
# Should return 200 with JSON
curl http://localhost:8000/health
```

### View Logs
```bash
# Real-time logs
docker-compose logs -f app

# Last 100 lines
docker-compose logs --tail=100 app

# Specific service
docker-compose logs -f db
docker-compose logs -f redis
```

### Check Resource Usage
```bash
docker stats
```

---

## Troubleshooting

### Container Won't Start
```bash
# Check logs
docker-compose logs app

# Restart
docker-compose restart app

# Reset (WARNING: loses data)
docker-compose down -v
docker-compose up -d
```

### Database Connection Error
```bash
# Ensure database is running
docker-compose ps db

# Check PostgreSQL logs
docker-compose logs db

# Verify database exists
docker-compose exec db psql -U postgres -l
```

### Health Check Fails
```bash
# Check app logs
docker-compose logs app

# Check migrations status
docker-compose exec app php artisan migrate:status

# Test database connection
docker-compose exec app php artisan tinker
# Then: DB::connection()->getPdo();
```

### Cache/Session Issues
```bash
# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Restart Redis
docker-compose restart redis
```

---

## File Locations

| Purpose | File |
|---------|------|
| Deployment Guide | `.github/DEPLOYMENT.md` |
| Workflow Details | `.github/workflows/README.md` |
| Configuration | `.env.production.example` |
| Implementation | `COMPLETE_IMPLEMENTATION.md` |
| Phase 4 Details | `PHASE_4_SUMMARY.md` |
| Pre-Deploy Check | `scripts/pre-deploy-check.sh` |

---

## Useful Commands

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View specific container logs
docker-compose logs -f app

# Execute command in container
docker-compose exec app php artisan [command]

# SSH into app container
docker-compose exec app sh

# Rebuild images
docker-compose build --no-cache

# Clean up (WARNING: removes volumes)
docker-compose down -v
docker system prune -a

# Check container health
docker-compose ps
docker inspect email-customs-app | grep Health
```

---

## Environment Variables

**Development (.env)**
```
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
REDIS_HOST=redis
CACHE_DRIVER=redis
APP_ENV=local
APP_DEBUG=true
```

**Production (.env.production.example)**
```
DB_CONNECTION=pgsql
APP_ENV=production
APP_DEBUG=false
REDIS_PASSWORD=YourSecurePassword
```

---

## Health Endpoint Response

```json
{
  "status": "healthy",
  "timestamp": "2026-06-20T12:00:00Z",
  "checks": {
    "database": "ok",
    "cache": "ok",
    "imap": "ok",
    "queue": "ok"
  }
}
```

---

## Deployment Checklist

- [ ] SSH key generated and added to GitHub
- [ ] Server created with Docker installed
- [ ] Repository cloned on server
- [ ] `.env` configured with database password
- [ ] Pre-deployment check passes
- [ ] GitHub secrets configured
- [ ] Code pushed to `main` branch
- [ ] CI/CD pipeline passes
- [ ] Deployment completes
- [ ] Health check endpoint returns 200
- [ ] Admin panel accessible
- [ ] Database migrations applied

---

## Support

**For detailed information, see:**
- `.github/DEPLOYMENT.md` — Production deployment guide
- `.github/workflows/README.md` — CI/CD pipeline details
- `COMPLETE_IMPLEMENTATION.md` — Full feature documentation

**Quick issues?**
1. Check logs: `docker-compose logs -f`
2. Run pre-deploy check: `bash scripts/pre-deploy-check.sh`
3. Review `.github/DEPLOYMENT.md` troubleshooting section

---

**Status: PRODUCTION READY ✅**
