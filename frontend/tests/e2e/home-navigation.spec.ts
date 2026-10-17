import { test, expect } from '@playwright/test';

test('01 trang chu hien tieu de', async ({ page }) => { await page.goto('/'); await expect(page.locator('h1')).toContainText('Bảo tàng ảo'); });
test('02 trang chu hien nut xem lo trinh', async ({ page }) => { await page.goto('/'); await expect(page.getByRole('link', {name:/Xem lộ trình/i})).toBeVisible(); });
test('03 trang chu hien khong gian', async ({ page }) => { await page.goto('/'); await expect(page.getByRole('heading', {name:'Không gian trưng bày'})).toBeVisible(); });
test('04 dieu huong sang lo trinh', async ({ page }) => { await page.goto('/'); await page.getByRole('link', {name:/Xem lộ trình/i}).click(); await expect(page).toHaveURL(/\/tours$/); });
test('05 dieu huong so luu but', async ({ page }) => { await page.goto('/'); await page.getByRole('link', {name:/Sổ lưu bút/i}).click(); await expect(page).toHaveURL(/so-luu-but/); });
test('06 dieu huong dang nhap', async ({ page }) => { await page.goto('/'); await page.getByRole('link', {name:/Đăng nhập/i}).click(); await expect(page).toHaveURL(/dang-nhap/); });
test('07 dieu huong dang ky', async ({ page }) => { await page.goto('/dang-ky'); await expect(page.locator('h2')).toContainText('Đăng ký'); });
test('08 trang quen mat khau hien form', async ({ page }) => { await page.goto('/quen-mat-khau'); await expect(page.locator('input[type=email]')).toBeVisible(); });
test('09 footer hoc thuat hien tren trang chu', async ({ page }) => { await page.goto('/'); await expect(page.locator('body')).toContainText('CSE703073'); });
test('10 khong co loi overflow doc tren desktop', async ({ page }) => { await page.goto('/'); const v=await page.evaluate(()=>document.documentElement.scrollWidth<=window.innerWidth+1); expect(v).toBeTruthy(); });
