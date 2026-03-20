<?php
get_header();

wordbook_next_render_view_header();

if ( have_posts() ) :
	?>
	<div class="wb-listing">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content/content', 'list' );
		endwhile;
		?>
	</div>

	<?php
	the_posts_pagination(
		array(
			'mid_size'  => 1,
			'prev_text' => __( '上一页', 'wordbook-next' ),
			'next_text' => __( '下一页', 'wordbook-next' ),
		)
	);
else :
	get_template_part( 'template-parts/content/content', 'none' );
endif;

get_footer();
