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
				<img class="lozad" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?php echo esc_url( $sketch['url'] ); ?>" alt="<?php echo esc_attr( $sketch['alt'] ?? '' ); ?>" width="<?php echo esc_attr( $sketch['width'] ?? '' ); ?>" height="<?php echo esc_attr( $sketch['height'] ?? '' ); ?>">
			</div>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<blockquote class="section--quote__text"><?php echo esc_html( $text ); ?></blockquote>
		<?php endif; ?>
	</div>
</section>
