<?php
if ( ! function_exists( 'wordbook_next_setup' ) ) {
	function wordbook_next_setup() {
		load_theme_textdomain( 'wordbook-next', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 1600, 9999 );

		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		add_theme_support(
			'custom-logo',
			array(
				'height'               => 80,
				'width'                => 280,
				'flex-height'          => true,
				'flex-width'           => true,
				'unlink-homepage-logo' => true,
			)
		);

		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor.css' );

		register_nav_menus(
			array(
				'docs'    => __( '文档导航', 'wordbook-next' ),
				'main'    => __( '旧版目录导航', 'wordbook-next' ),
				'utility' => __( '功能导航', 'wordbook-next' ),
				'footer'  => __( '页脚导航', 'wordbook-next' ),
			)
		);
	}
}
add_action( 'after_setup_theme', 'wordbook_next_setup' );

function wordbook_next_content_width() {
	$GLOBALS['content_width'] = 840;
}
add_action( 'after_setup_theme', 'wordbook_next_content_width', 0 );
