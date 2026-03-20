<?php
function wordbook_next_enqueue_assets() {
	$theme_version = wp_get_theme()->get( 'Version' );
	$style_path    = get_stylesheet_directory() . '/style.css';
	$main_css_path = get_template_directory() . '/assets/css/main.css';
	$script_path   = get_template_directory() . '/assets/js/theme.js';

	wp_enqueue_style(
		'wordbook-next-style',
		get_stylesheet_uri(),
		array(),
		file_exists( $style_path ) ? (string) filemtime( $style_path ) : $theme_version
	);

	wp_enqueue_style(
		'wordbook-next-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'wordbook-next-style' ),
		file_exists( $main_css_path ) ? (string) filemtime( $main_css_path ) : $theme_version
	);

	wp_enqueue_script(
		'wordbook-next-theme',
		get_template_directory_uri() . '/assets/js/theme.js',
		array(),
		file_exists( $script_path ) ? (string) filemtime( $script_path ) : $theme_version,
		true
	);
	wp_script_add_data( 'wordbook-next-theme', 'defer', true );
	wp_localize_script(
		'wordbook-next-theme',
		'wordbookNextTheme',
		array(
			'storageKey' => 'wordbook-next-reading',
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wordbook_next_enqueue_assets' );
