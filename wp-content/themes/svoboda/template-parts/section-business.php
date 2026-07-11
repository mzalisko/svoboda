<?php
/**
 * Секція 8: Для власників бізнесу / Для кого — нумерація 01/02/03, фото, кнопка "Презентація".
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$points           = get_field( 'business_points' ) ?: array();
$photo            = get_field( 'business_photo' );
$presentation_url = get_field( 'business_presentation_url' );
?>
<section class="section section--business" id="business">
	<div class="section--business__inner" data-fade-in data-fade-threshold="0.2">
		<h2 class="section--business__title">Для власників бізнесу</h2>

		<?php if ( $photo ) : ?>
			<div class="section--business__photo">
				<img src="<?php echo esc_url( $photo['url'] ); ?>" alt="">
			</div>
		<?php endif; ?>

		<?php if ( $points ) : ?>
			<ol class="section--business__points">
				<?php foreach ( $points as $index => $point ) : ?>
					<li class="section--business__point">
						<span class="section--business__point-number"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<h3 class="section--business__point-title"><?php echo esc_html( $point['title'] ?? '' ); ?></h3>
						<p class="section--business__point-text"><?php echo esc_html( $point['text'] ?? '' ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>

		<?php if ( $presentation_url ) : ?>
			<a class="btn-presentation" href="<?php echo esc_url( $presentation_url ); ?>" target="_blank" rel="noopener noreferrer">
				Презентація
			</a>
		<?php endif; ?>
	</div>
</section>
