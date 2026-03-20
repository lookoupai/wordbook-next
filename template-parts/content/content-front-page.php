<?php
$front_context = wordbook_next_get_front_page_context();
$hero_summary  = $front_context['summary'];
$hero_content  = trim( (string) $front_context['content'] );
$start_item    = wordbook_next_get_first_docs_item();
$quick_links   = wordbook_next_get_quick_links( 6 );
$recent_posts  = wordbook_next_get_recent_docs_posts( 6 );
?>

<article class="wb-home">
	<section class="wb-home-hero">
		<div class="wb-home-hero__content">
			<p class="wb-home-hero__eyebrow"><?php esc_html_e( '文档入口', 'wordbook-next' ); ?></p>
			<h1 class="wb-home-hero__title"><?php echo esc_html( $front_context['title'] ); ?></h1>

			<?php if ( $hero_summary ) : ?>
				<div class="wb-home-hero__summary">
					<p><?php echo esc_html( $hero_summary ); ?></p>
				</div>
			<?php endif; ?>

			<div class="wb-home-hero__meta">
				<time datetime="<?php echo esc_attr( wp_date( DATE_W3C, (int) $front_context['modified_date'] ) ); ?>">
					<?php
					printf(
						/* translators: %s: modified time */
						esc_html__( '最近整理于 %s', 'wordbook-next' ),
						esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $front_context['modified_date'] ) )
					);
					?>
				</time>
			</div>

			<div class="wb-home-hero__actions">
				<?php if ( $start_item ) : ?>
					<a class="wb-button wb-home-hero__button" href="<?php echo esc_url( $start_item->url ); ?>"><?php esc_html_e( '开始阅读', 'wordbook-next' ); ?></a>
				<?php endif; ?>

				<?php if ( ! empty( $recent_posts ) ) : ?>
					<a class="wb-button wb-home-hero__button wb-home-hero__button--ghost" href="#wb-home-updates"><?php esc_html_e( '最近更新', 'wordbook-next' ); ?></a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $front_context['thumbnail'] ) : ?>
			<div class="wb-home-hero__media">
				<?php echo $front_context['thumbnail']; ?>
			</div>
		<?php endif; ?>
	</section>

	<?php if ( $hero_content ) : ?>
		<section class="wb-home-section wb-home-section--intro">
			<div class="wb-home-section__header">
				<p class="wb-home-section__eyebrow"><?php esc_html_e( '站点说明', 'wordbook-next' ); ?></p>
				<h2 class="wb-home-section__title"><?php esc_html_e( '先了解这里有什么', 'wordbook-next' ); ?></h2>
			</div>

			<div class="wb-home-richtext entry-content">
				<?php echo apply_filters( 'the_content', $hero_content ); ?>
			</div>
		</section>
	<?php endif; ?>

		<?php if ( ! empty( $quick_links ) ) : ?>
			<section class="wb-home-section wb-home-section--quickstart" id="wb-home-quickstart">
				<div class="wb-home-section__header">
					<p class="wb-home-section__eyebrow"><?php esc_html_e( '快速入口', 'wordbook-next' ); ?></p>
					<h2 class="wb-home-section__title"><?php esc_html_e( '最常访问的内容', 'wordbook-next' ); ?></h2>
				</div>

				<div class="wb-home-grid">
					<?php foreach ( $quick_links as $quick_link ) : ?>
						<article class="wb-home-card">
							<p class="wb-home-card__eyebrow"><?php echo esc_html( $quick_link['eyebrow'] ); ?></p>
							<h3 class="wb-home-card__title"><a href="<?php echo esc_url( $quick_link['url'] ); ?>"><?php echo esc_html( $quick_link['title'] ); ?></a></h3>

							<?php if ( $quick_link['summary'] ) : ?>
								<p class="wb-home-card__summary"><?php echo esc_html( $quick_link['summary'] ); ?></p>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

	<?php if ( ! empty( $recent_posts ) ) : ?>
		<section class="wb-home-section wb-home-section--updates" id="wb-home-updates">
			<div class="wb-home-section__header">
				<p class="wb-home-section__eyebrow"><?php esc_html_e( '最近更新', 'wordbook-next' ); ?></p>
				<h2 class="wb-home-section__title"><?php esc_html_e( '刚刚整理过的内容', 'wordbook-next' ); ?></h2>
			</div>

			<div class="wb-listing wb-listing--front">
				<?php
				foreach ( $recent_posts as $post ) :
					setup_postdata( $post );
					get_template_part( 'template-parts/content/content', 'list' );
				endforeach;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

	</article>
