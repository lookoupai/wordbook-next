<?php
get_header();

if ( 'page' === get_option( 'show_on_front' ) && have_posts() ) {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/content', 'front-page' );
	}
} else {
	wordbook_next_render_listing_view();
}

get_footer();
