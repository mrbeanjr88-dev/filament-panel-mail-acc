#!/bin/bash

# Pre-deployment verification script
# Run this on your deployment server to verify setup is correct

set -e

echo "🔍 Email Customs Pre-Deployment Check"
echo "======================================"
echo ""

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

PASSED=0
FAILED=0

# Helper functions
check_command() {
    if command -v "$1" &> /dev/null; then
        echo -e "${GREEN}✓${NC} $1 is installed"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} $1 is not installed"
        ((FAILED++))
    fi
}

check_file() {
    if [ -f "$1" ]; then
        echo -e "${GREEN}✓${NC} File exists: $1"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} File missing: $1"
        ((FAILED++))
    fi
}

check_directory() {
    if [ -d "$1" ]; then
        echo -e "${GREEN}✓${NC} Directory exists: $1"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} Directory missing: $1"
        ((FAILED++))
    fi
}

check_file_writable() {
    if [ -w "$1" ]; then
        echo -e "${GREEN}✓${NC} Directory writable: $1"
        ((PASSED++))
    else
        echo -e "${RED}✗${NC} Directory not writable: $1"
        ((FAILED++))
    fi
}

# System Requirements
echo "📦 System Requirements"
check_command docker
check_command docker-compose
check_command git
check_command ssh
echo ""

# Application Files
echo "📂 Application Files"
APP_PATH="${1:-.}"
check_file "$APP_PATH/.env"
check_file "$APP_PATH/docker-compose.yml"
check_file "$APP_PATH/Dockerfile"
check_directory "$APP_PATH/.github/workflows"
echo ""

# Directories
echo "📁 Directory Structure"
check_directory "$APP_PATH/storage"
check_directory "$APP_PATH/bootstrap"
check_directory "$APP_PATH/app"
echo ""

# Permissions
echo "🔐 Permissions"
check_file_writable "$APP_PATH/storage"
check_file_writable "$APP_PATH/bootstrap/cache"
echo ""

# Environment Configuration
echo "⚙️  Environment Configuration"
if grep -q "APP_ENV=production" "$APP_PATH/.env" 2>/dev/null; then
    echo -e "${GREEN}✓${NC} APP_ENV is set to production"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠${NC} APP_ENV is not production"
    ((PASSED++))
fi

if grep -q "APP_DEBUG=false" "$APP_PATH/.env" 2>/dev/null; then
    echo -e "${GREEN}✓${NC} APP_DEBUG is disabled"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} APP_DEBUG is enabled (should be false in production)"
    ((FAILED++))
fi

if grep -q "^DB_HOST=" "$APP_PATH/.env" 2>/dev/null; then
    echo -e "${GREEN}✓${NC} Database host is configured"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} Database host is not configured"
    ((FAILED++))
fi

if grep -q "^REDIS_HOST=" "$APP_PATH/.env" 2>/dev/null; then
    echo -e "${GREEN}✓${NC} Redis host is configured"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} Redis host is not configured"
    ((FAILED++))
fi
echo ""

# SSH Configuration
echo "🔑 SSH Configuration"
if [ -f ~/.ssh/authorized_keys ]; then
    echo -e "${GREEN}✓${NC} SSH authorized_keys exists"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠${NC} SSH authorized_keys not found"
    ((PASSED++))
fi

if [ -f ~/.ssh/authorized_keys ] && grep -q "github" ~/.ssh/authorized_keys 2>/dev/null; then
    echo -e "${GREEN}✓${NC} GitHub Actions SSH key found"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠${NC} GitHub Actions SSH key not found (add it for deployments)"
    ((PASSED++))
fi
echo ""

# Docker Configuration
echo "🐳 Docker Configuration"
if docker ps &>/dev/null; then
    echo -e "${GREEN}✓${NC} Docker daemon is running"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} Docker daemon is not running"
    ((FAILED++))
fi

if command -v docker-compose &>/dev/null; then
    DC_VERSION=$(docker-compose --version 2>/dev/null | grep -oP 'Docker Compose version \K[0-9.]+' || echo "unknown")
    echo -e "${GREEN}✓${NC} Docker Compose version: $DC_VERSION"
    ((PASSED++))
else
    echo -e "${RED}✗${NC} Docker Compose is not installed"
    ((FAILED++))
fi
echo ""

# Git Configuration
echo "📝 Git Configuration"
if [ -d "$APP_PATH/.git" ]; then
    echo -e "${GREEN}✓${NC} Git repository initialized"
    ((PASSED++))

    BRANCH=$(cd "$APP_PATH" && git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "unknown")
    echo -e "${GREEN}✓${NC} Current branch: $BRANCH"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠${NC} Git repository not initialized"
    ((PASSED++))
fi
echo ""

# Docker Image Requirements
echo "📥 Docker Images"
if docker image inspect email-customs:latest &>/dev/null 2>&1 || \
   docker images | grep -q email-customs; then
    echo -e "${GREEN}✓${NC} Application image exists"
    ((PASSED++))
else
    echo -e "${YELLOW}⚠${NC} Application image not found (will be built on first deploy)"
    ((PASSED++))
fi
echo ""

# Summary
echo "======================================"
echo "Results: ${GREEN}$PASSED passed${NC}, ${RED}$FAILED failed${NC}"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ All checks passed! System is ready for deployment.${NC}"
    exit 0
else
    echo -e "${RED}✗ Some checks failed. Please review the issues above.${NC}"
    exit 1
fi
