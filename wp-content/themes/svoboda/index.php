<?php
/**
 * Фолбек-шаблон (Template Hierarchy). Тема односторінкова (front-page.php),
 * цей файл потрібен лише для сумісності — напр. попередній перегляд WP,
 * пошук чи 404 за замовчуванням.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="landing landing--fallback">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
			<?php
		endwhile;
		?>
	<?php else : ?>
		<p><?php esc_html_e( 'Нічого не знайдено.', 'svoboda' ); ?></p>
	<?php endif; ?>
</main>

<?php
get_footer();
