<?php
function wordbook_next_normalize_meta_text( $text ) {
	$text = strip_shortcodes( (string) $text );
	$text = wp_strip_all_tags( $text, true );
	$text = html_entity_decode( $text, ENT_QUOTES, get_bloginfo( 'charset' ) ?: 'UTF-8' );
	$text = preg_replace( '/\[\s*(?:…|\.{3})\s*\]/u', ' ', $text );
	$text = preg_replace( '/\s+/u', ' ', trim( $text ) );

	return is_string( $text ) ? $text : '';
}

function wordbook_next_trim_meta_description( $text, $width = 200 ) {
	$text = wordbook_next_normalize_meta_text( $text );

	if ( '' === $text ) {
		return '';
	}

	if ( function_exists( 'mb_strimwidth' ) ) {
		return trim( mb_strimwidth( $text, 0, $width, '', 'UTF-8' ) );
	}

	return trim( substr( $text, 0, $width ) );
}

function wordbook_next_get_post_meta_description( $post = null ) {
	$post = get_post( $post );

	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	$summary = wordbook_next_trim_meta_description( $post->post_excerpt );

	if ( '' === $summary ) {
		$summary = wordbook_next_trim_meta_description( $post->post_content );
	}

	if ( '' !== $summary ) {
		return $summary;
	}

	return wordbook_next_trim_meta_description( get_bloginfo( 'description' ) );
}

function wordbook_next_get_home_meta_description() {
	$front_page_id = 0;

	if ( is_front_page() && 'page' === get_option( 'show_on_front' ) ) {
		$front_page_id = (int) get_option( 'page_on_front' );
	}

	if ( is_home() && ! is_front_page() ) {
		$front_page_id = (int) get_option( 'page_for_posts' );
	}

	if ( $front_page_id ) {
		$description = wordbook_next_get_post_meta_description( $front_page_id );

		if ( '' !== $description ) {
			return $description;
		}
	}

	return wordbook_next_trim_meta_description( get_bloginfo( 'description' ) );
}

function wordbook_next_has_external_meta_description_provider() {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| class_exists( 'The_SEO_Framework\\Load', false )
		|| class_exists( 'SlimSEO\\Init', false );
}

function wordbook_next_get_meta_description() {
	if ( is_front_page() || is_home() ) {
		return wordbook_next_get_home_meta_description();
	}

	if ( is_singular() ) {
		return wordbook_next_get_post_meta_description( get_queried_object_id() );
	}

	if ( is_search() ) {
		global $wp_query;

		return wordbook_next_trim_meta_description(
			sprintf(
				'搜索“%s”的结果页，共 %d 条结果。',
				get_search_query(),
				isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0
			)
		);
	}

	$description = wordbook_next_trim_meta_description( get_the_archive_description() );

	if ( '' !== $description ) {
		return $description;
	}

	return wordbook_next_trim_meta_description( get_bloginfo( 'description' ) );
}

function wordbook_next_render_meta_description() {
	if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
		return;
	}

	$should_render = ! wordbook_next_has_external_meta_description_provider();
	$should_render = (bool) apply_filters( 'wordbook_next_should_render_meta_description', $should_render );

	if ( ! $should_render ) {
		return;
	}

	$description = apply_filters( 'wordbook_next_meta_description', wordbook_next_get_meta_description() );

	if ( '' === $description ) {
		return;
	}

	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'wordbook_next_render_meta_description', 1 );
