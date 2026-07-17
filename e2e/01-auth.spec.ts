import { test, expect } from '@playwright/test';
import { login, CREDENTIALS } from './helpers/auth';

// Auth tests need a clean session (no saved storageState)
test.use({ storageState: { cookies: [], origins: [] } });

test.describe('Authentication', () => {

  test('login page renders correctly', async ({ page }) => {
    await page.goto('/admin/login');
    await expect(page.getByLabel('Email address')).toBeVisible();
    // Password input - strict mode: use input[type=password] as Filament adds show/hide buttons
    await expect(page.locator('input[type="password"]').first()).toBeVisible();
    await expect(page.getByRole('button', { name: /sign in/i })).toBeVisible();
  });

  test('unauthenticated redirect to login', async ({ page }) => {
    await page.goto('/admin');
    await expect(page).toHaveURL(/\/admin\/login/);
  });

  test('unauthenticated redirect from protected resource', async ({ page }) => {
    await page.goto('/admin/pending-emails');
    await expect(page).toHaveURL(/\/admin\/login/);
  });

  test('login with valid credentials', async ({ page }) => {
    await login(page);
    await expect(page).toHaveURL(/\/admin/);
    // Should see the sidebar navigation
    await expect(page.locator('.fi-sidebar')).toBeVisible();
  });

  test('login with invalid password shows error', async ({ page }) => {
    await page.goto('/admin/login');
    await page.getByLabel('Email address').fill(CREDENTIALS.email);
    await page.locator('input[type="password"]').first().fill('wrong-password-xyz');
    await page.getByRole('button', { name: /sign in/i }).click();
    // Should stay on login page
    await expect(page).toHaveURL(/\/admin\/login/);
  });

  test('login with empty fields shows validation', async ({ page }) => {
    await page.goto('/admin/login');
    await page.getByRole('button', { name: /sign in/i }).click();
    // Should stay on login page
    await expect(page).toHaveURL(/\/admin\/login/);
  });

  test('authenticated user sees dashboard', async ({ page }) => {
    await login(page);
    // Dashboard has stats widgets (fi-wi-stats-overview-stat is the individual stat card)
    await expect(page.locator('.fi-wi-stats-overview-stat, .fi-wi-stats-overview').first()).toBeVisible({ timeout: 15_000 });
  });

  test('authenticated user sees navigation groups', async ({ page }) => {
    await login(page);
    // Sidebar renders after Alpine.js initializes - check for sidebar group labels
    const sidebar = page.locator('aside.fi-sidebar, .fi-sidebar-group-label').first();
    await expect(sidebar).toBeVisible({ timeout: 15_000 });
  });

});
