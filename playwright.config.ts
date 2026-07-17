import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './e2e',
  globalSetup: './e2e/global-setup.ts',
  timeout: 45_000,
  expect: { timeout: 12_000 },
  fullyParallel: false,   // Filament DB state must be sequential
  retries: 1,
  workers: 1,
  reporter: [['html', { outputFolder: 'e2e/report', open: 'never' }], ['list']],

  use: {
    baseURL: 'http://127.0.0.1:8000',
    headless: true,
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    trace: 'retain-on-failure',
    locale: 'ro-RO',
    // Reuse authenticated session by default (individual auth tests override this)
    storageState: 'e2e/.auth/user.json',
  },

  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
  ],
});
