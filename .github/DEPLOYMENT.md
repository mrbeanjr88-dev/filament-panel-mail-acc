# Deployment Guide

This guide covers deploying the Email Customs application using GitHub Actions and Docker.

## Prerequisites

- GitHub repository with Actions enabled
- Deployment server with Docker and Docker Compose installed
- SSH access to deployment server
- (Optional) Slack workspace for notifications

## Step 1: Set Up GitHub Secrets

Navigate to: **Repository Settings → Secrets and variables → Actions → New repository secret**

### Required Secrets

#### For Production Deployment
```
DEPLOY_HOST          = your-server.com
DEPLOY_USER          = deploy
DEPLOY_PRIVATE_KEY   = (SSH private key)
DEPLOY_PATH          = /var/www/email-customs
```

#### Optional Secrets
```
SLACK_WEBHOOK        = https://hooks.slack.com/services/...
REGISTRY_USERNAME    = (for Docker registry)
REGISTRY_PASSWORD    = (for Docker registry)
```

## Step 2: Generate SSH Deploy Key

On your **deployment server**, run as the deploy user:

```bash
# Generate SSH key
ssh-keygen -t ed25519 -N "" -f ~/.ssh/github-actions

# Display private key for copying to GitHub
cat ~/.ssh/github-actions

# Add public key to authorized_keys
cat ~/.ssh/github-actions.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

Copy the entire contents of `~/.ssh/github-actions` (including the `-----BEGIN OPENSSH PRIVATE KEY-----` header) to the GitHub secret `DEPLOY_PRIVATE_KEY`.

## Step 3: Configure Deployment Directory

On your **deployment server**:

```bash
# Create deployment directory
sudo mkdir -p /var/www/email-customs
sudo chown deploy:deploy /var/www/email-customs

# Clone repository
cd /var/www/email-customs
git clone https://github.com/your-org/email-customs.git .

# Create environment file
cp .env.example .env

# Edit environment variables
nano .env
```

**Critical .env settings for production:**
```bash
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxx  # Generate with: php artisan key:generate

DB_HOST=localhost
DB_DATABASE=email_customs
DB_USERNAME=postgres
DB_PASSWORD=<strong-password>

REDIS_HOST=localhost
REDIS_PASSWORD=<strong-password>

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=<optional-slack-webhook>
```

## Step 4: Set Up Docker Volumes

On your **deployment server**:

```bash
# Create persistent volume directories
mkdir -p /var/www/email-customs/storage/logs
mkdir -p /var/www/email-customs/storage/app/attachments
mkdir -p /var/www/email-customs/data/postgres
mkdir -p /var/www/email-customs/data/redis

# Set permissions
chown -R 82:82 /var/www/email-customs/storage  # 82 is www-data UID
chmod -R 775 /var/www/email-customs/storage
```

## Step 5: Verify Deployment Workflow

The deployment will:

1. **SSH into your server**
2. **Pull latest code** from the checked-out branch
3. **Pull Docker images** (PHP-FPM, PostgreSQL, Redis, etc.)
4. **Start containers** with `docker-compose up -d`
5. **Run migrations** with `php artisan migrate --force`
6. **Cache config** with `php artisan config:cache`
7. **Health check** — waits for endpoint to respond with status 200

## Step 6: Manual Deployment Trigger

To manually trigger deployment:

1. Go to: **Actions** tab → **Deploy to Production**
2. Click: **Run workflow**
3. Select: **staging** or **production**
4. Click: **Run workflow**

Or trigger via GitHub CLI:
```bash
gh workflow run deploy.yml -f environment=production
```

## Monitoring Deployment

### View Logs
1. Go to **Actions** tab
2. Click the latest workflow run
3. Expand **Deploy** job to see detailed logs

### SSH to Server and Check Status
```bash
# SSH into server
ssh deploy@your-server.com

# Check running containers
docker-compose ps

# View app logs
docker-compose logs -f app

# Check health
curl http://localhost:8000/health

