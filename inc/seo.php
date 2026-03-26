<?php
function wordbook_next_normalize_meta_text( $text ) {
	if ( function_exists( 'excerpt_remove_blocks' ) ) {
		$text = excerpt_remove_blocks( (string) $text );
	}

	$text = strip_shortcodes( (string) $text );
	$text = wp_strip_all_tags( $text, true );
	$text = html_entity_decode( $text, ENT_QUOTES, get_bloginfo( 'charset' ) ?: 'UTF-8' );
	$text = preg_replace( '#https?://[^\s]+#iu', ' ', $text );
	$text = preg_replace( '#www\.[^\s]+#iu', ' ', $text );
	$text = str_replace( '内容目录 显示', ' ', $text );
	$text = preg_replace( '/\[\s*(?:…|\.{3})\s*\]/u', ' ', $text );
	$text = preg_replace( '/\s+/u', ' ', trim( $text ) );

	return is_string( $text ) ? $text : '';
}

function wordbook_next_get_meta_context_post_id() {
	if ( is_singular() ) {
		return get_queried_object_id();
	}

	if ( is_front_page() && 'page' === get_option( 'show_on_front' ) ) {
		return (int) get_option( 'page_on_front' );
	}

	if ( is_home() && ! is_front_page() ) {
		return (int) get_option( 'page_for_posts' );
	}

	return 0;
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
	$front_page_id = wordbook_next_get_meta_context_post_id();

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

function wordbook_next_get_meta_title() {
	return wordbook_next_normalize_meta_text( wp_get_document_title() );
}

function wordbook_next_get_meta_url() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}

	if ( is_singular() ) {
		return get_permalink( get_queried_object_id() );
	}

	if ( is_home() && ! is_front_page() ) {
		$page_for_posts = (int) get_option( 'page_for_posts' );

		if ( $page_for_posts ) {
			return get_permalink( $page_for_posts );
		}
	}

	if ( is_search() ) {
		return get_search_link();
	}

	if ( ! is_404() ) {
		$page_number = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
		$url         = get_pagenum_link( $page_number );

		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}
	}

	return home_url( '/' );
}

function wordbook_next_get_meta_type() {
	if ( is_singular() ) {
		return 'article';
	}

	return 'website';
}

function wordbook_next_get_meta_image() {
	$image = array(
		'url' => '',
		'alt' => get_bloginfo( 'name' ),
	);
	$post_id = wordbook_next_get_meta_context_post_id();

	if ( $post_id ) {
		$thumbnail_id = get_post_thumbnail_id( $post_id );

		if ( $thumbnail_id ) {
			$image['url'] = wp_get_attachment_image_url( $thumbnail_id, 'full' );
			$image['alt'] = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $post_id );
		}
	}

	if ( '' === $image['url'] ) {
		$custom_logo_id = (int) get_theme_mod( 'custom_logo' );

		if ( $custom_logo_id ) {
			$image['url'] = wp_get_attachment_image_url( $custom_logo_id, 'full' );
			$image['alt'] = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true ) ?: get_bloginfo( 'name' );
		}
	}

	if ( '' === $image['url'] ) {
		$legacy_logo_url = wordbook_next_get_legacy_logo_url();

		if ( $legacy_logo_url ) {
			$image['url'] = $legacy_logo_url;
		}
	}

	if ( '' === $image['url'] ) {
		$site_icon_url = get_site_icon_url( 512 );

		if ( $site_icon_url ) {
			$image['url'] = $site_icon_url;
		}
	}

	$image['url'] = is_string( $image['url'] ) ? $image['url'] : '';
	$image['alt'] = wordbook_next_normalize_meta_text( $image['alt'] );

	return $image;
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

function wordbook_next_render_social_meta() {
	if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
		return;
	}

	$should_render = ! wordbook_next_has_external_meta_description_provider();
	$should_render = (bool) apply_filters( 'wordbook_next_should_render_social_meta', $should_render );

	if ( ! $should_render ) {
		return;
	}

	$title       = apply_filters( 'wordbook_next_meta_title', wordbook_next_get_meta_title() );
	$description = apply_filters( 'wordbook_next_meta_description', wordbook_next_get_meta_description() );
	$url         = apply_filters( 'wordbook_next_meta_url', wordbook_next_get_meta_url() );
	$type        = apply_filters( 'wordbook_next_meta_type', wordbook_next_get_meta_type() );
	$image       = apply_filters( 'wordbook_next_meta_image', wordbook_next_get_meta_image() );
	$site_name   = wordbook_next_normalize_meta_text( get_bloginfo( 'name' ) );
	$locale      = str_replace( '-', '_', get_locale() );

	if ( '' === $title || '' === $description || '' === $url ) {
		return;
	}

	echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
	echo '<meta name="twitter:card" content="' . esc_attr( ! empty( $image['url'] ) ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";

	if ( ! empty( $image['url'] ) ) {
		echo '<meta property="og:image" content="' . esc_url( $image['url'] ) . '">' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $image['url'] ) . '">' . "\n";

		if ( ! empty( $image['alt'] ) ) {
			echo '<meta property="og:image:alt" content="' . esc_attr( $image['alt'] ) . '">' . "\n";
		}
	}

	if ( is_singular() ) {
		$post_id = get_queried_object_id();

		echo '<meta property="article:published_time" content="' . esc_attr( get_post_time( DATE_W3C, false, $post_id ) ) . '">' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_post_modified_time( DATE_W3C, false, $post_id ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'wordbook_next_render_social_meta', 2 );
