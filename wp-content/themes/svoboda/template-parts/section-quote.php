<?php
/**
 * Секція 6: Цитатний блок — великий заголовок UPPERCASE + курсивне акцентування + скетч-ілюстрація.
 * Layout: лівий блок (лапки + текст), правий блок (скетч).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sketch = get_field( 'sketch_image' );
?>
<section class="section section--quote" id="quote">
	<div class="section--quote__inner" data-fade-in>
		<div class="section--quote__body">
			<span class="section--quote__marks" aria-hidden="true">&#x201C;&#x201C;</span>
			<blockquote class="section--quote__text">
				Свідомість не відображає реальність. <em>Вона її створює.</em>
			</blockquote>
		</div>

		<?php if ( $sketch ) : ?>
			<div class="section--quote__sketch" data-parallax-layer>
				<img
					class="lozad"
					src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
					data-src="<?php echo esc_url( $sketch['url'] ); ?>"
					alt="<?php echo esc_attr( $sketch['alt'] ?? '' ); ?>"
					width="<?php echo esc_attr( $sketch['width'] ?? '' ); ?>"
					height="<?php echo esc_attr( $sketch['height'] ?? '' ); ?>"
				>
			</div>
		<?php endif; ?>
	</div>
</section>
