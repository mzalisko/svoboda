<?php
/**
 * Секція 6: Цитатний блок зі скетч-ілюстрацією ("Свідомість не відображає...").
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text   = get_field( 'sketch_quote_text' );
$sketch = get_field( 'sketch_image' );
?>
<section class="section section--quote" id="quote">
	<div class="section--quote__inner" data-fade-in>
		<?php if ( $sketch ) : ?>
			<div class="section--quote__sketch" data-parallax-layer>
				<img src="<?php echo esc_url( $sketch['url'] ); ?>" alt="">
			</div>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<blockquote class="section--quote__text"><?php echo esc_html( $text ); ?></blockquote>
		<?php endif; ?>
	</div>
</section>
