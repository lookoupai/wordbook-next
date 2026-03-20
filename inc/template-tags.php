<?php
function wordbook_next_render_brand() {
	$home_url     = home_url( '/' );
	$description  = get_bloginfo( 'description' );
	$legacy_logo  = wordbook_next_get_legacy_logo_url();

	echo '<div class="wb-brand__identity">';

	if ( has_custom_logo() ) {
		echo '<div class="wb-brand__logo">';
		echo get_custom_logo();
		echo '</div>';
	} elseif ( $legacy_logo ) {
		echo '<a class="wb-brand__logo-link" href="' . esc_url( $home_url ) . '" rel="home">';
		echo '<img class="wb-brand__logo-image" src="' . esc_url( $legacy_logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
		echo '</a>';
	}

	echo '<div class="wb-brand__text">';
	echo '<a class="wb-brand__title" href="' . esc_url( $home_url ) . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';

	if ( $description ) {
		echo '<p class="wb-brand__description">' . esc_html( $description ) . '</p>';
	}

	echo '</div>';
	echo '</div>';
}

function wordbook_next_get_view_title() {
	if ( is_singular() ) {
		return get_the_title( get_queried_object_id() );
	}

	if ( is_home() && ! is_front_page() ) {
		$page_for_posts = (int) get_option( 'page_for_posts' );

		if ( $page_for_posts ) {
			return get_the_title( $page_for_posts );
		}

		return __( '最新内容', 'wordbook-next' );
	}

	if ( is_search() ) {
		return sprintf(
			/* translators: %s: search query */
			__( '搜索“%s”', 'wordbook-next' ),
			get_search_query()
		);
	}

	if ( is_category() ) {
		return single_cat_title( '', false );
	}

	if ( is_tag() || is_tax() ) {
		return single_term_title( '', false );
	}

	if ( is_404() ) {
		return __( '页面未找到', 'wordbook-next' );
	}

	$archive_title = get_the_archive_title();

	if ( $archive_title ) {
		return wp_strip_all_tags( $archive_title );
	}

	return get_bloginfo( 'name' );
}

function wordbook_next_get_view_description() {
	if ( is_search() ) {
		global $wp_query;

		return sprintf(
			/* translators: %d: result count */
			_n( '找到 %d 条结果。', '找到 %d 条结果。', (int) $wp_query->found_posts, 'wordbook-next' ),
			(int) $wp_query->found_posts
		);
	}

	$description = get_the_archive_description();

	if ( $description ) {
		return $description;
	}

	if ( is_front_page() || is_home() ) {
		return get_bloginfo( 'description' );
	}

	return '';
}

function wordbook_next_render_view_header() {
	$title       = wordbook_next_get_view_title();
	$description = wordbook_next_get_view_description();

	echo '<section class="wb-view-header">';
	echo '<p class="wb-view-header__eyebrow">' . esc_html__( '文档视图', 'wordbook-next' ) . '</p>';
	echo '<h1 class="wb-view-header__title">' . esc_html( $title ) . '</h1>';

	if ( $description ) {
		echo '<div class="wb-view-header__description">' . wp_kses_post( wpautop( $description ) ) . '</div>';
	}

	echo '</section>';
}
