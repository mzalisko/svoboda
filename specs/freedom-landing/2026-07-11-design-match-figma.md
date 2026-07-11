# Доведення верстки до відповідності Figma/скріншотів — дизайн-документ

> Доповнення до `specs/freedom-landing/spec.md`. Не замінює його, а деталізує процес
> приведення поточної (мінімальної) верстки теми "Свобода" до повної відповідності
> референс-скріншотам у `screenshots/` (=Figma, fileKey `UpjA6RtySQZAkgWjYUPDAM`).

## Контекст

Поточний стан теми: секції верстаються з fallback-значеннями/порожнім контентом
(fatal error через відсутній ACF вже виправлено окремо — тема активна, сайт віддає 200).
Але верстка й CSS — лише каркас, далекий від фінального дизайну зі скріншотів.

Зіставлення `screenshots/*.png` із поточним кодом виявило дві розбіжності зі spec.md:

1. **Відсутня секція** — `section-8-animation-section-2.png` («Безкоштовний уривок»,
   форма email + текстурна куля + літачок) не входить у список 10 секцій `spec.md` розділ 4
   і не реалізована в `front-page.php`. За координатами Figma вона лежить між
   `transformation` (кінець ~8354px) і `business` (початок 9532px) — незадокументована
   прогалина ~1178px підтверджує, що вузол існує в Figma, просто не був знятий раніше.
2. **Cormorant Garamond ніде не підключений** — ні Google Fonts, ні self-host — усупереч
   TODO в `style.css` і рекомендації self-host у `spec.md` розділ 5.2. Заголовки зараз
   рендеряться резервним Georgia.

## Рішення (погоджено з користувачем)

| Питання | Рішення |
|---|---|
| Обсяг заходу | Поетапно, по секціях, з проміжними звірками — не один суцільний прохід |
| Копірайт зі скріншотів (біо, болі, кроки трансформації, відгуки, бізнес-пункти) | Переносити повністю як реальний контент у ACF-поля |
| Фото (автор, бізнес, гора, обкладинка) | Викачувати з Figma через MCP (`download_assets`) як фінальні асети |
| Анімації | Використати готовий production-mapping з `docs/figma/animations.md`, без повторних `get_motion_context` |
| Нова секція "Безкоштовний уривок" — куди йде email | MVP: зберігати у WP (новий CPT `lead`, без зовнішнього ESP) |
| Шрифт Cormorant Garamond | Self-host (`.woff2` в `assets/fonts/`, `@font-face`) |
| Архітектура ACF-полів | Local JSON (перевести з raw PHP на `acf-json/` + DB-записи, editable в адмінці) |
| Структура контенту головної сторінки | **Flexible Content** замість фіксованого набору окремих полів (рішення прийняте під час виконання Фази 1, після Task 2) |

## Доповнення: Flexible Content замість фіксованих полів (додано під час Фази 1)

`group_front_page_content` перебудовано з ~20 окремих полів на **одне** поле типу Flexible
Content (`page_sections`) із 10 layouts — по одному на кожну секцію лендинга (hero, order,
about, for_you, video, quote, transformation, business, reviews, cta), у тому самому порядку,
що й раніше в `front-page.php`.

