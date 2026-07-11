<?php
/**
 * Секція 7: Трансформація — звивиста SVG-лінія маршруту, 6 пунктів шляху,
 * анімований літачок "приземляється" на текстуровану планету.
 *
 * SVG-лінія промальовується залежно від прогресу скролу секції (scroll-linked
 * stroke-dashoffset, керується JS у animations.js) — див. docs/figma/animations.md, п.5.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$steps = get_field( 'transformation_steps' ) ?: array();
?>
<section class="section section--transformation" id="transformation" data-scroll-line-section>
	<div class="section--transformation__inner">
		<h2 class="section--transformation__title">Трансформація</h2>

		<div class="section--transformation__route">
			<svg class="section--transformation__line" viewBox="0 0 1200 600" aria-hidden="true">
				<path
					class="transformation-line"
					data-scroll-line-path
					pathLength="1"
					d="M40,550 C300,500 300,300 600,300 C900,300 900,80 1160,60"
					fill="none"
					stroke="var(--red)"
					stroke-width="3"
				/>
			</svg>

			<div class="section--transformation__plane" data-scroll-line-plane aria-hidden="true">
				<?php get_template_part( 'template-parts/svg-plane' ); ?>
			</div>

			<div class="section--transformation__ball" data-scroll-line-ball aria-hidden="true"></div>

			<ol class="section--transformation__steps">
				<?php foreach ( $steps as $index => $step ) : ?>
					<li class="section--transformation__step" data-scroll-line-point="<?php echo esc_attr( $index ); ?>">
						<span class="section--transformation__step-number"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<h3 class="section--transformation__step-title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
						<p class="section--transformation__step-text"><?php echo esc_html( $step['text'] ?? '' ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
</section>
