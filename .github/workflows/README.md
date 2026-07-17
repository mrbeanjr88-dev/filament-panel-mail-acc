# GitHub Actions Workflows

This directory contains CI/CD automation for the Email Customs system.

## Workflows

### `ci.yml` — Continuous Integration
Runs on every push to `main` and `develop` branches, plus all pull requests.

**Jobs:**
- **Tests** — Runs unit and feature tests against PostgreSQL
- **Lint** — PHP syntax checks and code formatting (Pint)
- **Security** — Vulnerability scanning via `composer audit`
- **Build** — Builds Docker image (only on successful test/lint/security + push to main/develop)

### `deploy.yml` — Deployment to Production/Staging
Manually triggered or runs on push to `main`.

**Trigger:**
- Manual: `workflow_dispatch` with environment selection (staging/production)
- Automatic: Push to `main` branch (excludes README.md, docs/)

**Deployment Steps:**
1. SSH into deploy server
2. Pull latest code
3. Pull Docker images
4. Run `docker-compose up -d` (rebuild containers)
5. Run database migrations
6. Cache Laravel config
7. Health check verification (30 attempts, 5s intervals)
8. Slack notification (success/failure)

## Setup Instructions

### 1. GitHub Repository Secrets

Add the following secrets to your repository:

**For CI (all workflows):**
None required — tests use local PostgreSQL service container.

**For Deployment (`deploy.yml`):**
```
DEPLOY_HOST          # Production server hostname (e.g., deploy.example.com)
DEPLOY_USER          # SSH username (e.g., deploy)
DEPLOY_PRIVATE_KEY   # SSH private key (paste the full key)
DEPLOY_PATH          # Full path to app directory (e.g., /var/www/email-customs)
SLACK_WEBHOOK        # Slack webhook URL for notifications (optional)
```

### 2. Configure Deployment SSH Key

Generate a new SSH key on your deployment server:

```bash
# On your server, as the deploy user:
ssh-keygen -t ed25519 -N "" -f ~/.ssh/github-actions

# Export the private key:
cat ~/.ssh/github-actions
```

Then:
1. Copy the private key output to GitHub → Settings → Secrets → `DEPLOY_PRIVATE_KEY`
2. Add the public key to authorized_keys:
```bash
cat ~/.ssh/github-actions.pub >> ~/.ssh/authorized_keys
```

### 3. GitHub Environment Protection

For production deployments, configure GitHub environment protection:

1. Go to: Repository → Settings → Environments → New environment
2. Create `production` environment
3. Add required reviewers (team leads)
4. Add deployment branches: `main`

### 4. Docker Registry (Optional)

To push Docker images to a registry (Docker Hub, GitHub Container Registry, etc.):

1. Uncomment the `docker/login-action` step in `ci.yml` build job
2. Add registry credentials as secrets:
   ```
   REGISTRY_USERNAME
   REGISTRY_PASSWORD
   ```

### 5. Slack Notifications (Optional)

Enable deployment notifications:

1. Create a Slack webhook URL: https://api.slack.com/messaging/webhooks
2. Add to GitHub secrets: `SLACK_WEBHOOK`
3. Notifications will post to your channel on every deployment

## Local Testing

### Run CI Tests Locally

```bash
# Install dependencies
composer install

# Generate test key
php artisan key:generate --env=testing

# Run tests
php artisan test

# Run linting
./vendor/bin/pint --test
```

### Test Docker Build Locally

```bash
# Build Docker image
docker build -t email-customs:local .

# Run with docker-compose
docker-compose up -d

# Check health
curl http://localhost:8000/health
```

## Monitoring

### View Workflow Status

1. GitHub → Actions tab
2. Select workflow to see logs
3. Expand job steps to view detailed output

### Deployment Monitoring

- **Health endpoint**: `GET /health` returns application status
- **Logs**: Check `docker-compose logs app` on deployment server
- **Slack notifications**: Receive status updates in Slack (if configured)

## Troubleshooting

### Tests failing in CI but passing locally?

Common causes:
- Database state differences → run `php artisan migrate:fresh` locally
- Missing env vars → check `.env.testing`
- PostgreSQL version difference → use `postgres:15-alpine` locally

### Deployment fails with SSH error?

1. Verify `DEPLOY_PRIVATE_KEY` is the full private key (including `-----BEGIN OPENSSH PRIVATE KEY-----` header)
2. Check `DEPLOY_HOST` and `DEPLOY_USER` are correct
3. Verify public key is in `~/.ssh/authorized_keys` on server
4. Test SSH manually: `ssh -i key.pem user@host`

### Health check times out?

1. Verify app container is running: `docker-compose ps`
2. Check app logs: `docker-compose logs app`
3. Verify `docker-compose.yml` health check configuration
4. Increase timeout in deploy workflow if migrations are slow

## References

- [GitHub Actions Documentation](https://docs.github.com/actions)
- [Docker Buildx](https://docs.docker.com/build/buildkit/dockerfile/)
- [Laravel Testing](https://laravel.com/docs/testing)
