<?php
/**
 * Секція 5: Відеоблок — фото автора на природі, кнопка відтворення, підпис.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$poster  = get_field( 'video_poster' );
$embed   = get_field( 'video_embed_url' );
$caption = get_field( 'video_caption' );
?>
<section class="section section--video" id="video">
	<div class="section--video__inner" data-fade-in>
		<div class="section--video__player" data-video-player data-embed-url="<?php echo esc_url( $embed ); ?>">
			<?php if ( $poster ) : ?>
				<img class="section--video__poster lozad" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="<?php echo esc_url( $poster['url'] ); ?>" alt="<?php echo esc_attr( $poster['alt'] ?? '' ); ?>" width="<?php echo esc_attr( $poster['width'] ?? '' ); ?>" height="<?php echo esc_attr( $poster['height'] ?? '' ); ?>">
			<?php endif; ?>

			<button type="button" class="section--video__play-btn" aria-label="Відтворити відео" data-video-play-btn>
				&#9658;
			</button>
		</div>

		<?php if ( $caption ) : ?>
			<p class="section--video__caption"><?php echo esc_html( $caption ); ?></p>
		<?php endif; ?>
	</div>
</section>
