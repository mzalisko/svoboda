<?php
/**
 * Секція 10: Фінальна CTA — фон "людина на вершині гори", ті самі кнопки замовлення.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$background  = get_field( 'cta_background' );
$title       = get_field( 'cta_title' );
$price_paper = get_field( 'price_paper' ) ?: 450;
$price_ebook = get_field( 'price_ebook' ) ?: 290;
?>
<section
	class="section section--cta"
	id="cta"
	data-parallax-bg
	<?php if ( $background ) : ?>style="background-image:url('<?php echo esc_url( $background['url'] ); ?>');"<?php endif; ?>
>
	<div class="section--cta__inner">
		<?php if ( $title ) : ?>
			<h2 class="section--cta__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>

		<div class="section--cta__actions">
			<a href="#order" class="btn-order">Паперова — <?php echo esc_html( $price_paper ); ?> ₴</a>
			<a href="#order" class="btn-order">Електронна — <?php echo esc_html( $price_ebook ); ?> ₴</a>
		</div>
	</div>
</section>
