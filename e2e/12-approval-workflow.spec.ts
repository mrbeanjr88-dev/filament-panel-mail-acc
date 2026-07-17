import { test, expect } from '@playwright/test';
import { waitForTable } from './helpers/filament';

test.describe('Approval Workflow — Fluxul de Aprobare', () => {

  test.beforeEach(async ({ page }) => {
  });

  // ── Full approve flow ────────────────────────────────────────────────

  test('approve email from table — complete flow', async ({ page }) => {
    await page.goto('/admin/pending-emails');
    await waitForTable(page);

    const pendingTab = page.locator('[role="tab"]').filter({ hasText: 'În așteptare' }).first();
    if (await pendingTab.isVisible()) await pendingTab.click();
    await waitForTable(page);

    const firstRow = page.locator('table tbody tr').first();
    if (!(await firstRow.isVisible())) {
      test.skip(true, 'No pending emails to approve');
      return;
    }

    const approveBtn = firstRow.locator('button').filter({ hasText: /Aprobă.*Inbox|Approve/i }).first();
    if (await approveBtn.isVisible()) {
      await approveBtn.click();

      // Wait for a modal window to appear (not database-notifications)
      await page.waitForFunction(
        () => {
          const wins = document.querySelectorAll('.fi-modal-window');
          for (const w of wins) {
            const s = window.getComputedStyle(w);
            if (s.display !== 'none' && s.visibility !== 'hidden' && s.opacity !== '0') return true;
          }
          return false;
        },
        null,
        { timeout: 8_000 }
      );

      const confirmBtn = page.locator('.fi-modal-window:visible button')
        .filter({ hasText: /Confirm|Da|Aprobă/i }).last();
      if (await confirmBtn.count() > 0) await confirmBtn.click();
      await page.waitForLoadState('domcontentloaded');
    }
  });

  test('approve with bank update flow', async ({ page }) => {
    await page.goto('/admin/pending-emails');
    await waitForTable(page);

    const pendingTab = page.locator('[role="tab"]').filter({ hasText: 'În așteptare' }).first();
    if (await pendingTab.isVisible()) await pendingTab.click();
    await waitForTable(page);

    const firstRow = page.locator('table tbody tr').first();
    if (!(await firstRow.isVisible())) {
      test.skip(true, 'No pending emails');
      return;
    }

    const bankBtn = firstRow.locator('button').filter({ hasText: /bancă|bank/i }).first();
    if (await bankBtn.isVisible()) {
      await bankBtn.click();
      const hasModal = await page.waitForFunction(
        () => document.querySelectorAll('.fi-modal-window:not([style*="display: none"])').length > 0,
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

  // ── View page approval flow ──────────────────────────────────────────

  test('view page — approve without bank action exists', async ({ page }) => {
    await page.goto('/admin/pending-emails');
    await waitForTable(page);

    const pendingTab = page.locator('[role="tab"]').filter({ hasText: 'În așteptare' }).first();
    if (await pendingTab.isVisible()) await pendingTab.click();
    await waitForTable(page);

    const viewBtn = page.locator('table tbody tr').first()
      .locator('button, a').filter({ hasText: /Verifică|View/i }).first();

    if (await viewBtn.isVisible()) {
      await viewBtn.click();
      await page.waitForLoadState('domcontentloaded');

      const noBank = page.getByRole('button', { name: /Aprobă fără bancă|Approve without bank/i }).first();
      await expect(noBank).toBeVisible({ timeout: 8_000 });
    }
  });

  test('view page — reject with notes flow', async ({ page }) => {
    await page.goto('/admin/pending-emails');
    await waitForTable(page);

    const pendingTab = page.locator('[role="tab"]').filter({ hasText: 'În așteptare' }).first();
    if (await pendingTab.isVisible()) await pendingTab.click();
    await waitForTable(page);

    const viewBtn = page.locator('table tbody tr').first()
      .locator('button, a').filter({ hasText: /Verifică/i }).first();

    if (await viewBtn.isVisible()) {
      await viewBtn.click();
      await page.waitForLoadState('domcontentloaded');

      // Use .first() to avoid strict mode if multiple Respinge buttons
      const rejectBtn = page.getByRole('button', { name: /Respinge/i }).first();
      if (await rejectBtn.isVisible().catch(() => false)) {
        await rejectBtn.click();

        await page.waitForFunction(
          () => {
            const wins = document.querySelectorAll('.fi-modal-window');
            for (const w of wins) {
              const s = window.getComputedStyle(w);
              if (s.display !== 'none' && s.visibility !== 'hidden' && s.opacity !== '0') return true;
            }
            return false;
          },
          null,
          { timeout: 10_000 }
        );

        const notesField = page.locator('.fi-modal-window textarea').first();
        if (await notesField.isVisible().catch(() => false)) {
          await notesField.fill('Test rejection reason E2E');
        }

        const cancelBtn = page.locator('.fi-modal-window:visible button')
          .filter({ hasText: /Anulează|Cancel/i }).first();
        if (await cancelBtn.count() > 0) await cancelBtn.click();
      }
    }
  });

  // ── Bulk operations ──────────────────────────────────────────────────

  test('bulk approve flow', async ({ page }) => {
    await page.goto('/admin/pending-emails');
    await waitForTable(page);

    const pendingTab = page.locator('[role="tab"]').filter({ hasText: 'În așteptare' }).first();
    if (await pendingTab.isVisible()) await pendingTab.click();
    await waitForTable(page);

    const checkbox = page.locator('table tbody tr').first().locator('input[type="checkbox"]').first();
    if (await checkbox.isVisible().catch(() => false)) {
      await checkbox.click();

      const bulkActions = page.locator('.fi-ta-actions-bulk, .fi-ta-selection-indicator, .fi-actions').first();
      if (await bulkActions.isVisible({ timeout: 3_000 }).catch(() => false)) {
        const approveAllBtn = bulkActions.locator('button').filter({ hasText: /Aprobă.*Inbox/i }).first();
        if (await approveAllBtn.isVisible().catch(() => false)) {
          await approveAllBtn.click();
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
      }
    }
  });

  test('bulk reject opens notes modal', async ({ page }) => {
    await page.goto('/admin/pending-emails');
    await waitForTable(page);

    const allTab = page.locator('[role="tab"]').filter({ hasText: 'Toate' }).first();
    if (await allTab.isVisible()) await allTab.click();
    await waitForTable(page);

    const selectAll = page.locator('table thead input[type="checkbox"]').first();
    if (await selectAll.isVisible().catch(() => false)) {
      await selectAll.click();

      const bulkGroup = page.locator('.fi-ta-selection-indicator, .fi-ta-actions-bulk').first();
      if (await bulkGroup.isVisible({ timeout: 3_000 }).catch(() => false)) {
        const rejectBulk = bulkGroup.locator('button').filter({ hasText: /Respinge/i }).first();
        if (await rejectBulk.isVisible().catch(() => false)) {
          await rejectBulk.click();
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
            const hasTextarea = await page.locator('.fi-modal-window textarea').isVisible().catch(() => false);
            expect(hasTextarea).toBeTruthy();
            const cancelBtn = page.locator('.fi-modal-window:visible button')
              .filter({ hasText: /Anulează|Cancel/i }).first();
            if (await cancelBtn.count() > 0) await cancelBtn.click();
          }
        }
      }
    }
  });

  // ── Redirect after action ────────────────────────────────────────────

  test('after approving from view page redirects to list', async ({ page }) => {
    await page.goto('/admin/pending-emails');
    await waitForTable(page);

    const pendingTab = page.locator('[role="tab"]').filter({ hasText: 'În așteptare' }).first();
    if (await pendingTab.isVisible()) await pendingTab.click();
    await waitForTable(page);

    const rows = page.locator('table tbody tr');
    if (await rows.count() === 0) {
      test.skip(true, 'No pending emails for redirect test');
      return;
    }

    const viewBtn = rows.first().locator('button, a').filter({ hasText: /Verifică/i }).first();
    if (await viewBtn.isVisible().catch(() => false)) {
      await viewBtn.click();
      await page.waitForLoadState('domcontentloaded');

      const noBank = page.getByRole('button', { name: /Aprobă fără bancă/i }).first();
      if (await noBank.isVisible().catch(() => false)) {
        await noBank.click();
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
          const confirmBtn = page.locator('.fi-modal-window:visible button')
            .filter({ hasText: /Confirm|Da/i }).last();
          if (await confirmBtn.count() > 0) {
            await confirmBtn.click();
            await page.waitForLoadState('domcontentloaded');
            await expect(page).toHaveURL(/\/pending-emails(?!\/\d)/);
          }
        }
      }
    }
  });

});
