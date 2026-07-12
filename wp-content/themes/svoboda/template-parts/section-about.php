<?php
/**
 * Секція 3: Про автора.
 * Layout: ліворуч — великий заголовок + фото з overlay, праворуч — lead + divider + bio.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$name      = get_field( 'author_name' ) ?: 'Ігор Браславець';
$role      = get_field( 'author_role' ) ?: 'Ветеран. Радник власників бізнесу. Автор.';
$lead      = get_field( 'author_lead' ) ?: 'Керівник стратегічного маркетингу в міжнародній компанії і радник топ-менеджменту. Пройшов шлях від життя на нижчому соціальному рівні до кар\'єрного успіху і внутрішньої свободи.';
$bio       = get_field( 'author_bio' );
$photo     = get_field( 'author_photo' );
$instagram = get_field( 'author_instagram' ) ?: '#';
?>
<section class="section section--about" id="about">
	<div class="section--about__inner" data-fade-in data-fade-threshold="0.15">

		<!-- Ліворуч: заголовок зверху + фото з overlay знизу -->
		<h2 class="section--about__title">Про автора</h2>

		<?php if ( $photo ) : ?>
			<div class="section--about__photo">
				<img
					class="lozad"
					src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
					data-src="<?php echo esc_url( $photo['url'] ); ?>"
					alt="<?php echo esc_attr( $photo['alt'] ?? $name ); ?>"
					width="<?php echo esc_attr( $photo['width'] ?? '' ); ?>"
					height="<?php echo esc_attr( $photo['height'] ?? '' ); ?>"
				>
				<div class="section--about__photo-caption">
					<span class="section--about__photo-name"><?php echo esc_html( $name ); ?></span>
					<span class="section--about__photo-role"><?php echo esc_html( $role ); ?></span>
					<a class="section--about__instagram" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer">@braslavetsigor</a>
				</div>
			</div>
		<?php endif; ?>

		<!-- Праворуч: lead + червона лінія + основний bio -->
		<div class="section--about__content">
			<p class="section--about__lead"><?php echo esc_html( $lead ); ?></p>
			<hr class="section--about__divider">
			<?php if ( $bio ) : ?>
				<div class="section--about__bio"><?php echo wp_kses_post( $bio ); ?></div>
			<?php else : ?>
				<div class="section--about__bio">
					<p>Пройшов через пекло — і зрозумів: жодна проблема не вирішується на побутовому рівні процесів і дій. Вони вирішуються на рівні енергії, віри і думок якими ти наповнений. Застосував методологію не лише в особистому житті, а й у бізнесі. І вона працює. Якщо ти готовий перевірити це на собі — почни з цієї книги.</p>
				</div>
			<?php endif; ?>
		</div>

	</div>
</section>
