import { Page, expect, Locator } from '@playwright/test';

/** Navigate via sidebar link by label text */
export async function navTo(page: Page, label: string) {
  const link = page.locator('.fi-sidebar-item').filter({ hasText: label }).first();
  await link.click();
  await page.waitForLoadState('domcontentloaded');
  await page.locator('main, .fi-main, .fi-body').first().waitFor({ state: 'visible', timeout: 10_000 }).catch(() => {});
}

/** Wait for Filament table to finish loading.
 *  NOTE: networkidle is avoided because Livewire polls every 30s, keeping network busy.
 */
export async function waitForTable(page: Page) {
  await page.waitForLoadState('domcontentloaded');
  // Wait for the table element specifically instead of networkidle
  await page.locator('table, .fi-ta-empty-state').first().waitFor({ state: 'visible', timeout: 15_000 }).catch(() => {});
}

/** Get a table row by a cell text match */
export async function getTableRow(page: Page, cellText: string): Promise<Locator> {
  return page.locator('table tbody tr').filter({ hasText: cellText }).first();
}

/** Click a row action button by its label (tooltip or aria-label) */
export async function clickRowAction(page: Page, rowText: string, actionLabel: string) {
  const row = await getTableRow(page, rowText);
  const btn = row.getByRole('button', { name: new RegExp(actionLabel, 'i') });
  await btn.click();
}

/** Fill a Filament form field by label */
export async function fillField(page: Page, label: string, value: string) {
  const field = page.getByLabel(label, { exact: false });
  await field.fill(value);
}

/** Select an option in a Filament Select component by label + option text */
export async function selectOption(page: Page, label: string, option: string) {
  const wrapper = page.locator('.fi-fo-field-wrp').filter({ hasText: label }).first();
  await wrapper.click();
  await page.getByRole('option', { name: option }).click();
}

/** Click the primary page action button by name */
export async function clickAction(page: Page, name: string) {
  await page.getByRole('button', { name: new RegExp(name, 'i') }).first().click();
}

/** Submit the Filament form (Create/Save) */
export async function submitForm(page: Page) {
  const btn = page.getByRole('button', { name: /create|save|salveaz|creare/i }).first();
  await btn.click();
  await page.waitForLoadState('domcontentloaded');
}

/** Wait for and dismiss a Filament success notification */
export async function expectSuccess(page: Page, text?: string | RegExp) {
  const selector = text
    ? page.locator('[role="status"], .fi-no-notification-banner').filter({ hasText: text })
    : page.locator('[role="status"].fi-color-success, .fi-no-notification-banner').first();
  await expect(selector).toBeVisible({ timeout: 12_000 });
}

/** Check that table has at least N rows */
export async function expectTableRows(page: Page, min: number) {
  await expect(page.locator('table tbody tr')).toHaveCountGreaterThan(min - 1);
}
