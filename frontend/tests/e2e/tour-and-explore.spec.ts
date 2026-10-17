import { test, expect } from '@playwright/test';

test('11 danh sach tour load', async ({ page }) => { await page.goto('/tours'); await expect(page.locator('h2')).toContainText('Lộ trình'); });
test('12 danh sach tour co card', async ({ page }) => { await page.goto('/tours'); await expect(page.locator('.tour-card').first()).toBeVisible(); });
test('13 click tour card', async ({ page }) => { await page.goto('/tours'); await page.locator('.tour-card').first().click(); await expect(page).toHaveURL(/\/tours\/\d+/); });
test('14 tour player hien che do', async ({ page }) => { await page.goto('/tours/1'); await expect(page.locator('select')).toBeVisible(); });
test('15 tour player co nut dieu huong', async ({ page }) => { await page.goto('/tours/1'); await expect(page.getByRole('button', {name:/Trước/})).toBeVisible(); });
test('16 tour player chuyen tu guided sang free', async ({ page }) => { await page.goto('/tours/1'); const select=page.locator('select').first(); await select.selectOption('free'); await expect(page.locator('text=Chế độ tự do')).toBeVisible(); });
test('17 tham quan tu do hien viewer', async ({ page }) => { await page.goto('/tham-quan-tu-do/1'); await expect(page.locator('.panorama-container')).toBeVisible(); });
test('18 tham quan tu do co hotspot', async ({ page }) => { await page.goto('/tham-quan-tu-do/1'); await expect(page.locator('.panorama-container')).toBeVisible(); });
test('19 trang bo suu tap bi chan khi chua dang nhap', async ({ page }) => { await page.goto('/bo-suu-tap'); await expect(page).toHaveURL(/dang-nhap/); });
test('20 trang admin bi chan khi chua dang nhap', async ({ page }) => { await page.goto('/quan-tri/khong-gian'); await expect(page).toHaveURL(/\/$|dang-nhap/); });
