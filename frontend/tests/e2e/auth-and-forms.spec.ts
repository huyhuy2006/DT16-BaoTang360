import { test, expect } from '@playwright/test';

test('21 form dang nhap co email va password', async ({ page }) => { await page.goto('/dang-nhap'); await expect(page.locator('input[type=email]')).toBeVisible(); await expect(page.locator('input[type=password]')).toBeVisible(); });
test('22 form dang ky co ten', async ({ page }) => { await page.goto('/dang-ky'); await expect(page.locator('input').first()).toBeVisible(); });
test('23 form quen mat khau co nut gui', async ({ page }) => { await page.goto('/quen-mat-khau'); await expect(page.getByRole('button', {name:/Gửi liên kết/})).toBeVisible(); });
test('24 form reset mat khau hien truong', async ({ page }) => { await page.goto('/dat-lai-mat-khau?token=x&email=a@example.test'); await expect(page.locator('input[type=password]').first()).toBeVisible(); });
test('25 dang nhap sai hien loi', async ({ page }) => { await page.goto('/dang-nhap'); await page.fill('input[type=email]','admin@example.test'); await page.fill('input[type=password]','sai'); await page.getByRole('button',{name:'Đăng nhập'}).click(); await expect(page.locator('.error')).toBeVisible(); });
test('26 form so luu but hien ten', async ({ page }) => { await page.goto('/so-luu-but'); await expect(page.locator('input[placeholder="Tên của bạn"]')).toBeVisible(); });
test('27 form so luu but hien textarea', async ({ page }) => { await page.goto('/so-luu-but'); await expect(page.locator('textarea')).toBeVisible(); });
test('28 nut dang nhap co type submit', async ({ page }) => { await page.goto('/dang-nhap'); await expect(page.locator('button[type=submit]')).toHaveAttribute('type','submit'); });
test('29 route reset-password chay duoc voi query', async ({ page }) => { await page.goto('/dat-lai-mat-khau?token=abc&email=test@example.test'); await expect(page.locator('h2')).toContainText('Đặt lại mật khẩu'); });
test('30 route dang ky hien giao dien', async ({ page }) => { await page.goto('/dang-ky'); await expect(page.locator('body')).toContainText('Đăng ký'); });
