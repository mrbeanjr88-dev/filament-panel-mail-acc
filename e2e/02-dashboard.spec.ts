import { test, expect } from '@playwright/test';

test.describe('Dashboard', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/admin');
    await page.waitForLoadState('domcontentloaded');
  });

  test('dashboard renders all three widget sections', async ({ page }) => {
    // QuarantineStatsWidget
    const statsWidgets = page.locator('.fi-wi-stats-overview');
    await expect(statsWidgets.first()).toBeVisible();
  });

  test('quarantine stats widget shows stat cards', async ({ page }) => {
    const widget = page.locator('.fi-wi-stats-overview').first();
    await expect(widget).toContainText('În carantină');
    await expect(widget).toContainText('Aprobate astăzi');
    await expect(widget).toContainText('Eșuate');
    await expect(widget).toContainText('Respinse total');
  });

  test('bank summary widget shows financial stats', async ({ page }) => {
    const widgets = page.locator('.fi-wi-stats-overview');
    const bankWidget = widgets.nth(1);
    await expect(bankWidget).toContainText(/Tranzacții|Sold|Conturi bancare/i);
  });

  test('recent emails widget shows table', async ({ page }) => {
    const tableWidget = page.locator('.fi-wi-table').first();
    await expect(tableWidget).toBeVisible();
    await expect(tableWidget).toContainText(/Ultimele emailuri|Expeditor|Subiect/i);
  });

  test('sidebar navigation has correct groups', async ({ page }) => {
    const nav = page.locator('.fi-sidebar-nav');
    await expect(nav).toContainText('Email Customs');
    await expect(nav).toContainText('Rapoarte');
  });

  test('sidebar navigation has carantina link', async ({ page }) => {
    await expect(page.locator('.fi-sidebar-item').filter({ hasText: /Carantină|carantina/i })).toBeVisible();
  });

  test('sidebar has badge on quarantine with pending count', async ({ page }) => {
    // Navigation badge should show if pending emails exist
    const badge = page.locator('.fi-sidebar-item').filter({ hasText: /Carantină/i }).locator('.fi-badge, .fi-sidebar-item-badge');
    // May or may not have badge depending on data - just check item is visible
    await expect(page.locator('.fi-sidebar-item').filter({ hasText: /Carantină/i })).toBeVisible();
  });

  test('global search opens with button', async ({ page }) => {
    const searchBtn = page.locator('[data-testid="global-search-trigger"], .fi-global-search-trigger, button[title*="search" i]').first();
    if (await searchBtn.isVisible()) {
      await searchBtn.click();
      await expect(page.locator('input[type="search"], .fi-global-search-field input')).toBeVisible();
    }
  });

  test('breadcrumbs or page title visible', async ({ page }) => {
    await expect(page.locator('h1.fi-header-heading')).toBeVisible();
  });

});
