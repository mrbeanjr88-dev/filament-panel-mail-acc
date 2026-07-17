import { Page, expect } from '@playwright/test';

export const CREDENTIALS = {
  email: 'admin@test.com',
  password: 'password123',
};

export async function login(page: Page, email = CREDENTIALS.email, password = CREDENTIALS.password) {
  await page.goto('/admin/login');
  // Use 'load' to ensure all scripts (Livewire, Alpine.js) are initialized
  await page.waitForLoadState('load');

  // Wait for Livewire to be ready
  await page.waitForFunction(() => typeof (window as any).Livewire !== 'undefined', { timeout: 10_000 }).catch(() => {});

  await page.getByLabel('Email address').fill(email);
  // Password: use input type=password directly to avoid strict mode (show/hide buttons also match getByLabel)
  await page.locator('input[type="password"]').first().fill(password);

  // Start watching for URL change BEFORE clicking (arg must be null when passing options)
  const urlChanged = page.waitForFunction(
    () => !window.location.href.includes('/login'),
    null,
    { timeout: 20_000, polling: 100 }
  );

  await page.getByRole('button', { name: /sign in/i }).click();

  await urlChanged;
  await page.waitForLoadState('domcontentloaded');
}

export async function logout(page: Page) {
  const userMenu = page.locator('[data-testid="user-menu"], .fi-topbar-user-menu, .fi-sidebar-user-menu').first();
  if (await userMenu.isVisible()) {
    await userMenu.click();
    const logoutBtn = page.getByRole('menuitem', { name: /sign out|logout|deconect/i });
    if (await logoutBtn.isVisible()) await logoutBtn.click();
  }
}

export async function waitForNotification(page: Page, text: string | RegExp) {
  const textFilter = typeof text === 'string' ? text : undefined;
  await expect(
    page.locator('.fi-no-notification-banner, [role="alert"]').filter({ hasText: textFilter ?? '' })
  ).toBeVisible({ timeout: 10_000 });
}
