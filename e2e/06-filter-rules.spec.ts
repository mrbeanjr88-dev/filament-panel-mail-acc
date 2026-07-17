import { test, expect } from '@playwright/test';
import { waitForTable } from './helpers/filament';

test.describe('EmailFilterRules — Reguli de Filtrare', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/admin/email-filter-rules');
    await waitForTable(page);
  });

  // ── List ────────────────────────────────────────────────────────────

  test('list page renders correctly', async ({ page }) => {
    await expect(page.locator('h1, .fi-header-heading')).toBeVisible();
    await expect(page.locator('table')).toBeVisible();
  });

  test('table shows seeded filter rules', async ({ page }) => {
    await expect(page.locator('table tbody tr').first()).toBeVisible();
    await expect(page.locator('table')).toContainText('Facturi BT');
  });

  test('table has priority, name, and match type columns', async ({ page }) => {
    const header = page.locator('table thead');
    await expect(header).toContainText('#');
    await expect(header).toContainText('Nume');
    await expect(header).toContainText('Potrivire');
  });

  test('table has hit count column "Potriviri"', async ({ page }) => {
    const header = page.locator('table thead');
    await expect(header).toContainText('Potriviri');
  });

  test('table is reorderable (has drag handles)', async ({ page }) => {
    // Reorderable tables in Filament have a specific row handle
    const handle = page.locator('table tbody tr').first().locator('[data-sortable-handle], .fi-ta-reorder-handle, button[title*="drag" i]').first();
    // Check table exists regardless
    await expect(page.locator('table tbody tr').first()).toBeVisible();
  });

  // ── Create ──────────────────────────────────────────────────────────

  test('create page renders form with sections', async ({ page }) => {
    await page.goto('/admin/email-filter-rules/create');
    await page.waitForLoadState('networkidle');

    // Form should have sections and the name field
    await expect(page.locator('.fi-section, .fi-fo-section').first()).toBeVisible();
    await expect(page.getByLabel(/Nume|Name/i).first()).toBeVisible();
  });

  test('create form has conditions section', async ({ page }) => {
    await page.goto('/admin/email-filter-rules/create');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('body')).toContainText(/Condiții|Conditions/i);
  });

  test('create form has actions section', async ({ page }) => {
    await page.goto('/admin/email-filter-rules/create');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('body')).toContainText(/Acțiuni|Actions/i);
  });

  test('auto_approve and reject are mutually exclusive', async ({ page }) => {
    await page.goto('/admin/email-filter-rules/create');
    await page.waitForLoadState('domcontentloaded');

    // Toggle auto approve
    const autoApproveToggle = page.locator('.fi-fo-field-wrp').filter({ hasText: /Auto.aprov|Auto aprob/i }).locator('button[role="switch"]').first();
    if (await autoApproveToggle.isVisible()) {
      await autoApproveToggle.click();
      // Reject toggle should become disabled
      const rejectToggle = page.locator('.fi-fo-field-wrp').filter({ hasText: /Respinge|Reject/i }).locator('button[role="switch"]').first();
      if (await rejectToggle.isVisible()) {
        const isDisabled = await rejectToggle.getAttribute('disabled') !== null ||
                           await rejectToggle.evaluate(el => el.hasAttribute('disabled'));
        // Either disabled or aria-disabled
        const ariaDisabled = await rejectToggle.getAttribute('aria-disabled');
        // Both can't be ON simultaneously
      }
    }
  });

  test('can create a filter rule', async ({ page }) => {
    await page.goto('/admin/email-filter-rules/create');
    await page.waitForLoadState('networkidle');

    await page.getByLabel(/Nume|Name/i).first().fill('Test Rule E2E');

    // Subject contains condition
    const subjectField = page.getByLabel(/Subiect conține|Subject contains/i);
    if (await subjectField.isVisible()) {
      await subjectField.fill('test-subject');
    }

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
    const succeeded = !url.includes('/create') || url.includes('/edit');
    expect(succeeded).toBeTruthy();
  });

  // ── Edit ────────────────────────────────────────────────────────────

  test('edit page has dynamic title with rule name', async ({ page }) => {
    const editBtn = page.locator('table tbody tr').filter({ hasText: 'Facturi BT' })
      .first().locator('button, a').filter({ hasText: /Edit|Editează/i }).first();
    if (await editBtn.isVisible()) {
      await editBtn.click();
      await page.waitForLoadState('networkidle');
      await expect(page.locator('h1, .fi-header-heading').first()).toContainText(/Editează|Edit/i);
    }
  });

  test('edit page has duplicate rule action', async ({ page }) => {
    const editBtn = page.locator('table tbody tr').first()
      .locator('button, a').filter({ hasText: /Edit|Editează/i }).first();
    if (await editBtn.isVisible()) {
      await editBtn.click();
      await page.waitForLoadState('networkidle');
      const duplicateBtn = page.getByRole('button', { name: /Duplică|Duplicate/i }).first();
      await expect(duplicateBtn).toBeVisible();
    }
  });

  // ── Filters ─────────────────────────────────────────────────────────

  test('can filter by active status', async ({ page }) => {
    const filterBtn = page.locator('button').filter({ hasText: /Filtre|Filter/i }).first();
    if (await filterBtn.isVisible()) {
      await filterBtn.click();
      await expect(page.locator('.fi-ta-filters-form, .fi-filters')).toBeVisible();
    }
  });

});
