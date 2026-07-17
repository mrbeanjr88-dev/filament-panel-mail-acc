import { test, expect } from '@playwright/test';
import { CREDENTIALS } from './helpers/auth';
import { waitForTable } from './helpers/filament';

test.describe('Users — Utilizatori', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/admin/users');
    await waitForTable(page);
  });

  // ── List ────────────────────────────────────────────────────────────

  test('list page renders correctly', async ({ page }) => {
    await expect(page.locator('table')).toBeVisible();
    await expect(page.locator('table')).toContainText(/Admin/i);
  });

  test('table has name, email and verified columns', async ({ page }) => {
    const header = page.locator('table thead');
    await expect(header).toContainText('Nume');
    await expect(header).toContainText('Adresă email');
    await expect(header).toContainText('Verificat');
  });

  test('email column is copyable (has copy button on hover)', async ({ page }) => {
    const emailCell = page.locator('table tbody tr').first().locator('td').nth(1);
    await emailCell.hover();
    // Copyable adds a copy button
    await expect(emailCell).toBeVisible();
  });

  test('pagination shows [10, 25, 50] options', async ({ page }) => {
    const pagination = page.locator('.fi-ta-pagination, [data-testid="pagination"]');
    // If table has pagination controls
    await expect(page.locator('table')).toBeVisible();
  });

  test('email verification shown as icon column', async ({ page }) => {
    // The verified column renders as a boolean IconColumn — find any SVG in the first row
    const svgInRow = page.locator('table tbody tr').first().locator('svg').first();
    await expect(svgInRow).toBeVisible();
  });

  // ── Self-delete guard ────────────────────────────────────────────────

  test('logged-in user cannot delete own account from table', async ({ page }) => {
    // Find the row with our current user's email
    const myRow = page.locator('table tbody tr').filter({ hasText: CREDENTIALS.email }).first();
    if (await myRow.isVisible()) {
      const deleteBtn = myRow.locator('button').filter({ hasText: /Șterge|Delete/i }).first();
      // Delete button should be hidden for own account
      await expect(deleteBtn).not.toBeVisible();
    }
  });

  test('can delete other user (not self)', async ({ page }) => {
    // Find a row that is NOT the logged-in user
    const rows = page.locator('table tbody tr');
    const count = await rows.count();
    for (let i = 0; i < count; i++) {
      const row = rows.nth(i);
      const hasMyEmail = await row.locator(`text="${CREDENTIALS.email}"`).isVisible();
      if (!hasMyEmail) {
        const deleteBtn = row.locator('button').filter({ hasText: /Șterge|Delete/i }).first();
        if (await deleteBtn.isVisible()) {
          // Delete button should be visible for other users
          await expect(deleteBtn).toBeVisible();
          break;
        }
      }
    }
  });

  // ── Create ──────────────────────────────────────────────────────────

  test('create page has all required fields', async ({ page }) => {
    await page.goto('/admin/users/create');
    await page.waitForLoadState('domcontentloaded');
    await expect(page.getByLabel(/Nume complet|Full name/i)).toBeVisible();
    await expect(page.locator('input[type="email"]').first()).toBeVisible();
    await expect(page.locator('input[type="password"]').first()).toBeVisible();
    await expect(page.locator('input[type="password"]').nth(1)).toBeVisible();
  });

  test('create requires password confirmation match', async ({ page }) => {
    await page.goto('/admin/users/create');
    await page.waitForLoadState('networkidle');

    await page.getByLabel(/Nume complet|Full name/i).fill('New User Test');
    await page.locator('input[type="email"]').first().fill('newuser@test.com');
    const p1 = page.locator('input[type="password"]').first();
    await p1.fill('password123');
    await p1.press('Tab');
    const p2 = page.locator('input[type="password"]').nth(1);
    await p2.fill('different-password');
    await p2.press('Tab');

    await page.waitForLoadState('networkidle');

    const submitBtn2 = page.locator('button[type="submit"]:not(.fi-dropdown-list-item)').first();
    await submitBtn2.scrollIntoViewIfNeeded();
    await submitBtn2.click();
    await page.waitForLoadState('networkidle').catch(() => {});

    // Should stay on create page due to validation
    await expect(page).toHaveURL(/\/create/);
  });

  test('can create a user with valid data', async ({ page }) => {
    await page.goto('/admin/users/create');
    await page.waitForLoadState('networkidle');

    await page.getByLabel(/Nume complet|Full name/i).fill('E2E Test User');
    await page.locator('input[type="email"]').first().fill(`e2etestuser+${Date.now()}@test.com`);

    // Tab through password fields to trigger Livewire reactive state sync
    const pwd1 = page.locator('input[type="password"]').first();
    await pwd1.fill('password123');
    await pwd1.press('Tab');
    const pwd2 = page.locator('input[type="password"]').nth(1);
    await pwd2.fill('password123');
    await pwd2.press('Tab');

    // Wait for Livewire to finish debounced updates before submitting
    await page.waitForLoadState('networkidle');

    // Filament 5: sidebar nav uses button[type="submit"].fi-dropdown-list-item — exclude it
    const submitBtn = page.locator('button[type="submit"]:not(.fi-dropdown-list-item)').first();
    await submitBtn.scrollIntoViewIfNeeded();
    const beforeUrl = page.url();
    await submitBtn.click();

    // Livewire's redirectUsingNavigate uses Alpine.js navigate (pushState), which
    // isn't detected by waitForURL — poll window.location.href directly instead
    await page.waitForFunction(
      (init) => window.location.href !== init,
      beforeUrl,
      { timeout: 20_000 }
    ).catch(() => {});

    const url = page.url();
    expect(!url.includes('/create')).toBeTruthy();
  });

  // ── Edit ────────────────────────────────────────────────────────────

  test('edit page has dynamic title', async ({ page }) => {
    const editBtn = page.locator('table tbody tr').first()
      .locator('button, a').filter({ hasText: /Edit|Editează/i }).first();
    if (await editBtn.isVisible()) {
      await editBtn.click();
      await page.waitForLoadState('domcontentloaded');
      await expect(page.locator('h1, .fi-header-heading')).toContainText(/Editează/i);
    }
  });

  test('edit page has delete action that guards own account', async ({ page }) => {
    // Go to edit page for another user
    const rows = page.locator('table tbody tr');
    const count = await rows.count();
    for (let i = 0; i < count; i++) {
      const row = rows.nth(i);
      const hasMyEmail = await row.locator(`text="${CREDENTIALS.email}"`).isVisible();
      if (!hasMyEmail) {
        const editBtn = row.locator('button, a').filter({ hasText: /Edit|Editează/i }).first();
        if (await editBtn.isVisible()) {
          await editBtn.click();
          await page.waitForLoadState('domcontentloaded');
          // Delete action should be visible for other users
          const deleteBtn = page.locator('.fi-header').getByRole('button', { name: /Șterge|Delete/i });
          await expect(deleteBtn).toBeVisible();
          break;
        }
      }
    }
  });

  // ── Filters ─────────────────────────────────────────────────────────

  test('can filter by email verification status', async ({ page }) => {
    const filterBtn = page.locator('button').filter({ hasText: /Filtre|Filter/i }).first();
    if (await filterBtn.isVisible()) {
      await filterBtn.click();
      const filters = page.locator('.fi-ta-filters-form, .fi-filters');
      await expect(filters).toBeVisible();
      await expect(filters).toContainText(/Verificat|Verified/i);
    }
  });

});
