<?php
/**
 * Секція 8: Для власників бізнесу — великий заголовок, пронумеровані пункти, фото, кнопка.
 * Layout: ліва колонка (заголовок + підзаголовок + пункти + CTA), права — фото.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$points           = get_field( 'business_points' ) ?: array();
$photo            = get_field( 'business_photo' );
$subtitle         = get_field( 'business_subtitle' ) ?: 'Інструменти — це 10%. Решта — хто ними керує';
$presentation_url = get_field( 'business_presentation_url' );
$cta_label        = get_field( 'business_cta_label' ) ?: 'Стратегічний архітектор для бізнесу';
?>
<section class="section section--business" id="business">
	<div class="section--business__inner" data-fade-in data-fade-threshold="0.15">

		<!-- Ліва колонка -->
		<div class="section--business__left">
			<h2 class="section--business__title">Для власників бізнесу</h2>
			<p class="section--business__subtitle"><?php echo esc_html( $subtitle ); ?></p>

			<?php if ( $points ) : ?>
				<ol class="section--business__points">
					<?php foreach ( $points as $index => $point ) : ?>
						<li class="section--business__point">
							<span class="section--business__point-number"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
							<div>
								<h3 class="section--business__point-title"><?php echo esc_html( $point['title'] ?? '' ); ?></h3>
								<p class="section--business__point-text"><?php echo esc_html( $point['text'] ?? '' ); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>

			<div class="section--business__cta">
				<span class="section--business__cta-label">
					<?php echo esc_html( $cta_label ); ?> &nbsp;&#8594;
				</span>
				<?php if ( $presentation_url ) : ?>
					<a class="btn-presentation" href="<?php echo esc_url( $presentation_url ); ?>" target="_blank" rel="noopener noreferrer">
						Презентація
					</a>
				<?php endif; ?>
			</div>
		</div>

		<!-- Права колонка: фото -->
		<?php if ( $photo ) : ?>
			<div class="section--business__photo">
				<img
					class="lozad"
					src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
					data-src="<?php echo esc_url( $photo['url'] ); ?>"
					alt="<?php echo esc_attr( $photo['alt'] ?? '' ); ?>"
					width="<?php echo esc_attr( $photo['width'] ?? '' ); ?>"
					height="<?php echo esc_attr( $photo['height'] ?? '' ); ?>"
				>
			</div>
		<?php endif; ?>

	</div>
</section>