**Це скасовує рядок у `specs/freedom-landing/spec.md` розділ 4** ("порядок фіксований... без
перестановки блоків") — тепер редактор керує порядком/наявністю секцій через адмінку, а не
лише код теми.

**Виняток:** layout `order` не має власних sub-fields — це позиційний маркер. Ціни
(`price_paper`, `price_ebook`, `limited_edition_note`, `book_cover_3d`) лишаються в окремій
групі `group_order_block` (не Flexible Content), бо ці ж поля читає й секція `cta` — дублювання
даних по layout-рядках create desync risk.

`front-page.php` замінено з 10 жорстких `get_template_part()` викликів на цикл
`have_rows('page_sections')` / `get_row_layout()`, що диспетчерить у відповідний
`template-parts/section-*.php`. Кожен template-part переводиться з `get_field()` на
`get_sub_field()` **окремо, у своїй фазі** (Hero — у Фазі 1 зараз; інші 8 — коли дійде їхня
черга). До переведення секція просто показує свій хардкоджений fallback-текст (як і зараз) —
це не ламає відображення.

## Повторюваний пайплайн для кожної секції

1. `get_design_context` (Figma MCP) на node-id секції (id в таблиці `docs/figma/design-tokens.md`)
2. `download_assets` для фото/SVG цієї секції → `wp-content/themes/svoboda/assets/{svg,img}/`
3. Правка PHP-шаблону секції (`template-parts/section-*.php`) під фактичну структуру
4. Оновлення CSS секції в `style.css` під точні токени (px-значення, кольори, типографіка)
5. Перенесення реального тексту зі скріншота в ACF-поля (wp-cli)
6. Реалізація анімації секції за мапінгом з `docs/figma/animations.md`
7. Скріншот живої сторінки, звірка з референсом, фіксація розбіжностей

## Фази

**Phase 0 — інфраструктура (перед секціями):**
- Self-host Cormorant Garamond (SemiBold, Medium Italic, Bold Italic)
- Міграція 3 існуючих ACF-груп з raw PHP (`acf_add_local_field_group`) на Local JSON:
  підключити `acf/settings/save_json` + `acf/settings/load_json` на `acf-json/` в темі,
  персистнути групи в БД через `acf_update_field_group()`, прибрати старі
  `acf_add_local_field_group()` виклики з `inc/acf-fields.php`
- Перевірка: `edit.php?post_type=acf-field-group` показує 3 групи, `get_field()` й далі працює,
  головна сторінка й далі 200

**Phase 1** — Header + Hero
**Phase 2** — Order (замовлення книги)
**Phase 3** — About (про автора)
**Phase 4** — For You (ця книга для вас)
**Phase 5** — Video
**Phase 6** — Quote (цитатний блок)
**Phase 7** — Transformation (SVG-маршрут, 6 пунктів)
**Phase 8** — **Нова секція: Lead-magnet "Безкоштовний уривок"**
  - `template-parts/section-lead-magnet.php`
  - Нова ACF-група (через Local JSON, як і решта)
  - Новий CPT `lead` (за зразком `inc/cpt-order.php`) + `inc/rest-lead-magnet.php`
  - Реєстрація секції в `front-page.php` між transformation і business
  - Оновлення `specs/freedom-landing/spec.md` розділ 4 (додати секцію в список)
**Phase 9** — Business (для власників бізнесу)
**Phase 10** — Reviews (карусель відгуків)
**Phase 11** — Final CTA + Footer

## Стан фаз (оновлюється по ходу)

- **Phase 0 — готово.** Шрифти self-hosted; ACF Local JSON; обидві групи полів
  (`group_front_page_content`, `group_order_block`) використовують location-правило
  `page_type == front_page` (правило `page_template == front-page.php` ніколи не
  спрацьовує для сторінки, призначеної головною через Settings → Reading).
- **Phase 1 (Header + Hero) — готово.** Точні координати з
  `untitled/project/Дослідження - Свобода (лендінг).dc.html` (handoff-бандл, він же
  джерело правди замість вичерпаного Figma MCP). Мобільне меню: бургер-іконка
  "рукописний штрих" (3 хвилясті лінії), панель з fade+slide і stagger пунктів.
- **Phase 2 (Order) — готово.** Pill-картки з подвійним штрихом (inset+outset
  box-shadow 1.26px), обкладинка mix-blend multiply (важливо: кінцевий стан
  `[data-fade-in].is-visible` мусить бути `transform: none`, бо будь-який не-none
  transform створює stacking context і ламає blending у нащадків). Форма замовлення
  прихована — відкривається кліком по pill-картці з передвибором версії (весь
  JS-контракт `#order-form`/`data-np-*`/`data-endpoint` збережено).
- **Брейкпоінти:** 1150px — хедер згортається в бургер (3-колонкова nav-сітка не
  влазить раніше за стек контенту); 992px — Hero і Order переходять у вертикальний
  стек (нижче clamp-мінімуми шрифтів переростають %-бокси абсолютної розкладки);
  768px — дрібніші мобільні правки решти секцій.
- **Секції, що ще не в роботі, вимкнені** через `$enabled_sections` у
  `front-page.php` (зараз: `hero`, `order`) — щоб на живому сайті не світилися
  каркасні заглушки.

## Definition of Done (на фазу)

- Секція візуально збігається зі скріншотом
- Анімація секції працює (за мапінгом animations.md)
- Текст — реальний, не плейсхолдер
- Сайт і далі віддає HTTP 200 без PHP-помилок/попереджень у логах
