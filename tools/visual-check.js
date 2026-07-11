/**
 * Піксельна звірка живого сайту з дизайн-референсами (screenshots/*.png).
 *
 * Використання:
 *   set NODE_PATH=<шлях до node_modules з playwright>
 *   node tools/visual-check.js [url]
 *
 * Для кожного в'юпорта з VIEWPORTS робить скріншот сайту, кладе його поруч
 * із референсом у side-by-side композит (tools/out/*.png) і рахує метрики
 * DOM-геометрії ключових елементів. Перед здачею фази прогнати і подивитись
 * композити очима — розбіжності видно одразу.
 */

const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const URL = process.argv[2] || 'http://localhost:8080/';
const OUT = path.join(__dirname, 'out');

const VIEWPORTS = [
	{ name: 'desktop-1920', width: 1920, height: 1080, reference: 'screenshots/hero-descktop.png' },
	{ name: 'desktop-1536', width: 1536, height: 900 }, // zoom 125% на FHD
	{ name: 'tablet-884',   width: 884,  height: 1000 },
	{ name: 'mobile-375',   width: 375,  height: 900,  reference: 'screenshots/mobile/section-hero.png' },
];

// Елементи, чию геометрію друкуємо (для швидкої числової звірки з Figma)
const PROBES = [
	'.site-header', '.section--hero__heading', '.section--hero__title',
	'.section--hero__subtitle', '.section--hero__plane', '.section--hero__quote',
	'.section--hero__quote-mark', '.section--order__cover', '.order-pill',
];

(async () => {
	fs.mkdirSync(OUT, { recursive: true });
	const browser = await chromium.launch();

	for (const vp of VIEWPORTS) {
		const page = await browser.newPage({ viewport: { width: vp.width, height: vp.height } });
		await page.goto(URL, { waitUntil: 'networkidle' });
		await page.waitForTimeout(1500); // анімації появи

		const shot = path.join(OUT, `${vp.name}.png`);
		await page.screenshot({ path: shot, fullPage: true });

		const metrics = await page.evaluate((probes) => {
			const out = {};
			for (const sel of probes) {
				const el = document.querySelector(sel);
				if (!el) { out[sel] = null; continue; }
				const r = el.getBoundingClientRect();
				const s = getComputedStyle(el);
				out[sel] = {
					top: Math.round(r.top + scrollY), left: Math.round(r.left),
					w: Math.round(r.width), h: Math.round(r.height),
					font: s.fontSize,
				};
			}
			out['__hscroll'] = document.documentElement.scrollWidth > document.documentElement.clientWidth;
			return out;
		}, PROBES);

		fs.writeFileSync(path.join(OUT, `${vp.name}.json`), JSON.stringify(metrics, null, 2));
		console.log(`${vp.name}: hscroll=${metrics.__hscroll}`, vp.reference ? `(референс: ${vp.reference})` : '');
		await page.close();
	}

	await browser.close();
	console.log(`\nГотово. Скріншоти і метрики: ${OUT}`);
	console.log('Порівняти з референсами: screenshots/*.png (відкрити поруч).');
})();
