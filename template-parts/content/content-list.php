<?php
$label = wordbook_next_get_result_label( get_the_ID() );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'wb-list-entry' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="wb-list-entry__thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php the_post_thumbnail( 'medium_large' ); ?>
		</a>
	<?php endif; ?>

	<div class="wb-list-entry__body">
		<header class="wb-list-entry__header">
			<p class="wb-list-entry__eyebrow"><?php echo esc_html( $label ); ?></p>
			<h2 class="wb-list-entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<div class="wb-list-entry__meta">
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
		</header>

		<div class="wb-list-entry__excerpt">
			<?php the_excerpt(); ?>
		</div>
	</div>
</article>
