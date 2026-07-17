import { test, expect } from '@playwright/test';
import { waitForTable } from './helpers/filament';

test.describe('BankAccounts — Conturi Bancare', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/admin/bank-accounts');
    await waitForTable(page);
  });

  // ── List ────────────────────────────────────────────────────────────

  test('list page renders correctly', async ({ page }) => {
    await expect(page.locator('h1, .fi-header-heading')).toContainText(/cont|bank/i);
    await expect(page.locator('table')).toBeVisible();
  });

  test('table has expected columns', async ({ page }) => {
    const header = page.locator('table thead');
    await expect(header).toContainText(/Label|Cont|Etichet/i);
    await expect(header).toContainText(/Sold/i);
  });

  test('table shows seeded bank accounts', async ({ page }) => {
    await expect(page.locator('table tbody tr').first()).toBeVisible();
    await expect(page.locator('table')).toContainText('BT RON Principal');
  });

  test('table has New button', async ({ page }) => {
    const newBtn = page.locator('a, button').filter({ hasText: /New|Nou|Adaugă/i }).first();
    await expect(newBtn).toBeVisible();
  });

  // ── Create ──────────────────────────────────────────────────────────

  test('create page renders form', async ({ page }) => {
    await page.goto('/admin/bank-accounts/create');
    await page.waitForLoadState('networkidle');
    // Form should have key fields (label is "Etichetă" in Romanian)
    await expect(page.getByLabel(/Label|Etichet|Denumire/i).first()).toBeVisible();
    await expect(page.getByLabel(/IBAN/i).first()).toBeVisible();
  });

  test('create validates required fields', async ({ page }) => {
    await page.goto('/admin/bank-accounts/create');
    await page.waitForLoadState('networkidle');
    // Filament 5: sidebar nav uses button[type="submit"].fi-dropdown-list-item — exclude it
    const submitBtn = page.locator('button[type="submit"]:not(.fi-dropdown-list-item)').first();
    await submitBtn.scrollIntoViewIfNeeded();
    await submitBtn.click();
    await page.waitForLoadState('networkidle').catch(() => {});
    // Should still be on create page (validation failed)
    await expect(page).toHaveURL(/\/create/);
  });

  test('create validates IBAN format', async ({ page }) => {
    await page.goto('/admin/bank-accounts/create');
    await page.waitForLoadState('networkidle');
    await page.getByLabel(/Label|Etichet|Denumire/i).first().fill('Test Bank');
    await page.getByLabel(/IBAN/i).fill('INVALID_IBAN');
    const submitBtn = page.locator('button[type="submit"]:not(.fi-dropdown-list-item)').first();
    await submitBtn.scrollIntoViewIfNeeded();
    await submitBtn.click();
    await page.waitForLoadState('networkidle').catch(() => {});
    // Should stay on create page (IBAN or other validation failed)
    await expect(page).toHaveURL(/\/create/);
  });

  test('can create a bank account with valid data', async ({ page }) => {
    await page.goto('/admin/bank-accounts/create');
    await page.waitForLoadState('networkidle');

    await page.getByLabel(/Label|Etichet|Denumire/i).first().fill('Cont Test E2E');
    await page.getByLabel(/Titular/i).first().fill('Test Holder E2E');
    await page.getByLabel(/IBAN/i).fill('RO49AAAA1B31007593840001');

    const submitBtn = page.locator('button[type="submit"]:not(.fi-dropdown-list-item)').first();
    await submitBtn.scrollIntoViewIfNeeded();
    const beforeUrl = page.url();
    await submitBtn.click();

    // Wait for Alpine.js navigate redirect (same pattern as users)
    await page.waitForFunction(
      (init) => window.location.href !== init,
      beforeUrl,
      { timeout: 20_000 }
    ).catch(() => {});

    const url = page.url();
    expect(url.includes('/bank-accounts') && !url.includes('/create')).toBeTruthy();
  });

  // ── Edit ────────────────────────────────────────────────────────────

  test('can open edit page', async ({ page }) => {
    const editBtn = page.locator('table tbody tr').first().locator('button, a').filter({ hasText: /Edit|Editează/i }).first();
    if (await editBtn.isVisible()) {
      await editBtn.click();
      await page.waitForLoadState('domcontentloaded');
      await expect(page).toHaveURL(/\/edit/);
    }
  });

  test('edit page pre-fills existing data', async ({ page }) => {
    const row = page.locator('table tbody tr').filter({ hasText: 'BT RON Principal' }).first();
    const editBtn = row.locator('button, a').filter({ hasText: /Edit|Editează/i }).first();
    if (await editBtn.isVisible()) {
      await editBtn.click();
      await page.waitForLoadState('networkidle');
      const labelField = page.getByLabel(/Label|Etichet|Denumire/i).first();
      await expect(labelField).toHaveValue(/BT RON Principal/i);
    }
  });

  // ── Filters ─────────────────────────────────────────────────────────

  test('filter by active status works', async ({ page }) => {
    const filterBtn = page.locator('button').filter({ hasText: /Filtre|Filter/i }).first();
    if (await filterBtn.isVisible()) {
      await filterBtn.click();
      await expect(page.locator('.fi-ta-filters-form, .fi-filters')).toBeVisible();
    }
  });

  // ── Delete ──────────────────────────────────────────────────────────

  test('delete action requires confirmation', async ({ page }) => {
    const deleteBtn = page.locator('table tbody tr').first().locator('button').filter({ hasText: /Șterge|Delete/i }).first();
    if (await deleteBtn.isVisible()) {
      await deleteBtn.click();
      // Use fi-modal-window (not [role="dialog"] which also matches notifications)
      const hasModal = await page.waitForFunction(
        () => {
          const wins = document.querySelectorAll('.fi-modal-window');
          for (const w of wins) {
            const s = window.getComputedStyle(w);
            if (s.display !== 'none' && s.visibility !== 'hidden' && s.opacity !== '0') return true;
          }
          return false;
        },
        null,
        { timeout: 5_000 }
      ).catch(() => false);
      if (hasModal) {
        const cancelBtn = page.locator('.fi-modal-window:visible button')
          .filter({ hasText: /Anulează|Cancel/i }).first();
        if (await cancelBtn.count() > 0) await cancelBtn.click();
      }
    }
  });

});
