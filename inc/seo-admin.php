<?php
function wordbook_next_get_seo_meta_box_post_types() {
	return apply_filters(
		'wordbook_next_seo_meta_box_post_types',
		array( 'post', 'page' )
	);
}

function wordbook_next_register_seo_meta_box() {
	if ( ! is_admin() ) {
		return;
	}

	foreach ( wordbook_next_get_seo_meta_box_post_types() as $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			continue;
		}

		add_meta_box(
			'wordbook-next-seo-description',
			__( 'SEO 描述', 'wordbook-next' ),
			'wordbook_next_render_seo_meta_box',
			$post_type,
			'side',
			'default'
		);
	}
}
add_action( 'add_meta_boxes', 'wordbook_next_register_seo_meta_box' );

function wordbook_next_render_seo_meta_box( $post ) {
	$meta_key            = wordbook_next_get_seo_description_meta_key();
	$current_description = get_post_meta( $post->ID, $meta_key, true );
	$generated_preview   = wordbook_next_get_generated_post_meta_description( $post );

	wp_nonce_field( 'wordbook_next_save_seo_description', 'wordbook_next_seo_description_nonce' );

	echo '<p>' . esc_html__( '留空时自动从摘要或正文首段提取，建议 60–120 字，避免与标题重复。', 'wordbook-next' ) . '</p>';
	echo '<label class="screen-reader-text" for="wordbook-next-seo-description-field">' . esc_html__( 'SEO 描述', 'wordbook-next' ) . '</label>';
	echo '<textarea id="wordbook-next-seo-description-field" name="wordbook_next_seo_description" rows="6" style="width:100%;">' . esc_textarea( $current_description ) . '</textarea>';

	if ( '' !== $generated_preview ) {
		echo '<p><strong>' . esc_html__( '自动摘要预览：', 'wordbook-next' ) . '</strong></p>';
		echo '<p style="margin-bottom:0;">' . esc_html( $generated_preview ) . '</p>';
	}
}

function wordbook_next_save_seo_meta_box( $post_id ) {
	if ( ! isset( $_POST['wordbook_next_seo_description_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wordbook_next_seo_description_nonce'] ) ), 'wordbook_next_save_seo_description' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post || ! in_array( $post->post_type, wordbook_next_get_seo_meta_box_post_types(), true ) ) {
		return;
	}

	$value = '';

	if ( isset( $_POST['wordbook_next_seo_description'] ) ) {
		$value = sanitize_textarea_field( wp_unslash( $_POST['wordbook_next_seo_description'] ) );
	}

	$meta_key = wordbook_next_get_seo_description_meta_key();

	if ( '' === trim( $value ) ) {
		delete_post_meta( $post_id, $meta_key );
		return;
	}

	update_post_meta( $post_id, $meta_key, $value );
}
add_action( 'save_post', 'wordbook_next_save_seo_meta_box' );
