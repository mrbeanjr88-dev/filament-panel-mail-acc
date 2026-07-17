# Email Customs: Deployment Checklist

**Phase 4 Complete: Docker & CI/CD Ready**  
**Date:** 2026-06-20  
**Status:** ✅ PRODUCTION READY

---

## 📋 Pre-Deployment Checklist

### Local Setup (5 minutes)
```bash
# 1. Clone or navigate to repository
cd /path/to/email-customs

# 2. Copy environment file
cp .env.example .env

# 3. Start Docker containers
docker-compose up -d

# 4. Run migrations
docker-compose exec app php artisan migrate

# 5. Generate application key (if not set)
docker-compose exec app php artisan key:generate

# 6. Verify health endpoint
curl http://localhost:8000/health
```

**Verification:** Response should be JSON with `"status": "healthy"`

---

## 🔑 GitHub Repository Setup

### Step 1: Enable GitHub Actions
- [ ] Go to repository → Settings → Actions → General
- [ ] Enable "Allow all actions and reusable workflows"

### Step 2: Add Deployment Secrets
Go to: Settings → Secrets and variables → Actions → New repository secret

**Required Secrets:**

| Secret Name | Value | Example |
|------------|-------|---------|
| `DEPLOY_HOST` | Server hostname | `deploy.example.com` |
| `DEPLOY_USER` | SSH username | `deploy` |
| `DEPLOY_PRIVATE_KEY` | SSH private key | (full key with headers) |
| `DEPLOY_PATH` | App directory | `/var/www/email-customs` |

**Optional Secrets:**

| Secret Name | Value | Purpose |
|------------|-------|---------|
| `SLACK_WEBHOOK` | Slack webhook URL | Deployment notifications |
| `REGISTRY_USERNAME` | Docker registry user | Docker image push |
| `REGISTRY_PASSWORD` | Docker registry token | Docker image push |

### Step 3: Set Up Production Environment (Optional)
- [ ] Go to Settings → Environments → New environment
- [ ] Name: `production`
- [ ] Add required reviewers (team leads)
- [ ] Add deployment branches: `main`

---

## 🖥️ Deployment Server Setup

### Prerequisites
```bash
# 1. Verify Docker is installed
docker --version        # Should be 20.10+

# 2. Verify Docker Compose is installed
docker-compose --version  # Should be 2.0+

# 3. Create deploy user (if not exists)
sudo useradd -m -s /bin/bash deploy

# 4. Add user to docker group
sudo usermod -aG docker deploy

# 5. Switch to deploy user
sudo -u deploy -i
```

### Step 1: Generate SSH Key for GitHub Actions
```bash
# As deploy user:
ssh-keygen -t ed25519 -N "" -f ~/.ssh/github-actions

# Display private key (copy entire output)
cat ~/.ssh/github-actions

# Add public key to authorized_keys
cat ~/.ssh/github-actions.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys

# Verify SSH key works
ssh -i ~/.ssh/github-actions $USER@localhost
```

**What to copy to `DEPLOY_PRIVATE_KEY` secret:**
- Entire private key including `-----BEGIN OPENSSH PRIVATE KEY-----` header
- And `-----END OPENSSH PRIVATE KEY-----` footer
- Every line exactly as shown

### Step 2: Create Application Directory
```bash
# As deploy user or with sudo:
sudo mkdir -p /var/www/email-customs
sudo chown deploy:deploy /var/www/email-customs
sudo chmod 755 /var/www/email-customs

# Clone repository
cd /var/www/email-customs
git clone https://github.com/your-org/email-customs.git .
```

### Step 3: Configure Environment File
```bash
# Copy template
cp .env.example .env

# Edit for production
nano .env
```

**Critical settings:**
```bash
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_GENERATED_KEY

DB_HOST=db
DB_DATABASE=email_customs
DB_USERNAME=postgres
DB_PASSWORD=YOUR_SECURE_PASSWORD

REDIS_HOST=redis
REDIS_PASSWORD=YOUR_REDIS_PASSWORD

LOG_CHANNEL=stack
LOG_LEVEL=warning
```

