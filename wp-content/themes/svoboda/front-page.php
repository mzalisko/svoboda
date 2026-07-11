<?php
/**
 * One-page лендинг "Свобода".
 *
 * Порядок і наявність секцій керується через ACF Flexible Content (`page_sections`)
 * в адмінці (specs/freedom-landing/2026-07-11-design-match-figma.md, доповнення
 * "Flexible Content замість фіксованих полів") — редактор може змінювати порядок/
 * набір секцій без правки коду.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="landing">
	<?php
	// Назва layout'у в ACF ("for_you") не завжди збігається з іменем файлу шаблону
	// ("section-for-you.php") — мапа покриває розбіжності.
	$section_template_slugs = array(
		'for_you' => 'for-you',
	);

	// Секції рендеряться по фазах (specs/freedom-landing/2026-07-11-design-match-figma.md).
	// Ще не доведені до пікселя фази навмисно вимкнені тут (не видалені -- щойно
	// їхня фаза буде готова, просто додати назву layout'у в цей масив).
	$enabled_sections = array( 'hero', 'order' );

	if ( have_rows( 'page_sections' ) ) :
		while ( have_rows( 'page_sections' ) ) :
			the_row();
			$layout = get_row_layout();

			if ( ! in_array( $layout, $enabled_sections, true ) ) {
				continue;
			}

			$slug = $section_template_slugs[ $layout ] ?? $layout;
			get_template_part( 'template-parts/section', $slug );
		endwhile;
	endif;
	?>
</main>

<?php
get_footer();
