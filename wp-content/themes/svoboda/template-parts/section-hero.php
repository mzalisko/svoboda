<?php
/**
 * Секція 1: Hero — заголовок "СВОБОДА", підзаголовок, паперовий літачок, цитата.
 *
 * Виконується всередині have_rows('page_sections') циклу (front-page.php),
 * тому дані читаються через get_sub_field(), а не get_field().
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title    = get_sub_field( 'hero_title' ) ?: 'СВОБОДА';
$subtitle = get_sub_field( 'hero_subtitle' ) ?: "Шлях за межі реального.  Шлях на грані можливого.";
$quote    = get_sub_field( 'hero_quote' ) ?: "Ми не просто бачимо як події відбуваються з нами. Ми їх створюємо. Своїм внутрішнім станом, думками, вірою.<br><br>Ви дізнаєтеся як підкорити реальність.";
?>
<section class="section section--hero" id="hero">
	<div class="section--hero__plane-container" aria-hidden="true">
		<!-- Паперова дірка (вибоїна) -->
		<div class="section--hero__paper">
			<img class="section--hero__paper-img" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/img/hero-paper.webp' ); ?>" alt="" loading="eager" width="776" height="1540">
		</div>

		<!-- Літачок -->
		<div class="section--hero__fly" data-hero-fly>
			<img src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/img/hero-plane-fly.png' ); ?>" alt="" loading="eager" width="1802" height="560">
		</div>
	</div>

	<div class="section--hero__inner">

		<div class="section--hero__heading">
			<h1 class="section--hero__title">
				<?php echo esc_html( $title ); ?>
			</h1>

			<?php if ( $subtitle ) : ?>
				<p class="section--hero__subtitle">
					<?php echo esc_html( $subtitle ); ?>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( $quote ) : ?>
			<blockquote class="section--hero__quote">
				<img
					src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/svg/hero-quote-mark.svg' ); ?>"
					alt=""
					class="section--hero__quote-mark"
					data-fade-in
					data-fade-delay="300"
				>
				<div class="section--hero__quote-text">
					<?php echo wp_kses_post( $quote ); ?>
				</div>
			</blockquote>
		<?php endif; ?>
	</div>
</section>
