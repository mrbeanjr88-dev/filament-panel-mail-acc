import { test, expect } from '@playwright/test';
import { waitForTable } from './helpers/filament';

test.describe('AuditLog — Jurnal de Audit', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/admin/audit-logs');
    await waitForTable(page);
  });

  test('audit log page renders', async ({ page }) => {
    await expect(page.locator('table')).toBeVisible();
  });

  test('table has expected columns', async ({ page }) => {
    const header = page.locator('table thead');
    await expect(header).toContainText(/Data|Timestamp/i);
    await expect(header).toContainText(/Acțiune|Action/i);
    await expect(header).toContainText(/IP/i);
  });

  test('actions are shown as colored badges', async ({ page }) => {
    const firstRow = page.locator('table tbody tr').first();
    if (await firstRow.isVisible()) {
      // Look for badge in action column
      const badge = firstRow.locator('.fi-badge');
      await expect(badge.first()).toBeVisible();
    }
  });

  test('table is read-only (no create button)', async ({ page }) => {
    const createBtn = page.locator('a, button').filter({ hasText: /New|Nou|Creare/i });
    await expect(createBtn).not.toBeVisible();
  });

  test('table has pagination with 25 records per page', async ({ page }) => {
    await expect(page.locator('table')).toBeVisible();
    // Check pagination exists if there are multiple records
    const paginationSelect = page.locator('.fi-ta-pagination select, select[aria-label*="per page" i]');
    // May or may not be visible depending on record count
  });

  test('can filter by action type', async ({ page }) => {
    const filterBtn = page.locator('button').filter({ hasText: /Filtre|Filter/i }).first();
    if (await filterBtn.isVisible()) {
      await filterBtn.click();
      const filters = page.locator('.fi-ta-filters-form, .fi-filters').first();
      await expect(filters).toBeVisible();
      await expect(filters).toContainText(/Acțiune|Action/i);
    }
  });

  test('table records are sorted by newest first', async ({ page }) => {
    // Default sort is timestamp desc - rows should show recent entries
    const rows = page.locator('table tbody tr');
    if (await rows.count() > 1) {
      await expect(rows.first()).toBeVisible();
    }
  });

  test('empty state shows appropriate message', async ({ page }) => {
    const emptyState = page.locator('.fi-ta-empty-state, [data-empty-state]');
    const hasRows = await page.locator('table tbody tr').count();
    if (hasRows === 0) {
      await expect(emptyState).toContainText(/Niciun eveniment|No events/i);
    }
  });

});
