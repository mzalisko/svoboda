<?php
/**
 * Секція 4: "Ця книга для вас, якщо ви відчуваєте" — список болей + фотоколаж (3 фото).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pain_points = get_field( 'pain_points' ) ?: array();
?>
<section class="section section--for-you" id="for-you">
	<div class="section--for-you__inner" data-fade-in data-fade-threshold="0.2">
		<h2 class="section--for-you__title">Ця книга для вас, якщо ви відчуваєте</h2>

		<?php if ( $pain_points ) : ?>
			<ul class="section--for-you__list">
				<?php foreach ( $pain_points as $point ) : ?>
					<li class="section--for-you__list-item">
						<span class="section--for-you__marker" aria-hidden="true">&#9670;</span>
						<?php echo esc_html( $point['text'] ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="section--for-you__collage">
			<div class="section--for-you__collage-photo" data-collage-photo="1"></div>
			<div class="section--for-you__collage-photo" data-collage-photo="2"></div>
			<div class="section--for-you__collage-photo" data-collage-photo="3"></div>
			<svg class="section--for-you__collage-line" viewBox="0 0 400 200" aria-hidden="true">
				<path d="M20,180 C120,20 280,20 380,120" fill="none" stroke="var(--red)" stroke-width="2"/>
			</svg>
		</div>
	</div>
</section>
