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

function wordbook_next_render_reading_controls( $extra_class = '' ) {
	$classes = trim( 'wb-reading-controls ' . $extra_class );

	echo '<div class="' . esc_attr( $classes ) . '" aria-label="' . esc_attr__( '阅读设置', 'wordbook-next' ) . '">';
	echo '<button type="button" data-wb-action="decrease-font">' . esc_html__( 'A-', 'wordbook-next' ) . '</button>';
	echo '<button type="button" data-wb-action="increase-font">' . esc_html__( 'A+', 'wordbook-next' ) . '</button>';
	echo '<button type="button" data-wb-action="toggle-font">' . esc_html__( '字形', 'wordbook-next' ) . '</button>';
	echo '<button type="button" data-wb-action="toggle-theme">' . esc_html__( '主题', 'wordbook-next' ) . '</button>';
	echo '</div>';
}

function wordbook_next_get_front_page_context() {
	$context = array(
		'title'         => get_bloginfo( 'name' ),
		'summary'       => get_bloginfo( 'description' ),
		'content'       => '',
		'modified_date' => current_time( 'timestamp' ),
		'thumbnail'     => '',
		'post_id'       => 0,
	);

	if ( 'page' === get_option( 'show_on_front' ) ) {
		$front_page_id = (int) get_option( 'page_on_front' );

		if ( $front_page_id ) {
			$post = get_post( $front_page_id );

			if ( $post instanceof WP_Post ) {
				$context['title']         = get_the_title( $post );
				$context['summary']       = has_excerpt( $post ) ? $post->post_excerpt : wp_trim_words( wp_strip_all_tags( $post->post_content ), 42 );
				$context['content']       = $post->post_content;
				$context['modified_date'] = get_post_modified_time( 'U', false, $post );
				$context['thumbnail']     = get_the_post_thumbnail( $post, 'large' );
				$context['post_id']       = $post->ID;
			}
		}
	}

	return $context;
}

function wordbook_next_get_result_label( $post_id = null ) {
	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post ) {
		return __( '内容', 'wordbook-next' );
	}

	$type_object = get_post_type_object( $post->post_type );
	$label       = $type_object ? $type_object->labels->singular_name : __( '内容', 'wordbook-next' );

	if ( 'post' === $post->post_type ) {
		$categories = get_the_category( $post->ID );

		if ( ! empty( $categories ) ) {
			$label = $categories[0]->name;
		}
	}

	return $label;
}

function wordbook_next_get_result_summary( $post_id = null, $length = 34 ) {
	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	$text = has_excerpt( $post ) ? $post->post_excerpt : wp_strip_all_tags( $post->post_content );
	$text = preg_replace( '/\s+/u', ' ', trim( $text ) );

	return wp_trim_words( $text, $length );
}

function wordbook_next_highlight_search_text( $text, $query = '' ) {
	$text  = wp_strip_all_tags( (string) $text );
	$query = '' !== $query ? $query : get_search_query( false );

	if ( '' === $text || '' === trim( $query ) ) {
		return esc_html( $text );
	}

	$highlighted = esc_html( $text );
	$terms       = preg_split( '/\s+/u', trim( wp_strip_all_tags( $query ) ) );
	$terms       = array_values( array_unique( array_filter( $terms ) ) );

	foreach ( $terms as $term ) {
		$needle = esc_html( $term );

		if ( '' === $needle ) {
			continue;
		}

		$highlighted = preg_replace( '/' . preg_quote( $needle, '/' ) . '/iu', '<mark>$0</mark>', $highlighted );
	}

	return $highlighted;
}

function wordbook_next_get_search_result_path( $post_id = null ) {
	$permalink = get_permalink( $post_id );
	$path      = wp_parse_url( $permalink, PHP_URL_PATH );

	return $path ? untrailingslashit( $path ) : '';
}

function wordbook_next_render_singular_view() {
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content/content', 'singular' );

			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		}

		return;
	}

	get_template_part( 'template-parts/content/content', 'none' );
}

function wordbook_next_render_listing_view() {
	wordbook_next_render_view_header();

	if ( have_posts() ) {
		echo '<div class="wb-listing">';

		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content/content', 'list' );
		}

		echo '</div>';

		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => __( '上一页', 'wordbook-next' ),
				'next_text' => __( '下一页', 'wordbook-next' ),
			)
		);

		return;
	}

	get_template_part( 'template-parts/content/content', 'none' );
}

