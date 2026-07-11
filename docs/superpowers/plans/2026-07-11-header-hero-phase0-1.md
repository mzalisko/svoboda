# Header + Hero (Phase 0 + Phase 1) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.
>
> **No git in this project** (`C:\Dev\freeproject\my-project` is not a git repo). Skip all "commit" steps — mark each task done by checking its boxes instead. If the user later runs `git init`, an initial commit can capture everything at once.

**Goal:** Self-host Cormorant Garamond, migrate the 3 existing ACF field groups from raw PHP registration to Local JSON (DB-backed + git-versioned), and rebuild the Header + Hero section so it pixel-matches `screenshots/hero-descktop.png` with real content and the correct one-time entrance animation (not the placeholder crude SVG plane / HTML-entity quote mark currently in place).

**Architecture:** WordPress custom theme `svoboda` (vanilla PHP/CSS/JS, no build step, no framework). Font files self-hosted as static `.woff2` assets referenced via `@font-face`. ACF field groups persisted through ACF's own `acf_update_field_group()` API (the same call the admin UI makes on save) via `wp eval-file`, with `acf/settings/save_json` / `load_json` filters pointed at a new `acf-json/` folder so edits in wp-admin auto-export to versionable JSON. Hero visual rebuilt using the real Figma-exported raster (torn-paper + paper-airplane composite) and vector (quote mark) assets already downloaded into the theme in this session.

**Tech Stack:** WordPress 7.0.1, PHP 8.2, MariaDB 10.11, Secure Custom Fields 6.9.1 (ACF-API-compatible free plugin), Docker Compose (containers already running: `my-project-wordpress-1`, `my-project-db-1`, `my-project-phpmyadmin-1`), wp-cli 2.12 (already installed at `/usr/local/bin/wp` inside the `wordpress` container).

