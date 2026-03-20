<?php
$post_id = get_the_ID();
$label   = wordbook_next_get_result_label( $post_id );
$title   = wordbook_next_highlight_search_text( get_the_title( $post_id ) );
$summary = wordbook_next_highlight_search_text( wordbook_next_get_result_summary( $post_id, 38 ) );
$path    = wordbook_next_get_search_result_path( $post_id );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'wb-search-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="wb-search-card__thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php the_post_thumbnail( 'medium_large' ); ?>
		</a>
	<?php endif; ?>

	<div class="wb-search-card__body">
		<header class="wb-search-card__header">
			<div class="wb-search-card__meta">
				<span class="wb-search-card__label"><?php echo esc_html( $label ); ?></span>
				<time datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>">
					<?php
					printf(
						/* translators: %s: modified date */
						esc_html__( '更新于 %s', 'wordbook-next' ),
						esc_html( get_the_modified_time( get_option( 'date_format' ) ) )
					);
					?>
				</time>
			</div>

			<h2 class="wb-search-card__title">
				<a href="<?php the_permalink(); ?>"><?php echo wp_kses( $title, array( 'mark' => array() ) ); ?></a>
			</h2>

			<?php if ( $path ) : ?>
				<p class="wb-search-card__path"><?php echo esc_html( $path ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $summary ) : ?>
			<div class="wb-search-card__excerpt">
				<p><?php echo wp_kses( $summary, array( 'mark' => array() ) ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</article>