function wordbook_next_render_search_view() {
	global $wp_query;

	$query        = get_search_query();
	$result_count = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;
	$quick_links  = wordbook_next_get_quick_links( 4 );
	$recent_posts = wordbook_next_get_recent_docs_posts( 4 );

	echo '<section class="wb-search-panel">';
	echo '<div class="wb-search-panel__header">';
	echo '<p class="wb-search-panel__eyebrow">' . esc_html__( '文档检索', 'wordbook-next' ) . '</p>';
	echo '<h1 class="wb-search-panel__title">' . sprintf( esc_html__( '搜索“%s”', 'wordbook-next' ), esc_html( $query ) ) . '</h1>';
	echo '<p class="wb-search-panel__meta">' . sprintf( esc_html__( '共找到 %d 条结果。', 'wordbook-next' ), $result_count ) . '</p>';
	echo '</div>';
	echo '<form class="wb-search-panel__form" method="get" action="' . esc_url( home_url( '/' ) ) . '" role="search">';
	echo '<label class="screen-reader-text" for="wb-search-panel-field">' . esc_html__( '搜索', 'wordbook-next' ) . '</label>';
	echo '<input id="wb-search-panel-field" type="search" name="s" value="' . esc_attr( $query ) . '" placeholder="' . esc_attr__( '继续搜索你要找的内容', 'wordbook-next' ) . '">';
	echo '<button type="submit">' . esc_html__( '检索', 'wordbook-next' ) . '</button>';
	echo '</form>';
	echo '</section>';

	if ( have_posts() ) {
		echo '<div class="wb-search-results">';

		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content/content', 'search' );
		}

		echo '</div>';

		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => __( '上一页', 'wordbook-next' ),
				'next_text' => __( '下一页', 'wordbook-next' ),
			)
		);

		return;
	}

	echo '<section class="wb-search-empty">';
	echo '<div class="wb-search-empty__intro">';
	echo '<p class="wb-search-empty__eyebrow">' . esc_html__( '没有找到结果', 'wordbook-next' ) . '</p>';
	echo '<h2 class="wb-search-empty__title">' . esc_html__( '换个关键词试试', 'wordbook-next' ) . '</h2>';
	echo '<p class="wb-search-empty__description">' . esc_html__( '可以尝试缩短关键词、使用更明确的名称，或者直接从下面的推荐入口继续浏览。', 'wordbook-next' ) . '</p>';
	echo '</div>';

	if ( ! empty( $quick_links ) ) {
		echo '<div class="wb-search-empty__section">';
		echo '<h3 class="wb-search-empty__section-title">' . esc_html__( '推荐入口', 'wordbook-next' ) . '</h3>';
		echo '<div class="wb-search-empty__links">';

		foreach ( $quick_links as $quick_link ) {
			echo '<a class="wb-search-empty__link" href="' . esc_url( $quick_link['url'] ) . '">';
			echo '<span class="wb-search-empty__link-eyebrow">' . esc_html( $quick_link['eyebrow'] ) . '</span>';
			echo '<strong class="wb-search-empty__link-title">' . esc_html( $quick_link['title'] ) . '</strong>';
			echo '</a>';
		}

		echo '</div>';
		echo '</div>';
	}

	if ( ! empty( $recent_posts ) ) {
		echo '<div class="wb-search-empty__section">';
		echo '<h3 class="wb-search-empty__section-title">' . esc_html__( '最近更新', 'wordbook-next' ) . '</h3>';
		echo '<div class="wb-search-empty__recent">';

		foreach ( $recent_posts as $post ) {
			echo '<a class="wb-search-empty__recent-link" href="' . esc_url( get_permalink( $post ) ) . '">';
			echo '<span class="wb-search-empty__recent-title">' . esc_html( get_the_title( $post ) ) . '</span>';
			echo '<span class="wb-search-empty__recent-date">' . esc_html( get_the_modified_date( get_option( 'date_format' ), $post ) ) . '</span>';
			echo '</a>';
		}

		echo '</div>';
		echo '</div>';
	}

	echo '</section>';
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

	if ( is_archive() && function_exists( 'wordbook_next_get_archive_meta_description' ) ) {
		return wordbook_next_get_archive_meta_description();
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

function wordbook_next_has_utility_menu() {
	return has_nav_menu( 'utility' );
}
