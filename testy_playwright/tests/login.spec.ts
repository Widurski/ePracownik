import { test, expect } from '@playwright/test';

test.describe('Login Functionality', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/pages/login.html');
    });

    test('should login successfully with valid credentials', async ({ page }) => {
        // Mock API response for successful login
        await page.route('**/api/login', async route => {
            const json = {
                message: 'Zalogowano pomyślnie',
                token: 'fake-jwt-token',
                user: {
                    id: 1,
                    imie: 'Jan',
                    nazwisko: 'Kowalski',
                    email: 'jan@test.pl',
                    rola: 'administrator'
                }
            };
            await route.fulfill({ json });
        });

        await page.fill('#email', 'jan@test.pl');
        await page.fill('#password', 'secret123');
        await page.click('button[type="submit"]');

        // Expect redirection to admin dashboard based on role
        await expect(page).toHaveURL(/.*pages\/admin\.html/);
    });

    test('should show error with invalid credentials', async ({ page }) => {
        // Mock API response for failed login
        await page.route('**/api/login', async route => {
            await route.fulfill({
                status: 401,
                json: { error: 'Nieprawidłowy email lub hasło' }
            });
        });

        await page.fill('#email', 'wrong@test.pl');
        await page.fill('#password', 'wrongpass');
        await page.click('button[type="submit"]');

        await expect(page.locator('#komunikaty')).toContainText('Nieprawidłowy email lub hasło');
    });

    test('should validate empty fields', async ({ page }) => {
        // HTML5 validation check
        const emailInput = page.locator('#email');
        await expect(emailInput).toHaveAttribute('required', '');

        // Try to submit without filling
        await page.click('button[type="submit"]');

        // Check if the input is invalid (browser verification)
        const isInvalid = await emailInput.evaluate((e: HTMLInputElement) => !e.checkValidity());
        expect(isInvalid).toBe(true);
    });
});