### Step 4: Create Data Directories
```bash
# Create persistent volumes
mkdir -p /var/www/email-customs/storage/logs
mkdir -p /var/www/email-customs/storage/app/attachments
mkdir -p /var/www/email-customs/data/postgres
mkdir -p /var/www/email-customs/data/redis

# Set permissions
chmod -R 755 /var/www/email-customs/storage
chmod -R 755 /var/www/email-customs/data
```

### Step 5: Test Pre-Deployment
```bash
# Navigate to app directory
cd /var/www/email-customs

# Run pre-deployment checks
bash scripts/pre-deploy-check.sh

# Should show: "✓ All checks passed!"
```

---

## 🚀 First Deployment

### Option 1: Automatic (Recommended)
1. Push code to `main` branch
2. GitHub Actions CI/CD runs automatically
3. Wait for tests, lint, security, and build to pass
4. Deployment starts automatically
5. Check: `curl http://your-server.com:8000/health`

### Option 2: Manual Trigger
1. Go to GitHub repository → Actions tab
2. Select workflow: "Deploy to Production"
3. Click: "Run workflow"
4. Select environment: `production`
5. Click: "Run workflow"
6. Monitor logs in Actions tab

### Option 3: GitHub CLI
```bash
gh workflow run deploy.yml -f environment=production
```

---

## ✅ Post-Deployment Verification

### Immediate (1-2 minutes)
```bash
# 1. Check HTTP response
curl -i http://your-server.com:8000/health

# Expected: HTTP/1.1 200 OK
# Body: JSON with "status": "healthy"
```

### SSH Verification (5 minutes)
```bash
# SSH to server
ssh deploy@your-server.com

# 2. Check running containers
docker-compose ps

# Expected: All 3 services running (app, db, redis)

# 3. Check app logs
docker-compose logs app | tail -50

# Expected: No ERROR messages, migrations completed

# 4. Test database connection
docker-compose exec app php artisan tinker
# Then type: DB::connection()->getPdo();
# Expected: Returns PDO object (not null)
# Exit: exit()
```

### Admin Panel (10 minutes)
1. Navigate to: http://your-server.com:8000
2. Login with credentials
3. Check Dashboard loads correctly
4. Verify no console errors (F12 → Console)
5. Click through main sections:
   - Quarantine (Pending Emails)
   - Email Accounts
   - Bank Accounts
   - Filter Rules
   - Reports

### Database Verification (15 minutes)
```bash
# Connect to PostgreSQL
docker-compose exec db psql -U postgres -d email_customs

# Check tables exist
\dt

# Check sample data
SELECT COUNT(*) FROM pending_emails;
SELECT COUNT(*) FROM email_accounts;
SELECT COUNT(*) FROM bank_accounts;

# Exit
\q
```

---

## 📊 Monitoring After Deployment

### Daily
- [ ] Check health endpoint: `curl http://server/health`
- [ ] Review error logs: `docker-compose logs app | grep ERROR`
- [ ] Monitor disk space: `df -h /var/www/email-customs`

### Weekly
- [ ] Check database size: `SELECT pg_size_pretty(pg_database_size('email_customs'));`
- [ ] Review slow queries in logs
- [ ] Check Docker image sizes: `docker images`

### Monthly
- [ ] Archive old emails: `docker-compose exec app php artisan archive:emails`
- [ ] Review deployment history in GitHub Actions
- [ ] Update dependencies: `composer update`

---

## 🆘 Troubleshooting

### Deployment Failed: Permission Denied
```bash
# Problem: GitHub Actions can't SSH
# Solution:
1. Verify DEPLOY_PRIVATE_KEY is complete (includes header/footer)
2. Check public key in authorized_keys:
   cat ~/.ssh/authorized_keys | grep -i github
3. Verify SSH permissions:
   ls -la ~/.ssh/  # Should be 700
   ls -la ~/.ssh/authorized_keys  # Should be 600
```

### Deployment Failed: Connection Refused
```bash
# Problem: Can't reach server
# Solution:
1. Verify DEPLOY_HOST is correct (no https://, just hostname)
2. Test SSH manually:
   ssh -i /tmp/key deploy@your-server.com
3. Check firewall allows port 22:
   sudo ufw allow 22
```

