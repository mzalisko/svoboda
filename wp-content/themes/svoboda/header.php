<?php
/**
 * Header шаблон.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	// Preload шрифтів верхнього екрана: без цього font-display:swap показує
	// fallback-шрифт і сторінка помітно "стрибає", коли Cormorant довантажиться.
	$svoboda_preload_fonts = array(
		'cormorant-garamond-600-normal-cyrillic.woff2',
		'cormorant-garamond-600-normal-latin.woff2',
		'cormorant-garamond-500-italic-cyrillic.woff2',
		'cormorant-garamond-500-italic-latin.woff2',
	);
	foreach ( $svoboda_preload_fonts as $svoboda_font ) :
		?>
		<link rel="preload" href="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/fonts/' . $svoboda_font ); ?>" as="font" type="font/woff2" crossorigin>
	<?php endforeach; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="site-header" data-scroll-compact>
	<div class="site-header__inner">
		<nav class="site-header__nav site-header__nav--left" aria-label="<?php esc_attr_e( 'Навігація: про книгу', 'svoboda' ); ?>">
			<ul class="site-header__nav-list">
				<li><a href="#hero">Про книгу</a></li>
				<li><a href="#for-you">Що всередені</a></li>
				<li><a href="#about">Про автора</a></li>
			</ul>
		</nav>

		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php bloginfo( 'name' ); ?>
		</a>

		<nav class="site-header__nav site-header__nav--right" aria-label="<?php esc_attr_e( 'Навігація: дії', 'svoboda' ); ?>">
			<ul class="site-header__nav-list">
				<li><a href="#business">Для кого</a></li>
				<li><a href="#reviews">Відгуки</a></li>
				<li><a href="#order">Замовити</a></li>
			</ul>
		</nav>

		<button
			class="site-header__burger"
			type="button"
			data-burger-toggle
			aria-label="<?php esc_attr_e( 'Відкрити меню', 'svoboda' ); ?>"
			aria-expanded="false"
			aria-controls="mobile-nav"
		>
			<img class="site-header__burger-icon site-header__burger-icon--open" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/svg/icon-burger.svg' ); ?>" alt="">
			<img class="site-header__burger-icon site-header__burger-icon--close" src="<?php echo esc_url( SVOBODA_THEME_URI . '/assets/svg/icon-close.svg' ); ?>" alt="">
		</button>
	</div>

	<nav class="site-header__mobile-nav" id="mobile-nav" data-mobile-nav aria-label="<?php esc_attr_e( 'Мобільна навігація', 'svoboda' ); ?>">
		<ul class="site-header__mobile-nav-list">
			<li><a href="#hero">Про книгу</a></li>
			<li><a href="#for-you">Що всередені</a></li>
			<li><a href="#about">Про автора</a></li>
			<li><a href="#business">Для кого</a></li>
			<li><a href="#reviews">Відгуки</a></li>
			<li><a href="#order">Замовити</a></li>
		</ul>
	</nav>
</header>
