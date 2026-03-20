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

	if ( ! empty( $locations['docs'] ) ) {
		return 'docs';
	}

	if ( ! empty( $locations['main'] ) ) {
		return 'main';
	}

	return 'docs';
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

function wordbook_next_get_docs_menu_items( $filtered = false ) {
	static $cache = array();

	$cache_key = $filtered ? 'filtered' : 'all';

	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	$locations     = get_nav_menu_locations();
	$menu_location = wordbook_next_get_docs_menu_location();

	if ( empty( $locations[ $menu_location ] ) ) {
		$cache[ $cache_key ] = array();
		return $cache[ $cache_key ];
	}

	$items = wp_get_nav_menu_items( $locations[ $menu_location ] );

	if ( empty( $items ) ) {
		$cache[ $cache_key ] = array();
		return $cache[ $cache_key ];
	}

	usort(
		$items,
		static function( $left, $right ) {
			return (int) $left->menu_order <=> (int) $right->menu_order;
		}
	);

	if ( $filtered ) {
		$items = array_values(
			array_filter(
				$items,
				static function( $item ) {
					$url = isset( $item->url ) ? trim( $item->url ) : '';
					return '' !== $url && '#' !== $url && ! wordbook_next_is_excluded_pager_item( $item );
				}
			)
		);
	}

	$cache[ $cache_key ] = array_values( $items );

	return $cache[ $cache_key ];
}

function wordbook_next_get_docs_menu_item_children( $parent_id, $items = null ) {
	$items = is_array( $items ) ? $items : wordbook_next_get_docs_menu_items( true );

	return array_values(
		array_filter(
			$items,
			static function( $item ) use ( $parent_id ) {
				return (int) $item->menu_item_parent === (int) $parent_id;
			}
		)
	);
}

function wordbook_next_get_docs_menu_item_summary( $item ) {
	if ( 'post_type' === $item->type && ! empty( $item->object_id ) ) {
		$post = get_post( (int) $item->object_id );

		if ( $post instanceof WP_Post ) {
			if ( has_excerpt( $post ) ) {
				return wp_strip_all_tags( $post->post_excerpt );
			}

			return wp_trim_words( wp_strip_all_tags( $post->post_content ), 26 );
		}
	}

	if ( 'taxonomy' === $item->type && ! empty( $item->object_id ) ) {
		$term = get_term( (int) $item->object_id, $item->object );

		if ( $term instanceof WP_Term && ! is_wp_error( $term ) ) {
			return wp_trim_words( wp_strip_all_tags( term_description( $term ) ), 20 );
		}
	}

	return '';
}

function wordbook_next_get_docs_sections( $limit = 6, $child_limit = 4 ) {
	$items     = wordbook_next_get_docs_menu_items( true );
	$top_level = wordbook_next_get_docs_menu_item_children( 0, $items );
	$sections  = array();

	foreach ( $top_level as $item ) {
		$sections[] = array(
			'item'     => $item,
			'title'    => $item->title,
			'url'      => $item->url,
			'summary'  => wordbook_next_get_docs_menu_item_summary( $item ),
			'children' => array_slice( wordbook_next_get_docs_menu_item_children( $item->ID, $items ), 0, $child_limit ),
		);

		if ( count( $sections ) >= $limit ) {
			break;
		}
	}

	return $sections;
}

function wordbook_next_get_quick_links( $limit = 6 ) {
	$sections    = wordbook_next_get_docs_sections( 12, 6 );
	$quick_links = array();

	foreach ( $sections as $section ) {
		if ( empty( $section['children'] ) ) {
			$quick_links[] = array(
				'title'   => $section['title'],
				'url'     => $section['url'],
				'eyebrow' => __( '专题入口', 'wordbook-next' ),
				'summary' => $section['summary'],
			);
		} else {
			foreach ( $section['children'] as $child ) {
				$quick_links[] = array(
					'title'   => $child->title,
					'url'     => $child->url,
					'eyebrow' => $section['title'],
					'summary' => wordbook_next_get_docs_menu_item_summary( $child ),
				);

				if ( count( $quick_links ) >= $limit ) {
					break 2;
				}
			}
		}

		if ( count( $quick_links ) >= $limit ) {
			break;
		}
	}

	return array_slice( $quick_links, 0, $limit );
}

function wordbook_next_get_first_docs_item() {
	$items = wordbook_next_get_docs_menu_items( true );

	if ( empty( $items ) ) {
		return null;
	}

	$front_page_id  = (int) get_option( 'page_on_front' );
	$current_home   = wordbook_next_normalize_url( home_url( '/' ) );
	$current_front  = $front_page_id ? wordbook_next_normalize_url( get_permalink( $front_page_id ) ) : '';

	foreach ( $items as $item ) {
		$item_url = wordbook_next_normalize_url( $item->url );

		if ( $item_url && $item_url === $current_home ) {
			continue;
		}

		if ( $item_url && $current_front && $item_url === $current_front ) {
			continue;
		}

		if ( 'post_type' === $item->type && (int) $item->object_id === $front_page_id ) {
			continue;
		}

		return $item;
	}

	return $items[0];
}

function wordbook_next_get_recent_docs_posts( $limit = 6 ) {
	$items    = wordbook_next_get_docs_menu_items( true );
	$post_ids = array();

	foreach ( $items as $item ) {
		if ( 'post_type' !== $item->type ) {
			continue;
		}

		if ( ! in_array( $item->object, array( 'page', 'post' ), true ) ) {
			continue;
		}

		$post_ids[] = (int) $item->object_id;
	}

	$post_ids = array_values( array_unique( array_filter( $post_ids ) ) );

	if ( empty( $post_ids ) ) {
		return array();
	}

	$front_page_id = (int) get_option( 'page_on_front' );

	$query = new WP_Query(
		array(
			'post_type'           => array( 'page', 'post' ),
			'post__in'            => $post_ids,
			'post__not_in'        => array_filter( array( $front_page_id ) ),
			'posts_per_page'      => $limit,
			'post_status'         => 'publish',
			'orderby'             => 'modified',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
		)
	);

	return $query->posts;
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

	$documents = wordbook_next_get_docs_menu_items( true );

	if ( empty( $documents ) ) {
		return $context;
	}

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