### Health Check Timeout
```bash
# Problem: Health endpoint not responding
# Solution:
docker-compose logs app | grep -i health
docker-compose logs app | grep -i error

# Common causes:
# - Migrations still running (wait 2-5 minutes)
# - Database not ready (check db logs)
# - PHP-FPM crashed (check supervisord)
```

### Database Connection Error
```bash
# Check PostgreSQL is running
docker-compose ps db

# View database logs
docker-compose logs db

# Test connection from app container
docker-compose exec app php artisan db:show

# Reset database (WARNING: loses data)
docker-compose down -v
docker-compose up -d
docker-compose exec app php artisan migrate
```

### Cache/Session Issues
```bash
# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Restart Redis
docker-compose restart redis

# Verify Redis connectivity
docker-compose exec app php artisan redis:test
```

---

## 📈 Scaling Checklist

### Load Testing
```bash
# Install Apache Bench (if not installed)
apt-get install apache2-utils

# Test health endpoint
ab -n 1000 -c 100 http://your-server.com:8000/health

# Test main page
ab -n 100 -c 10 http://your-server.com:8000/
```

### Performance Optimization
- [ ] Enable query caching: Check Redis health
- [ ] Review slow queries: Set `log_min_duration_statement=100`
- [ ] Monitor memory: `docker stats`
- [ ] Add database indexes (already done in Phase 1)
- [ ] Enable gzip compression (already done)

### High Availability (Future)
- [ ] Load balancer (nginx, HAProxy)
- [ ] Database replica
- [ ] Redis cluster
- [ ] Multiple app instances
- [ ] CDN for static assets

---

## 🔒 Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong database password (12+ chars, mixed)
- [ ] Strong Redis password
- [ ] SSH key rotation (generate new keys quarterly)
- [ ] Review firewall rules (only ports 22, 80, 443)
- [ ] Enable SSL/TLS (add to nginx.conf)
- [ ] Regular backups configured
- [ ] Log monitoring enabled
- [ ] Rate limiting configured
- [ ] CORS properly configured

---

## 📚 Documentation Links

| Document | Purpose | When to Use |
|----------|---------|------------|
| `QUICK_START.md` | 5-minute setup | Getting started |
| `.github/DEPLOYMENT.md` | Full deployment guide | Detailed instructions |
| `.github/workflows/README.md` | CI/CD details | Understanding pipelines |
| `.env.production.example` | Environment reference | Configuration |
| `COMPLETE_IMPLEMENTATION.md` | Full feature docs | Feature overview |
| `PHASE_4_SUMMARY.md` | Docker & CI/CD details | Technical details |
| `scripts/pre-deploy-check.sh` | Verification script | Before deployment |

---

## ✅ Final Checklist

**Before First Deployment:**
- [ ] GitHub repository created and code pushed
- [ ] GitHub Actions enabled
- [ ] All secrets added to repository
- [ ] Deployment server created with Docker
- [ ] SSH key generated and configured
- [ ] Application directory created
- [ ] `.env` file configured
- [ ] Pre-deployment checks pass
- [ ] Health endpoint returns 200

**After Deployment:**
- [ ] Admin panel accessible and loads
- [ ] Database migrations applied
- [ ] Health check endpoint responding
- [ ] No error logs in app
- [ ] Containers all running
- [ ] Disk space adequate
- [ ] Backups configured (if applicable)

**Production Ready:**
- [ ] SSL/TLS certificate installed
- [ ] Monitoring configured
- [ ] Alerting configured
- [ ] Incident response plan documented
- [ ] Team trained on deployment process

---

## 🎯 Success Criteria

✅ **Deployment is successful when:**
1. `curl http://your-server:8000/health` returns HTTP 200
2. Admin panel is accessible at http://your-server:8000
3. All Docker containers are running: `docker-compose ps`
4. Database migrations are applied: `php artisan migrate:status`
5. No ERROR lines in app logs: `docker-compose logs app`
6. Health check shows all systems: database ✓, cache ✓, imap ✓, queue ✓

---

**Status: READY FOR DEPLOYMENT ✅**

For support, refer to `.github/DEPLOYMENT.md` or run:
```bash
bash scripts/pre-deploy-check.sh
```
