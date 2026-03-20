<?php
function wordbook_next_fallback_docs_menu( $args = array() ) {
	$menu_class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'wb-doc-nav__list';

	echo '<ul class="' . esc_attr( $menu_class ) . ' wb-doc-nav__list--fallback">';
	wp_list_pages(
		array(
			'title_li'    => '',
			'sort_column' => 'menu_order,post_title',
		)
	);
	echo '</ul>';
}

function wordbook_next_is_excluded_pager_item( $item ) {
	$classes = isset( $item->classes ) && is_array( $item->classes ) ? $item->classes : array();

	if ( in_array( 'skip-doc-pager', $classes, true ) ) {
		return true;
	}

	$title = function_exists( 'mb_strtolower' )
		? mb_strtolower( trim( wp_strip_all_tags( $item->title ) ), 'UTF-8' )
		: strtolower( trim( wp_strip_all_tags( $item->title ) ) );

	$blocked_titles = array(
		'登录',
		'注册',
		'login',
		'log in',
		'sign in',
		'sign up',
		'register',
	);

	if ( in_array( $title, $blocked_titles, true ) ) {
		return true;
	}

	$url  = isset( $item->url ) ? trim( $item->url ) : '';
	$path = (string) wp_parse_url( $url, PHP_URL_PATH );

	if ( '' !== $path ) {
		$blocked_paths = array(
			'/wp-login.php',
			'/wp-register.php',
			'/register',
			'/login',
			'/my-account',
			'/wp-admin',
		);

		foreach ( $blocked_paths as $blocked_path ) {
			if ( 0 === strpos( untrailingslashit( $path ), $blocked_path ) ) {
				return true;
			}
		}
	}

	$home_host = (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST );
	$item_host = (string) wp_parse_url( $url, PHP_URL_HOST );

	if ( $item_host && $home_host && strtolower( $item_host ) !== strtolower( $home_host ) ) {
		return true;
	}

	return false;
}

function wordbook_next_get_docs_menu_location() {
	$locations = get_nav_menu_locations();

	if ( ! empty( $locations['main'] ) ) {
		return 'main';
	}

	return 'main';
}

function wordbook_next_normalize_url( $url ) {
	$parts = wp_parse_url( $url );

	if ( false === $parts ) {
		return '';
	}

	$scheme = isset( $parts['scheme'] ) ? strtolower( $parts['scheme'] ) : 'https';
	$host   = isset( $parts['host'] ) ? strtolower( $parts['host'] ) : '';
	$path   = isset( $parts['path'] ) ? trailingslashit( $parts['path'] ) : '/';
	$query  = isset( $parts['query'] ) ? '?' . $parts['query'] : '';

	return $scheme . '://' . $host . $path . $query;
}

function wordbook_next_get_current_request_url() {
	global $wp;

	$request = isset( $wp->request ) && '' !== $wp->request ? '/' . ltrim( $wp->request, '/' ) . '/' : '/';

	return home_url( $request );
}

function wordbook_next_is_current_docs_item( $item ) {
	if ( is_singular() && 'post_type' === $item->type && (int) $item->object_id === get_queried_object_id() ) {
		return true;
	}

	if ( ( is_category() || is_tag() || is_tax() ) && 'taxonomy' === $item->type && (int) $item->object_id === get_queried_object_id() ) {
		return true;
	}

	$current_url = wordbook_next_normalize_url( wordbook_next_get_current_request_url() );
	$item_url    = wordbook_next_normalize_url( $item->url );

	return $current_url && $item_url && $current_url === $item_url;
}

function wordbook_next_get_document_context() {
	static $context = null;

	if ( null !== $context ) {
		return $context;
	}

	$context = array(
		'prev'    => null,
		'current' => null,
		'next'    => null,
	);

	$locations = get_nav_menu_locations();

	$menu_location = wordbook_next_get_docs_menu_location();

	if ( empty( $locations[ $menu_location ] ) ) {
		return $context;
	}

	$items = wp_get_nav_menu_items( $locations[ $menu_location ] );

	if ( empty( $items ) ) {
		return $context;
	}

	usort(
		$items,
		static function( $left, $right ) {
			return (int) $left->menu_order <=> (int) $right->menu_order;
		}
	);

	$documents = array_values(
		array_filter(
		$items,
		static function( $item ) {
			$url = isset( $item->url ) ? trim( $item->url ) : '';
			return '' !== $url && '#' !== $url && ! wordbook_next_is_excluded_pager_item( $item );
		}
	)
);

	$current_index = null;

	foreach ( $documents as $index => $item ) {
		if ( wordbook_next_is_current_docs_item( $item ) ) {
			$current_index = $index;
			break;
		}
	}

	if ( null === $current_index ) {
		return $context;
	}

	$context['current'] = $documents[ $current_index ];
	$context['prev']    = $current_index > 0 ? $documents[ $current_index - 1 ] : null;
	$context['next']    = isset( $documents[ $current_index + 1 ] ) ? $documents[ $current_index + 1 ] : null;

	return $context;
}

function wordbook_next_the_document_pager() {
	if ( is_front_page() || is_home() ) {
		return;
	}

	$context = wordbook_next_get_document_context();

	if ( empty( $context['prev'] ) && empty( $context['next'] ) ) {
		return;
	}

	echo '<nav class="wb-doc-pager" aria-label="' . esc_attr__( '文档翻页', 'wordbook-next' ) . '">';

	if ( ! empty( $context['prev'] ) ) {
		echo '<a class="wb-doc-pager__link wb-doc-pager__link--prev" href="' . esc_url( $context['prev']->url ) . '">';
		echo '<span class="wb-doc-pager__caption">' . esc_html__( '上一篇', 'wordbook-next' ) . '</span>';
		echo '<strong class="wb-doc-pager__title">' . esc_html( $context['prev']->title ) . '</strong>';
		echo '</a>';
	}

	if ( ! empty( $context['next'] ) ) {
		echo '<a class="wb-doc-pager__link wb-doc-pager__link--next" href="' . esc_url( $context['next']->url ) . '">';
		echo '<span class="wb-doc-pager__caption">' . esc_html__( '下一篇', 'wordbook-next' ) . '</span>';
		echo '<strong class="wb-doc-pager__title">' . esc_html( $context['next']->title ) . '</strong>';
		echo '</a>';
	}

	echo '</nav>';
}
