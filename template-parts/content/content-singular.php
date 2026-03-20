<article id="post-<?php the_ID(); ?>" <?php post_class( 'wb-entry wb-entry--singular' ); ?>>
	<header class="wb-entry__header">
		<?php the_title( '<h1 class="wb-entry__title">', '</h1>' ); ?>

		<div class="wb-entry__meta">
			<time datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>">
				<?php
				printf(
					/* translators: %s: modified time */
					esc_html__( '最后更新于 %s', 'wordbook-next' ),
					esc_html( get_the_modified_time( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) )
				);
				?>
			</time>
		</div>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="wb-entry__thumbnail">
			<?php the_post_thumbnail( 'large' ); ?>
		</div>
	<?php endif; ?>

	<div class="wb-entry__content entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<nav class="page-links">' . esc_html__( '分页：', 'wordbook-next' ),
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<footer class="wb-entry__footer">
		<?php
		if ( 'post' === get_post_type() ) {
			$tags = get_the_tags();

			if ( ! empty( $tags ) ) {
				echo '<div class="wb-entry__tags">';

				foreach ( $tags as $tag ) {
					echo '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a>';
				}

				echo '</div>';
			}
		}

		edit_post_link(
			__( '编辑当前内容', 'wordbook-next' ),
			'<div class="wb-entry__edit">',
			'</div>'
		);
		?>
	</footer>
</article>

<?php wordbook_next_the_document_pager(); ?>
