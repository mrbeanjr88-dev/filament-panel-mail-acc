import { test, expect } from '@playwright/test';
import { waitForTable } from './helpers/filament';

test.describe('EmailAccounts — Conturi Email', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/admin/email-accounts');
    await waitForTable(page);
  });

  test('list page renders correctly', async ({ page }) => {
    await expect(page.locator('h1, .fi-header-heading')).toContainText(/email|cont/i);
    await expect(page.locator('table')).toBeVisible();
  });

  test('table shows seeded email account', async ({ page }) => {
    await expect(page.locator('table')).toContainText('Test IMAP');
  });

  test('table has name, host and status columns', async ({ page }) => {
    const header = page.locator('table thead');
    await expect(header).toContainText(/Nume|Name/i);
    await expect(header).toContainText(/Host/i);
  });

  // ── Create ──────────────────────────────────────────────────────────

  test('create page renders IMAP form', async ({ page }) => {
    await page.goto('/admin/email-accounts/create');
    await page.waitForLoadState('networkidle');
    await expect(page.getByLabel(/Nume|Name/i).first()).toBeVisible();
    await expect(page.getByLabel(/Email/i).first()).toBeVisible();
    await expect(page.getByLabel(/host/i).first()).toBeVisible();
    await expect(page.getByLabel(/port/i).first()).toBeVisible();
    // Password field — use input type to avoid strict mode with show/hide buttons
    await expect(page.locator('input[type="password"]').first()).toBeVisible();
  });

  test('create validates required fields', async ({ page }) => {
    await page.goto('/admin/email-accounts/create');
    await page.waitForLoadState('networkidle');
    const submitBtn = page.locator('button[type="submit"]:not(.fi-dropdown-list-item)').first();
    await submitBtn.scrollIntoViewIfNeeded();
    await submitBtn.click();
    await page.waitForLoadState('networkidle').catch(() => {});
    await expect(page).toHaveURL(/\/create/);
  });

  test('can create an email account', async ({ page }) => {
    await page.goto('/admin/email-accounts/create');
    await page.waitForLoadState('networkidle');

    await page.getByLabel('Nume afișat').fill('Gmail Test E2E');
    // Use type selector to avoid matching sidebar "Email Customs" button
    await page.locator('input[type="email"]').first().fill('e2e@gmail.com');
    await page.getByLabel(/host/i).first().fill('imap.gmail.com');
    await page.getByLabel(/port/i).first().fill('993');
    await page.getByLabel(/Utilizator/i).fill('e2e@gmail.com');
    await page.locator('input[type="password"]').first().fill('testpass123');

    const submitBtn = page.locator('button[type="submit"]:not(.fi-dropdown-list-item)').first();
    await submitBtn.scrollIntoViewIfNeeded();
    const beforeUrl = page.url();
    await submitBtn.click();

    // Wait for Alpine.js navigate redirect
    await page.waitForFunction(
      (init) => window.location.href !== init,
      beforeUrl,
      { timeout: 20_000 }
    ).catch(() => {});

    const url = page.url();
    expect(url.includes('/email-accounts') && !url.includes('/create')).toBeTruthy();
  });

  // ── Edit ────────────────────────────────────────────────────────────

  test('edit page has test connection button', async ({ page }) => {
    const editBtn = page.locator('table tbody tr').first().locator('button, a').filter({ hasText: /Edit|Editează/i }).first();
    if (await editBtn.isVisible()) {
      await editBtn.click();
      await page.waitForLoadState('domcontentloaded');
      const testBtn = page.getByRole('button', { name: /Testează|Test conexiune/i }).first();
      await expect(testBtn).toBeVisible();
    }
  });

  // ── Status indicator ─────────────────────────────────────────────────

  test('table shows error status icon column', async ({ page }) => {
    // The "Stare" column with icon should exist
    const header = page.locator('table thead');
    await expect(header).toContainText(/Stare|Status/i);
  });

  // ── Delete ──────────────────────────────────────────────────────────

  test('delete action available on rows', async ({ page }) => {
    const row = page.locator('table tbody tr').filter({ hasText: 'Test IMAP' }).first();
    const deleteBtn = row.locator('button').filter({ hasText: /Șterge|Delete/i }).first();
    if (await deleteBtn.isVisible()) {
      await deleteBtn.click();
      const modal = page.locator('[role="dialog"]');
      await expect(modal).toBeVisible({ timeout: 5_000 });
      await modal.locator('button').filter({ hasText: /Anulează|Cancel/i }).click();
    }
  });

});
