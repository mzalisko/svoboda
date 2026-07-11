# Тема "Свобода"

Кастомна WordPress-тема для лендинга книги "Свобода". Чистий HTML5/CSS/PHP,
без білдерів сторінок, без WooCommerce.

## Локальний запуск

1. Скопіювати `.env.example` → `.env` в корені проекту, заповнити паролі БД.
2. `docker compose up -d`
3. Відкрити `http://localhost:8080`, пройти встановлення WordPress.
4. Встановити й активувати плагін **Advanced Custom Fields** (Pro, потрібен для Repeater-полів).
5. Активувати тему "Свобода" в Вигляд → Теми.
6. Створити сторінку з шаблоном `front-page.php` і призначити її як головну
   (Налаштування → Читання → Статична сторінка), або встановити цю сторінку домашньою.
7. Додати в `wp-config.php` (поза git):
   ```php
   define( 'MONOBANK_TOKEN', '...' );
   define( 'NOVAPOSHTA_API_KEY', '...' );
   ```

## Структура

- `functions.php` — тільки bootstrap (enqueue, підключення inc/)
- `inc/acf-fields.php` — усі ACF-поля (local field groups) + Options Page
- `inc/cpt-order.php` — CPT `order` + допоміжна `svoboda_create_order()`
- `inc/rest-novaposhta.php` — REST-проксі до Нової Пошти з transient-кешем
- `inc/rest-monobank-webhook.php` — створення інвойсу + webhook з перевіркою підпису
- `template-parts/section-*.php` — 10 секцій лендинга (front-page.php підключає їх по порядку)
- `assets/css/style.css` — усі стилі, CSS custom properties з `docs/figma/design-tokens.md`
- `assets/js/animations.js` — вся скролова/інтерактивна логіка

## Що ще не зроблено (навмисно, потребує рішення власника проекту)

- Форма замовлення зараз шле fetch() напряму на REST-ендпоінт; якщо потрібна
  валідація через Contact Form 7 / WPForms — інтегрувати їхній submit-хук
  з `svoboda_create_order()` замість/поряд з поточним JS-обробником.
- Реальні асети (обкладинка книги, фото автора, гірський пейзаж) — плейсхолдери,
  підключаються через ACF-поля адміністратором сайту.
- Перед продакшеном звірити hover/click Smart Animate переходи кнопок і карток
  відгуків окремим точковим запитом `get_motion_context` в Figma MCP (див.
  `docs/figma/animations.md`, розділ "Рекомендація") — поточний набір анімацій
  покриває лише декоративні loop-ефекти сторінки.
