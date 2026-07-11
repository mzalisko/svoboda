<?php
/**
 * Секція 9: Карусель відгуків — темний зоряний фон, стрілки, крапки пагінації.
 * Власний vanilla JS slider (без Swiper/Slick) — див. assets/js/animations.js.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reviews = get_field( 'reviews' ) ?: array();
?>
<section class="section section--reviews" id="reviews">
	<div class="section--reviews__inner">
		<h2 class="section--reviews__title">Відгуки</h2>

		<div class="reviews-slider" data-reviews-slider>
			<button type="button" class="reviews-slider__arrow reviews-slider__arrow--prev" data-slider-prev aria-label="Попередній відгук">&#10094;</button>

			<div class="reviews-slider__track" data-slider-track>
				<?php foreach ( $reviews as $index => $review ) : ?>
					<div class="reviews-slider__slide" data-slider-slide="<?php echo esc_attr( $index ); ?>">
						<?php if ( ! empty( $review['photo'] ) ) : ?>
							<img class="reviews-slider__photo" src="<?php echo esc_url( $review['photo']['url'] ); ?>" alt="">
						<?php endif; ?>
						<blockquote class="reviews-slider__text"><?php echo esc_html( $review['text'] ?? '' ); ?></blockquote>
						<p class="reviews-slider__author"><?php echo esc_html( $review['author'] ?? '' ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>

			<button type="button" class="reviews-slider__arrow reviews-slider__arrow--next" data-slider-next aria-label="Наступний відгук">&#10095;</button>

			<div class="reviews-slider__dots" data-slider-dots>
				<?php foreach ( $reviews as $index => $review ) : ?>
					<button type="button" class="reviews-slider__dot" data-slider-dot="<?php echo esc_attr( $index ); ?>" aria-label="Перейти до відгуку <?php echo esc_attr( $index + 1 ); ?>"></button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
