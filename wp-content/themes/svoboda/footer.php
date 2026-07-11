<?php
/**
 * Footer шаблон.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$footer_phone   = function_exists( 'get_field' ) ? get_field( 'footer_phone', 'option' ) : '';
$footer_email   = function_exists( 'get_field' ) ? get_field( 'footer_email', 'option' ) : '';
$footer_socials = function_exists( 'get_field' ) ? get_field( 'footer_socials', 'option' ) : array();
?>
	<footer class="site-footer">
		<div class="site-footer__inner">
			<p class="site-footer__copy">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
			</p>

			<?php if ( $footer_phone || $footer_email ) : ?>
				<div class="site-footer__contacts">
					<?php if ( $footer_phone ) : ?>
						<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $footer_phone ) ); ?>"><?php echo esc_html( $footer_phone ); ?></a>
					<?php endif; ?>
					<?php if ( $footer_email ) : ?>
						<a href="mailto:<?php echo esc_attr( $footer_email ); ?>"><?php echo esc_html( $footer_email ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $footer_socials ) && is_array( $footer_socials ) ) : ?>
				<ul class="site-footer__socials">
					<?php foreach ( $footer_socials as $social ) : ?>
						<li>
							<a href="<?php echo esc_url( $social['url'] ?? '#' ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( $social['label'] ?? '' ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</footer>

<?php wp_footer(); ?>
</body>
</html>
