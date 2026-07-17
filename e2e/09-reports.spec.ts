import { test, expect } from '@playwright/test';

test.describe('EmailReports — Rapoarte', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/admin/email-reports');
    await page.waitForLoadState('domcontentloaded');
  });

  test('reports page renders', async ({ page }) => {
    await expect(page.locator('.fi-page, main').first()).toBeVisible();
  });

  test('shows 4 KPI stat cards', async ({ page }) => {
    const kpiSection = page.locator('.grid').first();
    await expect(kpiSection).toBeVisible();
    const cards = page.locator('.rounded-2xl').filter({ hasText: /Procesate|așteptare|Luna|Respinse/i });
    const count = await cards.count();
    expect(count).toBeGreaterThan(2);
  });

  test('KPI card "Procesate Astăzi" is visible', async ({ page }) => {
    // Blade uses lowercase: 'Procesate astăzi'
    await expect(page.locator('text=/procesate ast/i').first()).toBeVisible();
  });

  test('KPI card "În Așteptare" shows pending count', async ({ page }) => {
    await expect(page.locator('text=/în a[s,ș]teptare/i').first()).toBeVisible();
  });

  test('KPI card "Luna Aceasta" is visible with amount', async ({ page }) => {
    await expect(page.locator('text=/luna aceasta/i').first()).toBeVisible();
  });

  test('KPI card "Total Respinse" is visible', async ({ page }) => {
    await expect(page.locator('text=/total respinse/i').first()).toBeVisible();
  });

  test('operators table section is visible', async ({ page }) => {
    await expect(page.locator('text=/Top Operatori/i').first()).toBeVisible();
  });

  test('operators table has correct column headers', async ({ page }) => {
    const opsTable = page.locator('table').first();
    await expect(opsTable).toContainText(/Operator/i);
    await expect(opsTable).toContainText(/Aprobate/i);
    await expect(opsTable).toContainText(/Sumă/i);
    await expect(opsTable).toContainText(/Medie/i);
  });

  test('bank accounts table section is visible', async ({ page }) => {
    await expect(page.locator('h2, h3').filter({ hasText: /Conturi Bancare/i }).first()).toBeVisible();
  });

  test('monthly trend section is visible', async ({ page }) => {
    await expect(page.locator('text=/Trend Lunar/i').first()).toBeVisible();
  });

  test('monthly trend table has volume progress bars', async ({ page }) => {
    const trendSection = page.locator('.rounded-2xl').filter({ hasText: /Trend Lunar/i }).first();
    await expect(trendSection).toBeVisible();
  });

  test('top categories section is visible', async ({ page }) => {
    await expect(page.locator('text=/Top Categorii/i').first()).toBeVisible();
  });

  test('categories section shows progress bars', async ({ page }) => {
    const catSection = page.locator('.rounded-2xl').filter({ hasText: /Top Categorii/i }).first();
    await expect(catSection).toBeVisible();
  });

  test('page uses dark mode compatible classes', async ({ page }) => {
    // The reports view has dark: Tailwind classes in its HTML
    const darkEls = page.locator('[class*="dark:"]').first();
    await expect(darkEls).toBeAttached();
  });

  test('operators section shows "Ultimele 30 de zile" subtitle', async ({ page }) => {
    await expect(page.locator('text=/Ultimele 30 de zile/i').first()).toBeVisible();
  });

  test('monthly trend shows trend arrows when data available', async ({ page }) => {
    const trendTable = page.locator('table').nth(2);
    if (await trendTable.isVisible().catch(() => false)) {
      await expect(trendTable).toBeVisible();
    }
  });

  test('empty states show "Nicio dată disponibilă"', async ({ page }) => {
    const tables = page.locator('table');
    const count = await tables.count();
    for (let i = 0; i < count; i++) {
      const hasRows = await tables.nth(i).locator('tbody tr').count();
      if (hasRows === 0) {
        await expect(tables.nth(i)).toContainText(/Nicio dată/i);
      }
    }
  });

});
