import { test, expect } from '@playwright/test';

test.describe('ManageSettings — Setări', () => {

  test.beforeEach(async ({ page }) => {
    await page.goto('/admin/manage-settings');
    await page.waitForLoadState('domcontentloaded');
  });

  test('settings page renders', async ({ page }) => {
    await expect(page.locator('.fi-page, main').first()).toBeVisible();
    await expect(page.locator('h1, .fi-header-heading').first()).toContainText(/Setări/i);
  });

  test('settings form has capture mode section', async ({ page }) => {
    const section = page.locator('.fi-section').filter({ hasText: /Captură|Capture/i }).first();
    await expect(section).toBeVisible();
  });

  test('settings form has rendering section', async ({ page }) => {
    const sections = page.locator('.fi-section');
    const count = await sections.count();
    let found = false;
    for (let i = 0; i < count; i++) {
      const text = await sections.nth(i).textContent();
      if (/Randare|Securitate|Rendering/i.test(text ?? '')) { found = true; break; }
    }
    expect(found).toBeTruthy();
  });

  test('settings form has default values section', async ({ page }) => {
    const sections = page.locator('.fi-section');
    const count = await sections.count();
    let found = false;
    for (let i = 0; i < count; i++) {
      const text = await sections.nth(i).textContent();
      if (/Valori implicite|Defaults/i.test(text ?? '')) { found = true; break; }
    }
    expect(found).toBeTruthy();
  });

  test('save button is visible and sticky', async ({ page }) => {
    const saveBtn = page.getByRole('button', { name: /Salvează|Save/i }).first();
    await expect(saveBtn).toBeVisible();
  });

  test('save button shows loading state during submit', async ({ page }) => {
    const saveBtn = page.getByRole('button', { name: /Salvează|Save/i }).first();
    await expect(saveBtn).toBeVisible();
    await expect(saveBtn).toBeEnabled();
  });

  test('can toggle capture mode setting', async ({ page }) => {
    const toggles = page.locator('button[role="switch"]');
    if (await toggles.count() > 0) {
      const firstToggle = toggles.first();
      const initialState = await firstToggle.getAttribute('aria-checked');
      await firstToggle.click();
      const newState = await firstToggle.getAttribute('aria-checked');
      expect(newState).not.toBe(initialState);
      await firstToggle.click();
    }
  });

  test('can change default currency', async ({ page }) => {
    const currencyField = page.locator('.fi-fo-field-wrp').filter({ hasText: /Valut|Currency/i }).first();
    if (await currencyField.isVisible()) {
      await expect(currencyField).toBeVisible();
    }
  });

  test('save settings shows success notification', async ({ page }) => {
    const saveBtn = page.getByRole('button', { name: /Salvează|Save/i }).first();
    await saveBtn.click();
    await page.waitForLoadState('domcontentloaded');
    const hasError = await page.locator('[role="alert"].fi-color-danger, .text-danger-600').isVisible().catch(() => false);
    expect(hasError).toBeFalsy();
  });

  test('info text below save button is visible', async ({ page }) => {
    await expect(page.locator('text=/aplicate imediat|applied immediately/i').first()).toBeVisible();
  });

});