# View database
docker-compose exec db psql -U postgres -d email_customs
```

### Database Access

From your server:
```bash
# Connect to PostgreSQL
docker-compose exec db psql -U postgres -d email_customs

# Common queries:
\dt                      # List tables
SELECT * FROM pending_emails LIMIT 10;  # Check emails
\q                       # Exit
```

## Rollback Procedure

If deployment fails:

1. **SSH to server**
2. **Check logs**: `docker-compose logs app`
3. **Rollback code**: `git reset --hard <previous-commit>`
4. **Restart services**: `docker-compose up -d`
5. **Verify**: `curl http://localhost:8000/health`

## Troubleshooting

### Workflow Status Page Shows "Action Failed"

**Check logs:**
1. Go to **Actions** → workflow run → **Deploy** job
2. Expand failed step
3. Common issues:

**SSH error: "Permission denied (publickey)"**
- Verify `DEPLOY_PRIVATE_KEY` secret is complete (includes header/footer)
- Check public key is in `~/.ssh/authorized_keys` on server
- Verify SSH permissions: `chmod 600 ~/.ssh/authorized_keys`

**Timeout: "Connection refused"**
- Check server is reachable: `ping your-server.com`
- Verify firewall allows SSH (port 22)
- Check `DEPLOY_HOST` is correct

**Health check fails: "Deployment failed: health check did not pass"**
- SSH to server: `docker-compose logs app`
- Check migrations: `docker-compose exec app php artisan migrate:status`
- Verify database: `docker-compose ps db`
- Check PHP-FPM: `docker-compose logs app | grep php-fpm`

**Database migration error**
- SSH to server: `docker-compose exec db psql -U postgres -d email_customs`
- Check schema: `\dt`
- View migration status: `docker-compose exec app php artisan migrate:status`
- Rollback if needed: `docker-compose exec app php artisan migrate:rollback`

### Can't Connect to PostgreSQL

```bash
# Test connection from app container
docker-compose exec app php artisan tinker
# Then: DB::connection()->getPdo();

# Test from host
docker-compose exec db psql -U postgres -c "SELECT 1"
```

### Docker Daemon Not Running

```bash
# Restart Docker
sudo systemctl restart docker

# Or check status
sudo systemctl status docker

# View Docker logs
sudo journalctl -u docker -n 50
```

## Performance Monitoring

### Database Size
```bash
docker-compose exec db psql -U postgres -d email_customs -c "SELECT pg_size_pretty(pg_database_size('email_customs'));"
```

### Active Connections
```bash
docker-compose exec db psql -U postgres -d email_customs -c "SELECT datname, count(*) FROM pg_stat_activity GROUP BY datname;"
```

### Slow Queries
Enable slow query logging in PostgreSQL:
```bash
# Add to docker-compose.yml postgres service
command: postgres -c log_min_duration_statement=1000  # Log queries > 1s
```

## Security Considerations

1. **Environment Variables**: Never commit `.env` to Git; use `.env.example` as template
2. **SSH Keys**: Rotate deploy keys regularly; use ed25519 instead of RSA
3. **Database**: Use strong passwords; restrict PostgreSQL to localhost
4. **Logs**: Monitor `/var/log/email-customs/app.log` for errors
5. **Slack Notifications**: Validate webhook URLs are correct
6. **Docker Images**: Keep base images updated (PHP, PostgreSQL, Redis)

## Automated Deployment Frequency

- **main branch**: Auto-deploys on push (after CI passes)
- **develop branch**: CI only (no auto-deploy; manual trigger via workflow_dispatch)
- **Pull requests**: CI runs; no deployment

To change this, edit `.github/workflows/deploy.yml` `on:` section.

## Next Steps

- [ ] Test CI/CD by opening a pull request
- [ ] Verify health check endpoint: `curl http://server/health`
- [ ] Configure Slack notifications (optional)
- [ ] Set up monitoring/alerting (Datadog, NewRelic, etc.)
- [ ] Document runbooks for on-call team
