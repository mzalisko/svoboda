# Design Tokens — знято з Figma MCP (get_design_context)

Джерело: fileKey `UpjA6RtySQZAkgWjYUPDAM`, node `180:62` ("Desktop - 4", 1920×12818)

## Кольори
```css
:root {
  --red: #F01212;
  --red-90: rgba(240, 18, 18, 0.9);   /* заголовки, ціни, кнопки, цитати */
  --black: #323232;                    /* основний текст */
  --gray: #606060;                     /* другорядний текст ("Тираж обмежений" тощо) */
  --bg-alt: #F5F5F5;                   /* фон альтернативних секцій */
  --bg-base: #FFFFFF;
  --accent-red-alt: #ff383c;           /* акцентна CTA-кнопка "Презентація" (окремий відтінок) */
}
```

## Типографіка (точні значення з інспектора)
- **H1 Hero "СВОБОДА"**: Cormorant Garamond SemiBold, 134.79px, line-height 125.972px, tracking 2.6958px, колір rgba(240,18,18,0.9), text-align right, uppercase
- **H2 секцій** ("Про автора", "Ви отримаєте трансформацію", "Відгуки" тощо): Cormorant Garamond SemiBold, 100px, line-height: none (leading-none), tracking 2px, uppercase, колір #323232
- **Підзаголовок Hero**: Times New Roman Regular, 45.35px, line-height 1.1, tracking 0.907px
- **Nav-посилання**: Times New Roman Regular, 24px, gap 40.311px
- **Body/цінники**: Times New Roman Regular/Bold, 20–24px
- **Цитати (italic)**: Cormorant Medium/Bold Italic, 30px, колір rgba(0,0,0,0.8)

## Кнопки
### Кнопка-пігулка замовлення (outline)
```css
.btn-order {
  border: 2.519px solid rgba(240, 18, 18, 0.9);
  border-radius: 57.947px;
  padding: 24px 30px;
  background: transparent;
  display: flex;
  align-items: center;
  gap: 12px;
}
```
### Кнопка "ОТРИМАТИ" (solid, hero-form)
```css
.btn-solid-white {
  background: rgba(255,255,255,0.9);
  border-radius: 51px;
  padding: 20px 24px;
  color: rgba(240,18,18,0.9);
  font: 20px "Times New Roman", serif;
  letter-spacing: 3.6px;
}
```
### Кнопка "Презентація" (solid red, для business-секції)
```css
.btn-presentation {
  background: #ff383c;
  border-radius: 79px;
  padding: 20px 24px;
  height: 67px;
  width: 297px;
  color: #fff;
  font: 20px "Times New Roman", serif;
  letter-spacing: 3.6px;
  text-transform: uppercase;
}
```

## Секції та точні координати (для верстки й перевірки скріншотами)
| Секція | node-id | top (px, canvas 1920 width) | height |
|---|---|---|---|
| Hero + Header | 180:63 / 180:68 | 0–~1138 | — |
| Замовлення книги (order block) | 193:91 | 1138 | 1137 |
| Про автора | 193:229 | ~2275 | 1200 |
| "Ця книга для вас, якщо..." | 193:421 | ~3475 | 1186 |
| Відеоблок | 200:431 | 5141 | 826 |
| Цитатний блок ("Свідомість не відображає...") | 207:20 | 6127 | 417 |
| Трансформація (6 пунктів + SVG-лінія) | 207:43 | 6696 | 1658 |
| Для власників бізнесу | 231:17 | 9532 | 1262 |
| Відгуки (карусель) | 271:12 | 10954 | 827 |
| Фінальна CTA (фото гори) | 276:71 | 11977 | 841 |

> Ці значення — з десктопної версії (frame `180:62`, 1920px канва). Мобільна версія лежить в окремому фреймі `347:672` ("mobile", 375×10350) — рекомендується зняти design-context і для нього окремо перед версткою адаптиву.

## Зображення/асети, знайдені в дизайні (потребують заміни на фінальні або залишення як плейсхолдер)
- Ілюстрація паперового літачка (Hero) — кастомний SVG
- Обкладинка книги (3D-візуалізація) — `ChatGPT Image ...Photoroom` — фінальний рендер від клієнта
- Фото автора (природа, портрет) — плейсхолдер до отримання фінальних фото
- Фото "гірський пейзаж" (фінальна CTA) — плейсхолдер (Unsplash/Pexels)
- Іконки: noun-book, noun-ebook, noun-time-limit — з Noun Project, потрібно перевірити ліцензію перед продакшеном
