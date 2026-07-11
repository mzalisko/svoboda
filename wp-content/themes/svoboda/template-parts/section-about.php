<?php
/**
 * Секція 3: Про автора.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$name      = get_field( 'author_name' ) ?: 'Ігор Браславець';
$bio       = get_field( 'author_bio' );
$photo     = get_field( 'author_photo' );
$instagram = get_field( 'author_instagram' );
?>
<section class="section section--about" id="about">
	<div class="section--about__inner" data-fade-in data-fade-threshold="0.2">
		<?php if ( $photo ) : ?>
			<div class="section--about__photo">
				<img src="<?php echo esc_url( $photo['url'] ); ?>" alt="<?php echo esc_attr( $photo['alt'] ?? $name ); ?>">
			</div>
		<?php endif; ?>

		<div class="section--about__content">
			<h2 class="section--about__title">Про автора</h2>
			<h3 class="section--about__name"><?php echo esc_html( $name ); ?></h3>

			<?php if ( $bio ) : ?>
				<div class="section--about__bio"><?php echo wp_kses_post( $bio ); ?></div>
			<?php endif; ?>

			<?php if ( $instagram ) : ?>
				<a class="section--about__instagram" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer">
					Instagram
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
