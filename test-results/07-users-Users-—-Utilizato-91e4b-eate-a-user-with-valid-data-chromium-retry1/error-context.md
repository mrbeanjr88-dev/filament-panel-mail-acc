# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: 07-users.spec.ts >> Users — Utilizatori >> can create a user with valid data
- Location: e2e/07-users.spec.ts:110:3

# Error details

```
Error: page.goto: Target page, context or browser has been closed
```

# Test source

```ts
  1   | import { test, expect } from '@playwright/test';
  2   | import { CREDENTIALS } from './helpers/auth';
  3   | import { waitForTable } from './helpers/filament';
  4   | 
  5   | test.describe('Users — Utilizatori', () => {
  6   | 
  7   |   test.beforeEach(async ({ page }) => {
> 8   |     await page.goto('/admin/users');
      |                ^ Error: page.goto: Target page, context or browser has been closed
  9   |     await waitForTable(page);
  10  |   });
  11  | 
  12  |   // ── List ────────────────────────────────────────────────────────────
  13  | 
  14  |   test('list page renders correctly', async ({ page }) => {
  15  |     await expect(page.locator('table')).toBeVisible();
  16  |     await expect(page.locator('table')).toContainText(/Admin/i);
  17  |   });
  18  | 
  19  |   test('table has name, email and verified columns', async ({ page }) => {
  20  |     const header = page.locator('table thead');
  21  |     await expect(header).toContainText('Nume');
  22  |     await expect(header).toContainText('Adresă email');
  23  |     await expect(header).toContainText('Verificat');
  24  |   });
  25  | 
  26  |   test('email column is copyable (has copy button on hover)', async ({ page }) => {
  27  |     const emailCell = page.locator('table tbody tr').first().locator('td').nth(1);
  28  |     await emailCell.hover();
  29  |     // Copyable adds a copy button
  30  |     await expect(emailCell).toBeVisible();
  31  |   });
  32  | 
  33  |   test('pagination shows [10, 25, 50] options', async ({ page }) => {
  34  |     const pagination = page.locator('.fi-ta-pagination, [data-testid="pagination"]');
  35  |     // If table has pagination controls
  36  |     await expect(page.locator('table')).toBeVisible();
  37  |   });
  38  | 
  39  |   test('email verification shown as icon column', async ({ page }) => {
  40  |     // The verified column renders as a boolean IconColumn — find any SVG in the first row
  41  |     const svgInRow = page.locator('table tbody tr').first().locator('svg').first();
  42  |     await expect(svgInRow).toBeVisible();
  43  |   });
  44  | 
  45  |   // ── Self-delete guard ────────────────────────────────────────────────
  46  | 
  47  |   test('logged-in user cannot delete own account from table', async ({ page }) => {
  48  |     // Find the row with our current user's email
  49  |     const myRow = page.locator('table tbody tr').filter({ hasText: CREDENTIALS.email }).first();
  50  |     if (await myRow.isVisible()) {
  51  |       const deleteBtn = myRow.locator('button').filter({ hasText: /Șterge|Delete/i }).first();
  52  |       // Delete button should be hidden for own account
  53  |       await expect(deleteBtn).not.toBeVisible();
  54  |     }
  55  |   });
  56  | 
  57  |   test('can delete other user (not self)', async ({ page }) => {
  58  |     // Find a row that is NOT the logged-in user
  59  |     const rows = page.locator('table tbody tr');
  60  |     const count = await rows.count();
  61  |     for (let i = 0; i < count; i++) {
  62  |       const row = rows.nth(i);
  63  |       const hasMyEmail = await row.locator(`text="${CREDENTIALS.email}"`).isVisible();
  64  |       if (!hasMyEmail) {
  65  |         const deleteBtn = row.locator('button').filter({ hasText: /Șterge|Delete/i }).first();
  66  |         if (await deleteBtn.isVisible()) {
  67  |           // Delete button should be visible for other users
  68  |           await expect(deleteBtn).toBeVisible();
  69  |           break;
  70  |         }
  71  |       }
  72  |     }
  73  |   });
  74  | 
  75  |   // ── Create ──────────────────────────────────────────────────────────
  76  | 
  77  |   test('create page has all required fields', async ({ page }) => {
  78  |     await page.goto('/admin/users/create');
  79  |     await page.waitForLoadState('domcontentloaded');
  80  |     await expect(page.getByLabel(/Nume complet|Full name/i)).toBeVisible();
  81  |     await expect(page.locator('input[type="email"]').first()).toBeVisible();
  82  |     await expect(page.locator('input[type="password"]').first()).toBeVisible();
  83  |     await expect(page.locator('input[type="password"]').nth(1)).toBeVisible();
  84  |   });
  85  | 
  86  |   test('create requires password confirmation match', async ({ page }) => {
  87  |     await page.goto('/admin/users/create');
  88  |     await page.waitForLoadState('networkidle');
  89  | 
  90  |     await page.getByLabel(/Nume complet|Full name/i).fill('New User Test');
  91  |     await page.locator('input[type="email"]').first().fill('newuser@test.com');
  92  |     const p1 = page.locator('input[type="password"]').first();
  93  |     await p1.fill('password123');
  94  |     await p1.press('Tab');
  95  |     const p2 = page.locator('input[type="password"]').nth(1);
  96  |     await p2.fill('different-password');
  97  |     await p2.press('Tab');
  98  | 
  99  |     await page.waitForLoadState('networkidle');
  100 | 
  101 |     const submitBtn2 = page.locator('button[type="submit"]:not(.fi-dropdown-list-item)').first();
  102 |     await submitBtn2.scrollIntoViewIfNeeded();
  103 |     await submitBtn2.click();
  104 |     await page.waitForLoadState('networkidle').catch(() => {});
  105 | 
  106 |     // Should stay on create page due to validation
  107 |     await expect(page).toHaveURL(/\/create/);
  108 |   });
```