# Animations — точні keyframe-дані з Figma (get_motion_context, recursive)

fileKey: `UpjA6RtySQZAkgWjYUPDAM`, root node: `180:62`
Базовий цикл усіх знайдених мотивів: **3000ms**, `animation-iteration-count: infinite`, `animation-direction: alternate` (boomerang-loop у самій Figma — це декоративні прев'ю-анімації дизайнера, не буквальний scroll-trigger; для сайту переосмислюємо нижче в розділі "Продакшн-мапінг").

## 1. Цитата "❝" в Hero (node `180:79`)
```css
.hero-quote {
  animation: kf_180_79_translate 3s linear, kf_180_79_scale 3s linear;
  animation-iteration-count: infinite;
  animation-direction: alternate;
}
@keyframes kf_180_79_translate {
  0%      { animation-timing-function: ease-out; translate: 60px 0px; }
  33.987% { translate: 0px 0px; }
  100%    { translate: 0px 0px; }
}
@keyframes kf_180_79_scale {
  0%      { animation-timing-function: ease-out; scale: 0.72 0.72; }
  33.987% { scale: 1 1; }
  100%    { scale: 1 1; }
}
```
**Продакшн-мапінг**: одноразовий fade/scale-in при появі Hero (не інфініт-луп) — прибрати `infinite`/`alternate`, запустити один раз при завантаженні сторінки, delay після заголовка.

## 2. Фото-колаж у блоці "Ця книга для вас" (3 фото, nodes `193:290`, `193:291`, `193:292`)
```css
@keyframes kf_image64_translate { /* 193:291 */
  0%      { animation-timing-function: step-end; translate: 0px 7.962px; }
  47.007% { translate: 0px 0px; }
  100%    { translate: 0px 0px; }
}
@keyframes kf_image65_translate { /* 193:292 */
  0%      { animation-timing-function: step-end; translate: 0px 15.932px; }
  47.864% { translate: 0px 0px; }
  100%    { translate: 0px 0px; }
}
@keyframes kf_image63_translate { /* 193:290 */
  0%      { animation-timing-function: step-end; translate: -11.042px 0px; }
  47.864% { translate: 0px 0px; }
  100%    { translate: 0px 0px; }
}
```
**Продакшн-мапінг**: це "збірка" фотоколажу — реалізувати як одноразову появу елементів по IntersectionObserver (кожне фото "з'їжджається" на своє місце), threshold 0.2, stagger ~100–150ms між фото.

## 3. Цитатний блок зі скетчем (nodes `217:499`, `217:500`)
```css
@keyframes kf_image69_scale { /* 217:500 */
  0%      { scale: 1.13 1.13; }
  50.304% { scale: 1 1; }
  100%    { scale: 1 1; }
}
@keyframes kf_image68_translate { /* 217:499 */
  0%      { animation-timing-function: step-end; translate: 0px 10.799px; }
  26.353% { translate: 0px -0.525px; }
  40.16%  { translate: 0px -6.563px; }
  50.304% { translate: 0px -11px; }
  100%    { translate: 0px -11px; }
}
```
**Продакшн-мапінг**: паралакс-зсув скетч-шарів при скролі повз секцію (2 шари рухаються з різною швидкістю) — реалізувати через `translate3d` прив'язаний до scroll progress секції, а не через infinite CSS-loop.

## 4. Куля/планета в кінці лінії трансформації (node `213:458`, "ball")
```css
@keyframes kf_ball_opacity {
  0%      { opacity: 0; }
  50.304% { animation-timing-function: ease-out; opacity: 0; }
  100%    { opacity: 1; }
}
```
**Продакшн-мапінг**: планета плавно проявляється (opacity 0→1) синхронно з моментом, коли SVG-лінія і літачок "долітають" до кінця маршруту.

## 5. SVG-лінія маршруту трансформації (node `232:754`, "Vector 11")
```css
/* Вимагає pathLength="1" на <path> у SVG */
@keyframes kf_line_trim {
  0%      { animation-timing-function: ease-out; stroke-dasharray: 0 1; stroke-dashoffset: -1; visibility: hidden; }
  50.304% { stroke-dasharray: 1 1; stroke-dashoffset: 0; visibility: visible; }
  100%    { stroke-dasharray: 1 1; stroke-dashoffset: 0; visibility: visible; }
}
```
**Продакшн-мапінг**: це ключова анімація секції 7 ТЗ ("промальовка лінії залежно від скролу"). У продакшені керувати `stroke-dashoffset` напряму через JS, обчислюючи прогрес скролу секції (0–1) і застосовуючи цей прогрес до `stroke-dashoffset` замість CSS keyframes на таймері. Точки шляху (6 пунктів) підсвічуються, коли прогрес лінії досягає їхньої позиції (можна порахувати позицію кожної точки як частку довжини path).

## Timeline cohort (координація)
```json
{
  "rootNodeId": "180:62",
  "durationMs": 3000,
  "loopMode": "boomerang",
  "memberNodeIds": ["180:79","193:291","193:292","193:290","217:500","217:499","213:458","232:754"]
}
```
Усі 8 анімованих вузлів належать до одного координованого таймлайну в Figma (для прев'ю-демонстрації дизайнеру). На сайті ці елементи належать до **різних** секцій сторінки і мають запускатися **незалежно**, по IntersectionObserver / scroll-position кожної секції — не одним спільним таймером.

## Анімації, описані текстом у ТЗ, але не зняті як Figma-timeline (реалізуються на стороні розробки за загальним описом)
- Header: компактність/напівпрозорість при скролі (JS `scroll` listener + toggle класу)
- Hero headings: послідовний fade-in зі зсувом (stagger, delay між елементами)
- 3D-нахил обкладинки книги за курсором (vanilla JS, `mousemove` → `transform: perspective(...) rotateX/rotateY`)
- Кнопка відтворення відео: pulse на hover (CSS `@keyframes pulse` на `box-shadow`/`scale`), заміна прев'ю на `<video>`/embed при кліку, lazy-load
- Карусель відгуків: власний vanilla JS slider (свайпи touch, стрілки, крапки пагінації) — без Swiper/Slick
- Фінальна CTA: паралакс фону (`background-attachment: fixed` або JS transform на scroll)

## Рекомендація
Перед імплементацією кожної анімованої секції — звірити ще раз конкретний вузол через `get_motion_context` (не рекурсивно, точковий виклик на потрібний layer), оскільки в Figma можуть бути додаткові Smart Animate переходи між станами компонентів (наприклад, hover-стан кнопок, open/close стан елементів), які не потрапляють у "decorative loop" інвентар, знятий вище.
