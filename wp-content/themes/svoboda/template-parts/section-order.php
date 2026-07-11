<?php
/**
 * Секція 2: Замовлення книги — 2 pill-картки цін, "Тираж обмежений", обкладинка.
 *
 * Виконується всередині have_rows('page_sections') циклу (front-page.php),
 * але layout `order` — позиційний маркер без sub-fields: ціни/обкладинка
 * живуть в окремій групі group_order_block (читаються через get_field),
 * бо ті самі поля використовує і фінальна CTA-секція (див. spec-доповнення).
 *
 * Форма замовлення прихована до кліку по pill-картці (дизайн показує лише
 * картки) — клік відкриває панель форми з передвибраною версією книги.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$price_paper = get_field( 'price_paper', get_the_ID() ) ?: 450;
$price_ebook = get_field( 'price_ebook', get_the_ID() ) ?: 290;
$limited     = get_field( 'limited_edition_note', get_the_ID() ) ?: 'Тираж обмежений';
$cover       = get_field( 'book_cover_3d', get_the_ID() );
$cover_url   = $cover ? $cover['url'] : SVOBODA_THEME_URI . '/assets/img/book-cover.png';
$cover_alt   = $cover ? ( $cover['alt'] ?: 'Обкладинка книги «Свобода»' ) : 'Обкладинка книги «Свобода»';
?>
<section class="section section--order" id="order">
	<div class="section--order__inner">
		<div class="section--order__content">
			<h2 class="section--order__title" data-fade-in>Замовити книгу</h2>

			<div class="section--order__pills">
				<button type="button" class="order-pill" data-order-open="paper" data-fade-in>
					<img class="order-pill__icon" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/svg/icon-book.svg' ); ?>" alt="">
					<span class="order-pill__label">Паперова</span>
					<span class="order-pill__price">₴<?php echo esc_html( $price_paper ); ?></span>
				</button>

				<button type="button" class="order-pill" data-order-open="ebook" data-fade-in data-fade-delay="120">
					<img class="order-pill__icon" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/svg/icon-ebook.svg' ); ?>" alt="">
					<span class="order-pill__label">Електронна</span>
					<span class="order-pill__price">₴<?php echo esc_html( $price_ebook ); ?></span>
				</button>
			</div>

			<p class="section--order__limited" data-fade-in data-fade-delay="200">
				<img class="section--order__limited-icon" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/svg/icon-time-limit.svg' ); ?>" alt="">
				<?php echo esc_html( $limited ); ?>
			</p>

		</div>

		<div class="section--order__cover" data-fade-in data-fade-threshold="0.15">
			<img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php echo esc_attr( $cover_alt ); ?>">
		</div>
	</div>

	<div class="order-modal" data-order-modal hidden>
		<button class="order-modal__backdrop" type="button" data-order-close tabindex="-1" aria-hidden="true"></button>

		<div class="order-modal__wrap">
			<button class="order-modal__close" type="button" data-order-close aria-label="<?php esc_attr_e( 'Закрити', 'svoboda' ); ?>">&#10005;</button>

			<div class="order-modal__dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Замовлення книги', 'svoboda' ); ?>">
				<form class="order-form" id="order-form" data-endpoint="<?php echo esc_url( rest_url( 'svoboda/v1/order/create-invoice' ) ); ?>">
					<div class="order-form__variants">
						<label class="variant-row is-checked" data-variant-row>
							<input type="checkbox" name="variant_paper" checked data-variant="paper" data-price="<?php echo esc_attr( $price_paper ); ?>">
							<img class="variant-row__icon" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/svg/icon-book.svg' ); ?>" alt="">
							<span class="variant-row__label">Паперова</span>
							<span class="variant-row__qty" data-qty-wrap>
								<button type="button" data-qty-minus aria-label="<?php esc_attr_e( 'Менше', 'svoboda' ); ?>">&minus;</button>
								<span class="variant-row__qty-label" data-qty-label>1шт</span>
								<button type="button" data-qty-plus aria-label="<?php esc_attr_e( 'Більше', 'svoboda' ); ?>">+</button>
								<input type="hidden" name="qty_paper" value="1" data-qty-input>
							</span>
							<span class="variant-row__price" data-line-price>&#8372;<?php echo esc_html( $price_paper ); ?></span>
						</label>

						<label class="variant-row" data-variant-row>
							<input type="checkbox" name="variant_ebook" data-variant="ebook" data-price="<?php echo esc_attr( $price_ebook ); ?>">
							<img class="variant-row__icon" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/svg/icon-ebook.svg' ); ?>" alt="">
							<span class="variant-row__label">Електронна</span>
							<span class="variant-row__qty" data-qty-wrap>
								<button type="button" data-qty-minus aria-label="<?php esc_attr_e( 'Менше', 'svoboda' ); ?>">&minus;</button>
								<span class="variant-row__qty-label" data-qty-label>1шт</span>
								<button type="button" data-qty-plus aria-label="<?php esc_attr_e( 'Більше', 'svoboda' ); ?>">+</button>
								<input type="hidden" name="qty_ebook" value="1" data-qty-input>
							</span>
							<span class="variant-row__price" data-line-price>&#8372;<?php echo esc_html( $price_ebook ); ?></span>
						</label>
					</div>

					<div class="order-form__total" data-order-total>&#8372;<?php echo esc_html( $price_paper ); ?></div>

					<input type="text" name="customer_name" placeholder="<?php esc_attr_e( 'Ім’я', 'svoboda' ); ?>" autocomplete="name">
					<input type="tel" name="customer_phone" placeholder="<?php esc_attr_e( 'Номер телефону', 'svoboda' ); ?>" autocomplete="tel">
					<input type="email" name="customer_email" placeholder="<?php esc_attr_e( 'Емейл *', 'svoboda' ); ?>" autocomplete="email" required>

					<div class="order-form__np-fields" data-np-fields>
						<input type="text" name="np_city" placeholder="<?php esc_attr_e( 'Місто', 'svoboda' ); ?>" autocomplete="off" data-np-city>
						<select name="np_warehouse" data-np-warehouse disabled>
							<option value=""><?php esc_html_e( 'Відділення Нової Пошти', 'svoboda' ); ?></option>
						</select>
					</div>

					<select name="payment">
						<option value="online"><?php esc_html_e( 'Оплата карткою онлайн', 'svoboda' ); ?></option>
					</select>

					<button type="button" class="order-form__promo-toggle" data-promo-toggle><?php esc_html_e( 'Додати промокод', 'svoboda' ); ?> +</button>
					<div class="order-form__promo" data-promo-field hidden>
						<input type="text" name="promo" placeholder="<?php esc_attr_e( 'Промокод', 'svoboda' ); ?>">
					</div>

					<p class="order-form__error" data-order-error hidden></p>
					<p class="order-form__note"><?php esc_html_e( 'Книгу буде відправлено на пошту', 'svoboda' ); ?></p>

					<button type="submit" class="order-form__submit"><?php esc_html_e( 'Замовити', 'svoboda' ); ?></button>
				</form>
			</div>
		</div>
	</div>

	<div class="order-toast" data-order-toast hidden>
		<svg class="order-toast__icon" viewBox="0 0 90 34" xmlns="http://www.w3.org/2000/svg" fill="none">
			<path d="M6 26 L74 6 L38 22 Z" stroke="#323232" stroke-width="1.4" stroke-linejoin="round"/>
			<path d="M38 22 L42 31 L48 24" stroke="#323232" stroke-width="1.4" stroke-linejoin="round"/>
			<path d="M74 6 L42 31" stroke="#323232" stroke-width="1.4" stroke-linejoin="round"/>
		</svg>
		<span><?php esc_html_e( 'Книгу відправлено на пошту', 'svoboda' ); ?></span>
	</div>
</section>
