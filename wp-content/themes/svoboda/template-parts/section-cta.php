<?php
/**
 * Секція 10: Фінальна CTA — фон «людина на вершині гори», заголовок, ті самі pill-кнопки замовлення.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$background  = get_field( 'cta_background' );
$title       = get_field( 'cta_title' ) ?: 'Замовити книгу';
$price_paper = get_field( 'price_paper' ) ?: 450;
$price_ebook = get_field( 'price_ebook' ) ?: 290;

$icon_paper = SVOBODA_THEME_URI . '/assets/svg/icon-book-paper.svg';
$icon_ebook = SVOBODA_THEME_URI . '/assets/svg/icon-book-ebook.svg';
?>
<section
	class="section section--cta"
	id="cta"
	<?php if ( $background ) : ?>style="background-image:url('<?php echo esc_url( $background['url'] ); ?>');"<?php endif; ?>
>
	<div class="section--cta__inner">
		<h2 class="section--cta__title"><?php echo esc_html( $title ); ?></h2>

		<div class="section--cta__actions">
			<a href="#order" class="order-pill">
				<img class="order-pill__icon" src="<?php echo esc_url( $icon_paper ); ?>" alt="" aria-hidden="true">
				<span class="order-pill__label">Паперова</span>
				<span class="order-pill__price"><?php echo esc_html( $price_paper ); ?> ₴</span>
			</a>
			<a href="#order" class="order-pill">
				<img class="order-pill__icon" src="<?php echo esc_url( $icon_ebook ); ?>" alt="" aria-hidden="true">
				<span class="order-pill__label">Електронна</span>
				<span class="order-pill__price"><?php echo esc_html( $price_ebook ); ?> ₴</span>
			</a>
		</div>

		<p class="section--cta__limited">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/><polyline points="12 6 12 12 16 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
			Тираж обмежений
		</p>
	</div>
</section>
