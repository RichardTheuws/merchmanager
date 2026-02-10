/**
 * Playwright config for MerchManager P2 UAT.
 * Run against local WordPress (e.g. merchmanager.local):
 *
 *   BASE_URL=https://merchmanager.local WP_USER=admin WP_PASSWORD=yourpassword npx playwright test
 *
 * Or set .env (see .env.example) and run: npx playwright test
 */
// @ts-check
const { defineConfig, devices } = require('@playwright/test');

const baseURL = process.env.BASE_URL || process.env.MERCHMANAGER_BASE_URL || 'http://localhost:8080';

module.exports = defineConfig({
  testDir: './tests/e2e',
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 1 : 0,
  workers: 1,
  reporter: [['html', { open: 'never' }], ['list']],
  use: {
    baseURL,
    ignoreHTTPSErrors: true,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'on-first-retry',
  },
  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
  ],
  timeout: 30000,
  expect: { timeout: 10000 },
});