All `wp` commands below run via:
```bash
docker compose exec -T -u root wordpress bash -c "cd /var/www/html && wp <command> --allow-root"
```
(run from `C:\Dev\freeproject\my-project`, since that's where `docker-compose.yml` lives).

---

## Task 1: Self-host Cormorant Garamond

**Files:**
- Create: `wp-content/themes/svoboda/assets/fonts/cormorant-garamond-600-normal-cyrillic.woff2`
- Create: `wp-content/themes/svoboda/assets/fonts/cormorant-garamond-600-normal-cyrillic-ext.woff2`
- Create: `wp-content/themes/svoboda/assets/fonts/cormorant-garamond-600-normal-latin.woff2`
- Create: `wp-content/themes/svoboda/assets/fonts/cormorant-garamond-500-italic-cyrillic.woff2`
- Create: `wp-content/themes/svoboda/assets/fonts/cormorant-garamond-500-italic-cyrillic-ext.woff2`
- Create: `wp-content/themes/svoboda/assets/fonts/cormorant-garamond-500-italic-latin.woff2`
- Create: `wp-content/themes/svoboda/assets/fonts/cormorant-garamond-700-italic-cyrillic.woff2`
- Create: `wp-content/themes/svoboda/assets/fonts/cormorant-garamond-700-italic-cyrillic-ext.woff2`
- Create: `wp-content/themes/svoboda/assets/fonts/cormorant-garamond-700-italic-latin.woff2`
- Modify: `wp-content/themes/svoboda/assets/css/style.css:1-30`

- [ ] **Step 1: Create the fonts directory and download the 9 files**

```bash
mkdir -p "C:\Dev\freeproject\my-project\wp-content\themes\svoboda\assets\fonts"
cd "C:\Dev\freeproject\my-project\wp-content\themes\svoboda\assets\fonts"

curl -sL -o cormorant-garamond-600-normal-cyrillic-ext.woff2 "https://fonts.gstatic.com/s/cormorantgaramond/v21/co3umX5slCNuHLi8bLeY9MK7whWMhyjypVO7abI26QOD_iE9KnnOiss4.woff2"
curl -sL -o cormorant-garamond-600-normal-cyrillic.woff2 "https://fonts.gstatic.com/s/cormorantgaramond/v21/co3umX5slCNuHLi8bLeY9MK7whWMhyjypVO7abI26QOD_iE9KnDOiss4.woff2"
curl -sL -o cormorant-garamond-600-normal-latin.woff2 "https://fonts.gstatic.com/s/cormorantgaramond/v21/co3umX5slCNuHLi8bLeY9MK7whWMhyjypVO7abI26QOD_iE9KnTOig.woff2"

curl -sL -o cormorant-garamond-500-italic-cyrillic-ext.woff2 "https://fonts.gstatic.com/s/cormorantgaramond/v21/co3ZmX5slCNuHLi8bLeY9MK7whWMhyjYrEtFmSq17w.woff2"
curl -sL -o cormorant-garamond-500-italic-cyrillic.woff2 "https://fonts.gstatic.com/s/cormorantgaramond/v21/co3ZmX5slCNuHLi8bLeY9MK7whWMhyjYrEtMmSq17w.woff2"
curl -sL -o cormorant-garamond-500-italic-latin.woff2 "https://fonts.gstatic.com/s/cormorantgaramond/v21/co3ZmX5slCNuHLi8bLeY9MK7whWMhyjYrEtImSo.woff2"

curl -sL -o cormorant-garamond-700-italic-cyrillic-ext.woff2 "https://fonts.gstatic.com/s/cormorantgaramond/v21/co3smX5slCNuHLi8bLeY9MK7whWMhyjYrGFEsdtdc62E6zd5FTf-hdM8Efs.woff2"
curl -sL -o cormorant-garamond-700-italic-cyrillic.woff2 "https://fonts.gstatic.com/s/cormorantgaramond/v21/co3smX5slCNuHLi8bLeY9MK7whWMhyjYrGFEsdtdc62E6zd5FTf-jNM8Efs.woff2"
curl -sL -o cormorant-garamond-700-italic-latin.woff2 "https://fonts.gstatic.com/s/cormorantgaramond/v21/co3smX5slCNuHLi8bLeY9MK7whWMhyjYrGFEsdtdc62E6zd5FTf-iNM8.woff2"
```

- [ ] **Step 2: Verify all 9 files are real woff2 binaries (not HTML error pages)**

```bash
file "C:\Dev\freeproject\my-project\wp-content\themes\svoboda\assets\fonts\"*.woff2
```
Expected: each line ends with `Web Open Font Format` (version 2). If any line says `HTML document text`, the download failed — re-run that one `curl` command.

- [ ] **Step 3: Add `@font-face` rules to `style.css`, replacing the TODO comment**

In `wp-content/themes/svoboda/assets/css/style.css`, replace lines 28-29 (the `/* TODO: self-host Cormorant Garamond ... */` comment) with:

```css
@font-face {
	font-family: "Cormorant Garamond";
	font-style: normal;
	font-weight: 600;
	font-display: swap;
	src: url("../fonts/cormorant-garamond-600-normal-latin.woff2") format("woff2");
	unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+2000-206F, U+2122;
}
@font-face {
	font-family: "Cormorant Garamond";
	font-style: normal;
	font-weight: 600;
	font-display: swap;
	src: url("../fonts/cormorant-garamond-600-normal-cyrillic.woff2") format("woff2");
	unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
@font-face {
	font-family: "Cormorant Garamond";
	font-style: normal;
	font-weight: 600;
	font-display: swap;
	src: url("../fonts/cormorant-garamond-600-normal-cyrillic-ext.woff2") format("woff2");
	unicode-range: U+0460-052F, U+1C80-1C8A, U+20B4, U+2DE0-2DFF, U+A640-A69F;
}
@font-face {
	font-family: "Cormorant Garamond";
	font-style: italic;
	font-weight: 500;
	font-display: swap;
	src: url("../fonts/cormorant-garamond-500-italic-latin.woff2") format("woff2");
	unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+2000-206F, U+2122;
}
@font-face {
	font-family: "Cormorant Garamond";
	font-style: italic;
	font-weight: 500;
	font-display: swap;
	src: url("../fonts/cormorant-garamond-500-italic-cyrillic.woff2") format("woff2");
	unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
@font-face {
	font-family: "Cormorant Garamond";
	font-style: italic;
	font-weight: 500;
	font-display: swap;
	src: url("../fonts/cormorant-garamond-500-italic-cyrillic-ext.woff2") format("woff2");
	unicode-range: U+0460-052F, U+1C80-1C8A, U+20B4, U+2DE0-2DFF, U+A640-A69F;
}
@font-face {
	font-family: "Cormorant Garamond";
	font-style: italic;
	font-weight: 700;
	font-display: swap;
	src: url("../fonts/cormorant-garamond-700-italic-latin.woff2") format("woff2");
	unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+2000-206F, U+2122;
}
@font-face {
	font-family: "Cormorant Garamond";
	font-style: italic;
	font-weight: 700;
	font-display: swap;
	src: url("../fonts/cormorant-garamond-700-italic-cyrillic.woff2") format("woff2");
	unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
}
@font-face {
	font-family: "Cormorant Garamond";
	font-style: italic;
	font-weight: 700;
	font-display: swap;
	src: url("../fonts/cormorant-garamond-700-italic-cyrillic-ext.woff2") format("woff2");
	unicode-range: U+0460-052F, U+1C80-1C8A, U+20B4, U+2DE0-2DFF, U+A640-A69F;
}
```

- [ ] **Step 4: Verify a font file is served over HTTP through the running site**

```bash
curl -s -o /dev/null -w "%{http_code} %{content_type}\n" "http://localhost:8080/wp-content/themes/svoboda/assets/fonts/cormorant-garamond-600-normal-cyrillic.woff2"
```
Expected: `200 font/woff2` (or `application/octet-stream` — either is fine, 200 is what matters).

- [ ] **Step 5: Verify homepage still returns 200**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/
```
Expected: `200`

---

## Task 2: Migrate ACF field groups to Local JSON

**Files:**
- Create: `wp-content/themes/svoboda/acf-json/` (directory, auto-populated by ACF on save)
- Create: `wp-content/themes/svoboda/inc/acf-json.php`
- Modify: `wp-content/themes/svoboda/functions.php:63-67` (require list)
- Modify: `wp-content/themes/svoboda/inc/acf-fields.php` (remove `svoboda_acf_field_groups()` + its `add_action`, keep `svoboda_acf_options_page()`)

- [ ] **Step 1: Create the `acf-json` directory**

```bash
mkdir -p "C:\Dev\freeproject\my-project\wp-content\themes\svoboda\acf-json"
```

- [ ] **Step 2: Create `inc/acf-json.php` with the save/load path filters**

```php
<?php
/**
 * Local JSON: ACF field groups редагуються через адмінку (edit.php?post_type=acf-field-group)
 * і одночасно версіонуються як JSON у git — стандартний production-патерн ACF/SCF.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'acf/settings/save_json',
	function () {
		return SVOBODA_THEME_DIR . '/acf-json';
	}
);

add_filter(
	'acf/settings/load_json',
	function ( $paths ) {
		unset( $paths[0] );
		$paths[] = SVOBODA_THEME_DIR . '/acf-json';
		return $paths;
	}
);
```

- [ ] **Step 3: Require the new file from `functions.php`**

In `wp-content/themes/svoboda/functions.php`, the current tail (lines 63-67) reads:

```php
require_once SVOBODA_THEME_DIR . '/inc/acf-fields.php';
require_once SVOBODA_THEME_DIR . '/inc/cpt-order.php';
require_once SVOBODA_THEME_DIR . '/inc/rest-novaposhta.php';
require_once SVOBODA_THEME_DIR . '/inc/rest-monobank-webhook.php';
```

Replace with:

```php
require_once SVOBODA_THEME_DIR . '/inc/acf-json.php';
require_once SVOBODA_THEME_DIR . '/inc/acf-fields.php';
require_once SVOBODA_THEME_DIR . '/inc/cpt-order.php';
require_once SVOBODA_THEME_DIR . '/inc/rest-novaposhta.php';
require_once SVOBODA_THEME_DIR . '/inc/rest-monobank-webhook.php';
```

- [ ] **Step 4: Write a one-off migration script and run it via `wp eval-file`**

Create a temporary file (outside the theme, this is a one-time migration script, not part of the codebase) at
`C:\Dev\freeproject\my-project\scratch-migrate-acf.php`:

```php
<?php
// One-off: persist the 3 existing local field groups into the DB (+ acf-json via save_json filter).
// Field arrays copied verbatim from inc/acf-fields.php, with one change: 'hero_quote' becomes
// a 'wysiwyg' field (was 'textarea') so the final bolded sentence in the Hero quote can be
// marked up, matching screenshots/hero-descktop.png.

acf_update_field_group(
	array(
		'key'      => 'group_front_page_content',
		'title'    => 'Контент головної сторінки',
		'fields'   => array(
			array( 'key' => 'field_hero_title', 'label' => 'Hero: заголовок', 'name' => 'hero_title', 'type' => 'text', 'default_value' => 'СВОБОДА' ),
			array( 'key' => 'field_hero_subtitle', 'label' => 'Hero: підзаголовок', 'name' => 'hero_subtitle', 'type' => 'textarea' ),
			array( 'key' => 'field_hero_quote', 'label' => 'Hero: цитата', 'name' => 'hero_quote', 'type' => 'wysiwyg', 'media_upload' => 0, 'toolbar' => 'basic' ),
			array( 'key' => 'field_author_name', 'label' => 'Про автора: ім\'я', 'name' => 'author_name', 'type' => 'text', 'default_value' => 'Ігор Браславець' ),
			array( 'key' => 'field_author_bio', 'label' => 'Про автора: біографія', 'name' => 'author_bio', 'type' => 'wysiwyg' ),
			array( 'key' => 'field_author_photo', 'label' => 'Про автора: фото', 'name' => 'author_photo', 'type' => 'image', 'return_format' => 'array' ),
			array( 'key' => 'field_author_instagram', 'label' => 'Про автора: Instagram (посилання)', 'name' => 'author_instagram', 'type' => 'url', 'default_value' => 'https://instagram.com/braslavetsigor' ),
			array( 'key' => 'field_pain_points', 'label' => '"Ця книга для вас": пункти', 'name' => 'pain_points', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати пункт', 'sub_fields' => array( array( 'key' => 'field_pain_point_text', 'label' => 'Текст', 'name' => 'text', 'type' => 'text' ) ) ),
			array( 'key' => 'field_video_embed', 'label' => 'Відеоблок: embed/URL', 'name' => 'video_embed_url', 'type' => 'url' ),
			array( 'key' => 'field_video_poster', 'label' => 'Відеоблок: превʼю-фото', 'name' => 'video_poster', 'type' => 'image', 'return_format' => 'array' ),
			array( 'key' => 'field_video_caption', 'label' => 'Відеоблок: підпис (тривалість, ім\'я)', 'name' => 'video_caption', 'type' => 'text' ),
			array( 'key' => 'field_sketch_quote_text', 'label' => 'Цитатний блок: текст', 'name' => 'sketch_quote_text', 'type' => 'textarea' ),
			array( 'key' => 'field_sketch_image', 'label' => 'Цитатний блок: скетч-ілюстрація', 'name' => 'sketch_image', 'type' => 'image', 'return_format' => 'array' ),
			array( 'key' => 'field_transformation_steps', 'label' => 'Трансформація: 6 пунктів шляху', 'name' => 'transformation_steps', 'type' => 'repeater', 'layout' => 'table', 'min' => 0, 'max' => 6, 'button_label' => 'Додати пункт', 'sub_fields' => array( array( 'key' => 'field_transformation_step_title', 'label' => 'Заголовок', 'name' => 'title', 'type' => 'text' ), array( 'key' => 'field_transformation_step_text', 'label' => 'Опис', 'name' => 'text', 'type' => 'textarea' ) ) ),
			array( 'key' => 'field_business_points', 'label' => 'Для власників бізнесу: пункти (01/02/03)', 'name' => 'business_points', 'type' => 'repeater', 'layout' => 'block', 'max' => 3, 'button_label' => 'Додати пункт', 'sub_fields' => array( array( 'key' => 'field_business_point_title', 'label' => 'Заголовок', 'name' => 'title', 'type' => 'text' ), array( 'key' => 'field_business_point_text', 'label' => 'Текст', 'name' => 'text', 'type' => 'textarea' ) ) ),
			array( 'key' => 'field_business_photo', 'label' => 'Для власників бізнесу: фото', 'name' => 'business_photo', 'type' => 'image', 'return_format' => 'array' ),
			array( 'key' => 'field_business_presentation_url', 'label' => 'Для власників бізнесу: посилання "Презентація"', 'name' => 'business_presentation_url', 'type' => 'url' ),
			array( 'key' => 'field_reviews', 'label' => 'Відгуки', 'name' => 'reviews', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Додати відгук', 'sub_fields' => array( array( 'key' => 'field_review_author', 'label' => 'Автор', 'name' => 'author', 'type' => 'text' ), array( 'key' => 'field_review_text', 'label' => 'Текст відгуку', 'name' => 'text', 'type' => 'textarea' ), array( 'key' => 'field_review_photo', 'label' => 'Фото (опційно)', 'name' => 'photo', 'type' => 'image', 'return_format' => 'array' ) ) ),
			array( 'key' => 'field_cta_background', 'label' => 'Фінальна CTA: фонове фото (гора)', 'name' => 'cta_background', 'type' => 'image', 'return_format' => 'array' ),
			array( 'key' => 'field_cta_title', 'label' => 'Фінальна CTA: заголовок', 'name' => 'cta_title', 'type' => 'text' ),
		),
		'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'front-page.php' ) ) ),
	)
);

acf_update_field_group(
	array(
		'key'      => 'group_order_block',
		'title'    => 'Замовлення книги',
		'fields'   => array(
			array( 'key' => 'field_price_paper', 'label' => 'Ціна: паперова версія (грн)', 'name' => 'price_paper', 'type' => 'number', 'default_value' => 450 ),
			array( 'key' => 'field_price_ebook', 'label' => 'Ціна: електронна версія (грн)', 'name' => 'price_ebook', 'type' => 'number', 'default_value' => 290 ),
			array( 'key' => 'field_limited_edition_note', 'label' => 'Позначка обмеженого тиражу', 'name' => 'limited_edition_note', 'type' => 'text', 'default_value' => 'Тираж обмежений' ),
			array( 'key' => 'field_book_cover_3d', 'label' => '3D-візуалізація обкладинки', 'name' => 'book_cover_3d', 'type' => 'image', 'return_format' => 'array' ),
		),
		'location' => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => 'front-page.php' ) ) ),
	)
);

acf_update_field_group(
	array(
		'key'      => 'group_site_options',
		'title'    => 'Глобальні налаштування сайту',
		'fields'   => array(
			array( 'key' => 'field_footer_phone', 'label' => 'Телефон', 'name' => 'footer_phone', 'type' => 'text' ),
			array( 'key' => 'field_footer_email', 'label' => 'Email', 'name' => 'footer_email', 'type' => 'email' ),
			array( 'key' => 'field_footer_socials', 'label' => 'Соцмережі', 'name' => 'footer_socials', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Додати мережу', 'sub_fields' => array( array( 'key' => 'field_footer_social_label', 'label' => 'Назва', 'name' => 'label', 'type' => 'text' ), array( 'key' => 'field_footer_social_url', 'label' => 'URL', 'name' => 'url', 'type' => 'url' ) ) ),
		),
		'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'svoboda-options' ) ) ),
	)
);

echo "3 field groups persisted.\n";
```

- [ ] **Step 5: Copy the migration script into the container and run it**

```bash
docker compose cp "C:\Dev\freeproject\my-project\scratch-migrate-acf.php" wordpress:/tmp/scratch-migrate-acf.php
docker compose exec -T -u root wordpress bash -c "cd /var/www/html && wp eval-file /tmp/scratch-migrate-acf.php --allow-root"
```
Expected output: `3 field groups persisted.`

- [ ] **Step 6: Remove the now-redundant raw PHP field group registration**

In `wp-content/themes/svoboda/inc/acf-fields.php`, delete the entire `svoboda_acf_field_groups()` function body and its `add_action( 'acf/init', 'svoboda_acf_field_groups' );` line (everything from the `/** * Group: контент... */` comment through the end of the file), but **keep** `svoboda_acf_options_page()` and its `add_action` — that registers the *options page itself* (a different ACF concept from a field group) and is unaffected by the Local JSON change.

The file should end with the options-page function only:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function svoboda_acf_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => 'Налаштування сайту',
			'menu_title' => 'Налаштування сайту',
			'menu_slug'  => 'svoboda-options',
			'capability' => 'manage_options',
			'redirect'   => false,
		)
	);
}
add_action( 'acf/init', 'svoboda_acf_options_page' );
```

- [ ] **Step 7: Delete the one-off migration script (not part of the theme)**

```bash
rm "C:\Dev\freeproject\my-project\scratch-migrate-acf.php"
docker compose exec -T -u root wordpress bash -c "rm -f /tmp/scratch-migrate-acf.php"
```

- [ ] **Step 8: Verify field groups now exist as DB posts, JSON files were written, and `get_field()` still works**

```bash
docker compose exec -T -u root wordpress bash -c "cd /var/www/html && wp post list --post_type=acf-field-group --fields=ID,post_title --allow-root"
ls "C:\Dev\freeproject\my-project\wp-content\themes\svoboda\acf-json"
```
Expected: 3 rows (Контент головної сторінки, Замовлення книги, Глобальні налаштування сайту) and 3 `.json` files in `acf-json/`.

```bash
docker compose exec -T -u root wordpress bash -c "cat > /tmp/check-fields.php <<'PHP'
<?php
\$groups = acf_get_field_groups( array( 'post_id' => 10 ) );
foreach ( \$groups as \$g ) { echo \$g['title'] . PHP_EOL; }
PHP
cd /var/www/html && wp eval-file /tmp/check-fields.php --allow-root"
```
Expected: `Контент головної сторінки` and `Замовлення книги` (page ID 10 is "Головна", created earlier).

- [ ] **Step 9: Verify homepage still returns 200 with no PHP errors**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/
docker compose logs wordpress --since 2m 2>&1 | grep -i "PHP Fatal\|PHP Warning" || echo "no errors"
```
Expected: `200` and `no errors`.

---

## Task 3: Header — three-column layout matching Figma

**Files:**
- Modify: `wp-content/themes/svoboda/header.php:19-38`
- Modify: `wp-content/themes/svoboda/assets/css/style.css:56-89` (Header section)

- [ ] **Step 1: Replace the header markup**

Current `header.php` (lines 19-38) uses a single `wp_nav_menu()` call. Figma shows three independent groups: left anchor nav, centered small red logo, right anchor nav — confirmed against the section `id` attributes that already exist in each `template-parts/section-*.php` file (`#hero`, `#for-you`, `#about`, `#business`, `#reviews`, `#order`). Replace lines 19-38 with:

```php
<header class="site-header" id="site-header" data-scroll-compact>
	<div class="site-header__inner">
		<nav class="site-header__nav site-header__nav--left" aria-label="<?php esc_attr_e( 'Навігація: про книгу', 'svoboda' ); ?>">
			<ul class="site-header__nav-list">
				<li><a href="#hero">Про книгу</a></li>
				<li><a href="#for-you">Що всередені</a></li>
				<li><a href="#about">Про автора</a></li>
			</ul>
		</nav>

		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php bloginfo( 'name' ); ?>
		</a>

		<nav class="site-header__nav site-header__nav--right" aria-label="<?php esc_attr_e( 'Навігація: дії', 'svoboda' ); ?>">
			<ul class="site-header__nav-list">
				<li><a href="#business">Для кого</a></li>
				<li><a href="#reviews">Відгуки</a></li>
				<li><a href="#order">Замовити</a></li>
			</ul>
		</nav>
	</div>
</header>
```

- [ ] **Step 2: Update the site title so the logo reads "Свобода", not "svoboda"**

```bash
docker compose exec -T -u root wordpress bash -c "cd /var/www/html && wp option update blogname 'Свобода' --allow-root"
```

- [ ] **Step 3: Update header CSS for the 3-column centered-logo layout**

In `wp-content/themes/svoboda/assets/css/style.css`, replace the `.site-header__inner` and `.site-header__nav-list` rules (current lines ~73-88) with:

```css
.site-header__inner {
	display: grid;
	grid-template-columns: 1fr auto 1fr;
	align-items: center;
	max-width: var(--content-max-width);
	margin: 0 auto;
}

.site-header__nav-list {
	display: flex;
	gap: 40.311px;
	list-style: none;
	margin: 0;
	padding: 0;
	font: 24px var(--font-body);
}

.site-header__nav--left { justify-self: start; }
.site-header__nav--right { justify-self: end; }

.site-header__logo {
	justify-self: center;
	font-family: var(--font-body);
	font-weight: 700;
	font-size: 24px;
	letter-spacing: 2px;
	text-transform: uppercase;
	color: var(--red-90);
}
```

- [ ] **Step 4: Verify homepage returns 200 and header renders 3 groups**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/
curl -s http://localhost:8080/ | grep -o 'site-header__nav--left\|site-header__logo\|site-header__nav--right'
```
Expected: `200`, then all three class names printed once each.

---

## Task 4: Hero — real image assets + markup

**Files:**
- Already created (this session): `wp-content/themes/svoboda/assets/img/hero-plane-torn-paper.png` (2552×2466 PNG)
- Already created (this session): `wp-content/themes/svoboda/assets/svg/hero-quote-mark.svg`
- Modify: `wp-content/themes/svoboda/template-parts/section-hero.php` (full rewrite)
- Modify: `wp-content/themes/svoboda/inc/acf-fields.php` is already updated by Task 2 Step 6 — no further field changes needed here (the `hero_quote` field is already `wysiwyg` from the Task 2 migration).

- [ ] **Step 1: Verify the two asset files are present and valid**

```bash
file "C:\Dev\freeproject\my-project\wp-content\themes\svoboda\assets\img\hero-plane-torn-paper.png"
file "C:\Dev\freeproject\my-project\wp-content\themes\svoboda\assets\svg\hero-quote-mark.svg"
```
Expected: `PNG image data, 2552 x 2466 ...` and `SVG Scalable Vector Graphics image`.

- [ ] **Step 2: Rewrite `section-hero.php`**

Replace the entire file with:

```php
<?php
/**
 * Секція 1: Hero — заголовок "СВОБОДА", підзаголовок, паперовий літачок, цитата.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title    = get_field( 'hero_title' ) ?: 'СВОБОДА';
$subtitle = get_field( 'hero_subtitle' );
$quote    = get_field( 'hero_quote' );
?>
<section class="section section--hero" id="hero">
	<div class="section--hero__inner">
		<div class="section--hero__plane">
			<img
				src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/img/hero-plane-torn-paper.png' ); ?>"
				alt=""
				class="section--hero__plane-img"
			>
		</div>

		<h1 class="section--hero__title" data-fade-in>
			<?php echo esc_html( $title ); ?>
		</h1>

		<?php if ( $subtitle ) : ?>
			<p class="section--hero__subtitle" data-fade-in data-fade-delay="150">
				<?php echo esc_html( $subtitle ); ?>
			</p>
		<?php endif; ?>

		<?php if ( $quote ) : ?>
			<blockquote class="section--hero__quote">
				<img
					src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/svg/hero-quote-mark.svg' ); ?>"
					alt=""
					class="section--hero__quote-mark"
					data-fade-in
				>
				<div class="section--hero__quote-text">
					<?php echo wp_kses_post( $quote ); ?>
				</div>
			</blockquote>
		<?php endif; ?>
	</div>
</section>
```

Note: `svg-plane.php` is no longer called from Hero (it stays in use for the Transformation section — leave that file untouched).

- [ ] **Step 3: Verify homepage returns 200**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/
docker compose logs wordpress --since 1m 2>&1 | grep -i "PHP Fatal" || echo "no fatal errors"
```
Expected: `200` and `no fatal errors`.

---

## Task 5: Hero — CSS layout matching Figma coordinates

**Files:**
- Modify: `wp-content/themes/svoboda/assets/css/style.css:132-184` (Section: Hero block)

Figma hero canvas is 1920×1138 (per `docs/figma/design-tokens.md` row "Hero + Header | top 0–~1138"). Converting the exact node coordinates fetched from Figma this session to percentages of that canvas:
- Title+subtitle text block: `left: 4.95%; top: 13%; width: 42.6%` (node `180:63`, x=95 y=148 w=818)
- Plane/torn-paper image: `left: 0%; top: -2.4%; width: 69.2%; height: 99.1%` (node `180:66`, x=1 y=-27 w=1328 h=1128) — the image itself overflows its box slightly (Figma fill crop): `width:100.52%; height:114.34%; left:-2.99%` inside that box.
- Quote block (mark + text): `left: 54.9%; top: 63.6%; width: 36.9%` (marks span from node `180:79` x=1055 to text end x=1184.7+561.8=1746.5, so width ≈ (1746.5-1055)/1920 = 36.0%)

- [ ] **Step 1: Replace the Hero CSS block**

In `style.css`, replace the entire `/* --- 5. Section: Hero --- */` block (current lines ~132-172, from `.section--hero {` through `.svg-plane { ... }`) with:

```css
.section--hero {
	position: relative;
	min-height: 90vh;
	display: flex;
	align-items: center;
}

.section--hero__inner {
	position: relative;
	width: 100%;
	aspect-ratio: 1920 / 1138;
}

.section--hero__title {
	position: absolute;
	left: 4.95%;
	top: 13%;
	width: 42.6%;
	font-family: var(--font-display);
	font-weight: 600;
	font-size: 134.79px;
	line-height: 125.972px;
	letter-spacing: 2.6958px;
	color: var(--red-90);
	text-align: right;
	text-transform: uppercase;
}

.section--hero__subtitle {
	position: absolute;
	left: 4.95%;
	top: 27%;
	width: 42.6%;
	font-family: var(--font-body);
	font-size: 45.35px;
	line-height: 1.1;
	letter-spacing: 0.907px;
	white-space: pre-line;
}

.section--hero__plane {
	position: absolute;
	left: 0;
	top: -2.4%;
	width: 69.2%;
	height: 99.1%;
	overflow: hidden;
}

.section--hero__plane-img {
	position: absolute;
	top: 0;
	left: -2.99%;
	width: 100.52%;
	max-width: none;
	height: 114.34%;
}

.section--hero__quote {
	position: absolute;
	left: 54.9%;
	top: 63.6%;
	width: 36.9%;
	display: flex;
	gap: 12px;
	margin: 0;
}

.section--hero__quote-mark {
	display: block;
	width: 62px;
	flex-shrink: 0;
	opacity: 0;
	transform: translateX(60px) scale(0.72);
	transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.section--hero__quote-mark.is-visible {
	opacity: 1;
	transform: translateX(0) scale(1);
}

.section--hero__quote-text {
	font-family: var(--font-display);
	font-style: italic;
	font-weight: 500;
	font-size: 30px;
	color: rgba(0, 0, 0, 0.8);
}

.section--hero__quote-text strong {
	font-weight: 700;
	font-style: italic;
}

/* Fade-in утиліта, використовується IntersectionObserver'ом (animations.js) */
[data-fade-in] {
	opacity: 0;
	transform: translateY(24px);
	transition: opacity 0.6s ease, transform 0.6s ease;
}
[data-fade-in].is-visible {
	opacity: 1;
	transform: translateY(0);
}
```

This block intentionally keeps the generic `[data-fade-in]` utility (moved here from directly below its old position) **before** `.section--hero__quote-mark`'s own rules in the cascade — both selectors have equal specificity (0,1,0), so declaration order decides, and the quote-mark's own `opacity`/`transform` values must win over the generic ones since both are applied to the same `<img data-fade-in class="section--hero__quote-mark">` element.

- [ ] **Step 2: Add a mobile override so the absolute-position layout collapses to a stack**

In the existing `/* --- 16. Responsive (mobile <= 768px...) --- */` block at the bottom of `style.css`, add (inside the existing `@media (max-width: 768px) { ... }` block, right after the `.section--hero__subtitle { font-size: 22px; }` line):

```css
	.section--hero__inner {
		position: static;
		aspect-ratio: auto;
		display: flex;
		flex-direction: column;
		gap: 24px;
	}

	.section--hero__title,
	.section--hero__subtitle,
	.section--hero__plane,
	.section--hero__quote {
		position: static;
		width: 100%;
	}

	.section--hero__plane {
		height: auto;
		aspect-ratio: 1328 / 1128;
	}
```

- [ ] **Step 3: Verify homepage returns 200**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/
```
Expected: `200`

---

## Task 6: Hero — one-time entrance animations (fade-in stagger + quote mark)

The generic `[data-fade-in]` + `IntersectionObserver` mechanism (`animations.js`, function `initFadeIn`) already exists and already runs on every element carrying a `data-fade-in` attribute — no JS changes are needed. `section-hero.php` (Task 4) already added `data-fade-in` to the title, the subtitle (with `data-fade-delay="150"`), and the quote mark image. This task only adds a stagger delay to the quote-mark relative to the title/subtitle, matching `docs/figma/animations.md` section 1 ("одноразовий fade/scale-in при появі Hero... delay після заголовка").

**Files:**
- Modify: `wp-content/themes/svoboda/template-parts/section-hero.php:26` (one attribute)

- [ ] **Step 1: Add a fade-delay to the quote mark**

In `section-hero.php`, change:
```php
				<img
					src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/svg/hero-quote-mark.svg' ); ?>"
					alt=""
					class="section--hero__quote-mark"
					data-fade-in
				>
```
to:
```php
				<img
					src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/svg/hero-quote-mark.svg' ); ?>"
					alt=""
					class="section--hero__quote-mark"
					data-fade-in
					data-fade-delay="300"
				>
```

- [ ] **Step 2: Verify `initFadeIn()` picks up the new delay attribute**

```bash
curl -s http://localhost:8080/ | grep -o 'data-fade-delay="[0-9]*"'
```
Expected: three matches — `data-fade-delay="150"` (subtitle) and `data-fade-delay="300"` (quote mark); the title has no delay attribute (defaults to 0 in `initFadeIn`'s `parseInt(...|| '0', 10)` fallback).

---

## Task 7: Populate real Hero content

**Files:** none (content lives in the database via ACF fields on page ID 10, "Головна")

- [ ] **Step 1: Write the content-population script**

Create `C:\Dev\freeproject\my-project\scratch-hero-content.php`:

```php
<?php
update_field( 'hero_title', 'СВОБОДА', 10 );
update_field( 'hero_subtitle', "Шлях за межі реального.\nШлях на грані можливого.", 10 );
update_field(
	'hero_quote',
	'<p>Ми не просто бачимо як події відбуваються з нами. Ми їх створюємо. Своїм внутрішнім станом, думками, вірою.</p><p><strong><em>Ви дізнаєтеся як підкорити реальність.</em></strong></p>',
	10
);
echo "Hero content saved.\n";
```

- [ ] **Step 2: Run it**

```bash
docker compose cp "C:\Dev\freeproject\my-project\scratch-hero-content.php" wordpress:/tmp/scratch-hero-content.php
docker compose exec -T -u root wordpress bash -c "cd /var/www/html && wp eval-file /tmp/scratch-hero-content.php --allow-root"
rm "C:\Dev\freeproject\my-project\scratch-hero-content.php"
docker compose exec -T -u root wordpress bash -c "rm -f /tmp/scratch-hero-content.php"
```
Expected: `Hero content saved.`

- [ ] **Step 3: Verify the content renders on the live page**

```bash
curl -s http://localhost:8080/ | grep -o 'СВОБОДА\|Шлях за межі реального\|Ви дізнаєтеся як підкорити реальність'
```
Expected: all three strings present in the output.

---

## Task 8: Final verification

- [ ] **Step 1: Full page health check**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/
docker compose logs wordpress --since 2m 2>&1 | grep -i "PHP Fatal\|PHP Warning\|PHP Notice" || echo "no PHP errors/warnings"
```
Expected: `200` and `no PHP errors/warnings`.

- [ ] **Step 2: Confirm all Task-4/5 elements are present in the rendered HTML**

```bash
curl -s http://localhost:8080/ | grep -o 'section--hero__plane-img\|section--hero__quote-mark\|hero-plane-torn-paper.png\|hero-quote-mark.svg'
```
Expected: all four strings present.

- [ ] **Step 3: Visual review**

Open `http://localhost:8080/` in a browser (or use the `run` skill's Playwright pattern to screenshot it) and compare side-by-side against `screenshots/hero-descktop.png`. Note any remaining spacing/sizing gaps for a follow-up pass — Task 5's percentage-based layout is a faithful first cut, not pre-verified pixel-for-pixel against a rendered browser.

---

## What's next

This plan covers Phase 0 + Phase 1 only (per `specs/freedom-landing/2026-07-11-design-match-figma.md`). After this is reviewed and the visual check in Task 8 Step 3 looks right, Phase 2 (Order block) gets its own plan the same way — pull `get_design_context` for that section's node id, download its assets, write the plan, execute. Figma MCP hit its Starter-plan rate limit once already this session; pace Figma calls in future phases (batch metadata+design-context calls per section rather than exploratory back-and-forth).
