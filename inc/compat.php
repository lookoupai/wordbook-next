<?php
function wordbook_next_sanitize_rich_text( $value ) {
	return wp_kses_post( $value );
}

function wordbook_next_get_footer_text() {
	$text = get_theme_mod( 'footer_text', '' );

	if ( is_string( $text ) && '' !== trim( wp_strip_all_tags( $text ) ) ) {
		return $text;
	}

	$legacy_text = get_option( 'footbanquan' );

	if ( is_string( $legacy_text ) && '' !== trim( wp_strip_all_tags( $legacy_text ) ) ) {
		return $legacy_text;
	}

	return sprintf(
		'© %s %s',
		wp_date( 'Y' ),
		get_bloginfo( 'name' )
	);
}

function wordbook_next_get_sidebar_notice() {
	$notice = get_theme_mod( 'sidebar_notice', '' );

	if ( is_string( $notice ) && '' !== trim( wp_strip_all_tags( $notice ) ) ) {
		return $notice;
	}

	return '';
}

function wordbook_next_get_legacy_logo_url() {
	$legacy_logo = get_option( 'header_logo_image' );

	if ( is_numeric( $legacy_logo ) ) {
		$attachment_url = wp_get_attachment_image_url( (int) $legacy_logo, 'full' );

		if ( $attachment_url ) {
			return $attachment_url;
		}
	}

	if ( is_string( $legacy_logo ) && filter_var( $legacy_logo, FILTER_VALIDATE_URL ) ) {
		return esc_url_raw( $legacy_logo );
	}

	if ( is_string( $legacy_logo ) && 0 === strpos( $legacy_logo, '/' ) ) {
		return home_url( $legacy_logo );
	}

	$hardcoded_path = ABSPATH . 'wp-content/uploads/2020/02/1581597009-logo.png';

	if ( ! empty( $legacy_logo ) && file_exists( $hardcoded_path ) ) {
		return home_url( '/wp-content/uploads/2020/02/1581597009-logo.png' );
	}

	return '';
}
