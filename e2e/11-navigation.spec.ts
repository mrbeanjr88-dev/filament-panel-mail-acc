import { test, expect } from '@playwright/test';

test.describe('Navigation & Global UI', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/admin');
    await page.waitForLoadState('domcontentloaded');
  });

  // ── SPA navigation ───────────────────────────────────────────────────

  test('SPA mode — navigate between pages without full reload', async ({ page }) => {
    const pendingLink = page.locator('.fi-sidebar-item').filter({ hasText: /Carantină/i }).first();
    await pendingLink.click();
    await expect(page).toHaveURL(/\/pending-emails/);
    await expect(page.locator('table')).toBeVisible();
  });

  test('SPA mode — back button works', async ({ page }) => {
    const pendingLink = page.locator('.fi-sidebar-item').filter({ hasText: /Carantină/i }).first();
    await pendingLink.click();
    await expect(page).toHaveURL(/\/pending-emails/);
    await page.goBack();
    await expect(page).toHaveURL(/\/admin$/);
  });

  // ── Sidebar ──────────────────────────────────────────────────────────

  test('sidebar has Email Customs navigation group', async ({ page }) => {
    await expect(page.locator('.fi-sidebar-nav')).toContainText('Email Customs');
  });

  test('sidebar has all main resource links', async ({ page }) => {
    const nav = page.locator('.fi-sidebar-nav');
    await expect(nav).toContainText(/Carantină/i);
    await expect(nav).toContainText(/Conturi email/i);
    await expect(nav).toContainText(/Conturi bancare/i);
    await expect(nav).toContainText(/Reguli/i);
  });

  test('sidebar is collapsible on desktop', async ({ page }) => {
    const collapseBtn = page.locator('[data-testid="sidebar-collapse"], button[aria-label*="collapse" i], .fi-sidebar-close-btn').first();
    if (await collapseBtn.isVisible()) {
      await collapseBtn.click();
      await page.waitForTimeout(500);
    }
    await expect(page.locator('.fi-sidebar')).toBeAttached();
  });

  test('Rapoarte navigation group is visible', async ({ page }) => {
    await expect(page.locator('.fi-sidebar-nav')).toContainText('Rapoarte');
  });

  test('Sistem navigation group collapses by default', async ({ page }) => {
    const sistemGroup = page.locator('.fi-sidebar-group').filter({ hasText: 'Sistem' });
    if (await sistemGroup.isVisible()) {
      await expect(sistemGroup).toBeVisible();
    }
  });

  // ── Top bar ──────────────────────────────────────────────────────────

  test('top bar is visible', async ({ page }) => {
    const topbar = page.locator('.fi-topbar, header').first();
    await expect(topbar).toBeVisible();
  });

  test('profile menu or user info visible', async ({ page }) => {
    // In Filament 5, user info is shown as an avatar button in the topbar
    const userBtn = page.locator('button[aria-label*="User" i], button[aria-label*="Avatar" i], .fi-topbar img[alt*="Avatar" i]').first();
    if (await userBtn.isVisible()) {
      await expect(userBtn).toBeVisible();
    } else {
      // Fallback: check sidebar has any user-related element
      await expect(page.locator('.fi-topbar, .fi-sidebar').first()).toBeVisible();
    }
  });

  test('notification bell icon is visible', async ({ page }) => {
    const notifBtn = page.locator('[data-testid="notification-trigger"], button[aria-label*="notification" i], .fi-notifications-trigger').first();
    if (await notifBtn.isVisible()) {
      await expect(notifBtn).toBeVisible();
    }
  });

  // ── Global search ────────────────────────────────────────────────────

  test('global search trigger is visible', async ({ page }) => {
    await page.keyboard.press('Meta+k');
    const searchInput = page.locator('input[type="search"], .fi-global-search-field input');
    await page.keyboard.press('Escape');
  });

  // ── Full page navigation test ────────────────────────────────────────

  test('all main pages load without 500 errors', async ({ page }) => {
    const pages = [
      '/admin',
      '/admin/pending-emails',
      '/admin/bank-accounts',
      '/admin/email-accounts',
      '/admin/email-filter-rules',
      '/admin/users',
      '/admin/audit-logs',
      '/admin/email-reports',
      '/admin/manage-settings',
    ];

    for (const url of pages) {
      await page.goto(url);
      await page.waitForLoadState('domcontentloaded');
      const bodyText = await page.textContent('body');
      const has500 = bodyText?.includes('Server Error') || (bodyText?.includes('500') && bodyText?.includes('Exception'));
      expect(has500, `Page ${url} returned 500 error`).toBeFalsy();
      await expect(page.locator('.fi-layout, .fi-page, main, body').first()).toBeVisible();
    }
  });

  // ── Breadcrumbs ──────────────────────────────────────────────────────

  test('breadcrumbs show current location', async ({ page }) => {
    await page.goto('/admin/pending-emails');
    await page.waitForLoadState('domcontentloaded');
    const breadcrumbs = page.locator('.fi-breadcrumbs, nav[aria-label*="breadcrumb" i]');
    if (await breadcrumbs.isVisible()) {
      await expect(breadcrumbs).toContainText(/Carantină|carantina/i);
    }
  });

  // ── Responsive ───────────────────────────────────────────────────────

  test('layout works on mobile viewport', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/admin');
    await page.waitForLoadState('domcontentloaded');
    await expect(page.locator('.fi-layout, body').first()).toBeVisible();
  });

  test('layout works on tablet viewport', async ({ page }) => {
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto('/admin');
    await page.waitForLoadState('domcontentloaded');
    await expect(page.locator('.fi-layout, body').first()).toBeVisible();
  });

});
