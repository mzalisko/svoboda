<?php
/**
 * Секція 9: Карусель відгуків — заголовок між стрілками, 3 картки видимі одночасно,
 * ромбоподібні точки, dark-картки з зірковим фоном.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reviews = get_field( 'reviews' ) ?: array();
?>
<section class="section section--reviews" id="reviews">
	<div class="section--reviews__inner">

		<!-- Header: ← ВІДГУКИ → + dots -->
		<div class="section--reviews__header">
			<button type="button" class="reviews-slider__arrow reviews-slider__arrow--prev" data-slider-prev aria-label="Попередній відгук">&#8592;</button>
			<h2 class="section--reviews__title">Відгуки</h2>
			<button type="button" class="reviews-slider__arrow reviews-slider__arrow--next" data-slider-next aria-label="Наступний відгук">&#8594;</button>
		</div>

		<!-- Dots (diamonds) -->
		<div class="section--reviews__dots" role="tablist" aria-label="Пагінація відгуків">
			<?php foreach ( $reviews as $index => $review ) : ?>
				<button
					type="button"
					class="section--reviews__dot<?php echo $index === 0 ? ' is-active' : ''; ?>"
					data-slider-dot="<?php echo esc_attr( $index ); ?>"
					aria-label="Перейти до відгуку <?php echo esc_attr( $index + 1 ); ?>"
					role="tab"
					aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
				></button>
			<?php endforeach; ?>
		</div>

		<!-- 3-колонкова карусель -->
		<div class="reviews-slider" data-reviews-slider>
			<div class="reviews-slider__track" data-slider-track>
				<?php foreach ( $reviews as $index => $review ) : ?>
					<div class="reviews-slider__slide" data-slider-slide="<?php echo esc_attr( $index ); ?>">
						<div class="reviews-slider__card">
							<?php if ( ! empty( $review['photo'] ) ) : ?>
								<img
									class="reviews-slider__photo lozad"
									src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
									data-src="<?php echo esc_url( $review['photo']['url'] ); ?>"
									alt="<?php echo esc_attr( $review['photo']['alt'] ?? '' ); ?>"
									width="64"
									height="64"
								>
							<?php endif; ?>
							<blockquote class="reviews-slider__text"><?php echo esc_html( $review['text'] ?? '' ); ?></blockquote>
							<p class="reviews-slider__author"><?php echo esc_html( $review['author'] ?? '' ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

	</div>
</section>
