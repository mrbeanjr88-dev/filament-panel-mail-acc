import { chromium, FullConfig } from '@playwright/test';

async function globalSetup(config: FullConfig) {
  const { baseURL } = config.projects[0].use;
  const browser = await chromium.launch();
  const page = await browser.newPage();

  await page.goto(`${baseURL}/admin/login`);
  await page.waitForLoadState('load');

  // Wait for Livewire to initialize
  await page.waitForFunction(() => typeof (window as any).Livewire !== 'undefined', { timeout: 10_000 }).catch(() => {});

  await page.getByLabel('Email address').fill('admin@test.com');
  await page.locator('input[type="password"]').first().fill('password123');

  const urlChanged = page.waitForFunction(
    () => !window.location.href.includes('/login'),
    null,  // arg (required before options)
    { timeout: 20_000, polling: 100 }
  );
  await page.getByRole('button', { name: /sign in/i }).click();
  await urlChanged;
  await page.waitForLoadState('domcontentloaded');

  // Save session state for reuse in all tests
  await page.context().storageState({ path: 'e2e/.auth/user.json' });
  await browser.close();
}

export default globalSetup;
