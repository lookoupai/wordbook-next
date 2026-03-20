<?php
get_header();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/content', 'singular' );

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	}
} else {
	get_template_part( 'template-parts/content/content', 'none' );
}

get_footer();
