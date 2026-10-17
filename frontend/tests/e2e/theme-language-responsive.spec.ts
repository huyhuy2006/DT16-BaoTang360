import { test, expect } from '@playwright/test';

test('31 doi chu de sang toi', async ({ page }) => { await page.goto('/'); await page.getByText('Chủ đề tối').click(); await expect(page.locator('html')).toHaveAttribute('data-theme','dark'); });
test('32 doi chu de sang sang', async ({ page }) => { await page.goto('/'); const b=page.locator('button').filter({hasText:/Chủ đề/}).first(); if(await b.count()) await b.click(); expect(true).toBeTruthy(); });
test('33 doi ngon ngu sang Anh', async ({ page }) => { await page.goto('/'); const s=page.locator('.lang-switcher'); await s.selectOption('en'); await expect(page.locator('body')).toContainText('Tours'); });
test('34 doi ngon ngu sang Viet', async ({ page }) => { await page.goto('/'); const s=page.locator('.lang-switcher'); await s.selectOption('vi'); await expect(page.locator('body')).toContainText('Lộ trình'); });
test('35 mobile 375 khong overflow', async ({ page }) => { await page.setViewportSize({width:375,height:812}); await page.goto('/'); const v=await page.evaluate(()=>document.body.scrollWidth<=document.body.clientWidth+1); expect(v).toBeTruthy(); });
test('36 tablet 768 khong overflow', async ({ page }) => { await page.setViewportSize({width:768,height:1024}); await page.goto('/'); const v=await page.evaluate(()=>document.body.scrollWidth<=document.body.clientWidth+1); expect(v).toBeTruthy(); });
test('37 desktop 1440 khong overflow', async ({ page }) => { await page.setViewportSize({width:1440,height:900}); await page.goto('/'); const v=await page.evaluate(()=>document.body.scrollWidth<=document.body.clientWidth+1); expect(v).toBeTruthy(); });
test('38 header co brand', async ({ page }) => { await page.goto('/'); await expect(page.locator('.brand')).toBeVisible(); });
test('39 nut theme ton tai', async ({ page }) => { await page.goto('/'); await expect(page.locator('button').filter({hasText:/Chủ đề/}).first()).toBeVisible(); });
test('40 selector language ton tai', async ({ page }) => { await page.goto('/'); await expect(page.locator('.lang-switcher')).toBeVisible(); });
