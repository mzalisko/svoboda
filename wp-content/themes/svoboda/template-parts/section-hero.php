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
$subtitle = get_sub_field( 'hero_subtitle' );
$quote    = get_sub_field( 'hero_quote' );
?>
<section class="section section--hero" id="hero">
	<?php
	// Десктоп: композит розрізано на 2 шари. Папір "прориває" лівий край
	// САМОГО ЕКРАНА (прямий шар секції, поза центрованою 1920-канвою),
	// літак -- окремий прозорий спрайт, що летить по скролу (data-hero-fly).
	?>
	<?php
	// Пробоїна порізана на 4 радіальні сегменти навколо гирла отвору:
	// на старті вони стиснуті до центру ("закритий" отвір), у момент пострілу
	// розгортаються назовні з перельотом і сходяться в піксельний оригінал.
	?>
	<div class="section--hero__paper" aria-hidden="true">
		<img class="section--hero__paper-piece section--hero__paper-piece--tl" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/img/hero-paper-tl.png' ); ?>" alt="">
		<img class="section--hero__paper-piece section--hero__paper-piece--tr" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/img/hero-paper-tr.png' ); ?>" alt="">
		<img class="section--hero__paper-piece section--hero__paper-piece--bl" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/img/hero-paper-bl.png' ); ?>" alt="">
		<img class="section--hero__paper-piece section--hero__paper-piece--br" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/img/hero-paper-br.png' ); ?>" alt="">
	</div>

	<div class="section--hero__inner">
		<div class="section--hero__fly" data-hero-fly aria-hidden="true">
			<img src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/img/hero-plane-fly.png' ); ?>" alt="">
		</div>

		<div class="section--hero__plane">
			<?php // Мобільний стек (<=992px): статичний курований кадр 375:209 — без шарів/польоту. ?>
			<img
				src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/img/hero-plane-mobile.png' ); ?>"
				alt=""
				class="section--hero__plane-img"
				loading="eager"
			>
		</div>

		<div class="section--hero__heading">
			<h1 class="section--hero__title" data-fade-in>
				<?php echo esc_html( $title ); ?>
			</h1>

			<?php if ( $subtitle ) : ?>
				<p class="section--hero__subtitle" data-fade-in data-fade-delay="150">
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
