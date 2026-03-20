<?php
function wordbook_next_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'wordbook_next_theme',
		array(
			'title'       => __( 'Wordbook Next', 'wordbook-next' ),
			'description' => __( '配置文档主题的页脚与侧栏文案。', 'wordbook-next' ),
			'priority'    => 160,
		)
	);

	$wp_customize->add_setting(
		'footer_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'wordbook_next_sanitize_rich_text',
		)
	);

	$wp_customize->add_control(
		'footer_text',
		array(
			'label'       => __( '页脚文案', 'wordbook-next' ),
			'section'     => 'wordbook_next_theme',
			'type'        => 'textarea',
			'description' => __( '留空时会回退到旧主题的 footbanquan 配置。', 'wordbook-next' ),
		)
	);

	$wp_customize->add_setting(
		'sidebar_notice',
		array(
			'default'           => '',
			'sanitize_callback' => 'wordbook_next_sanitize_rich_text',
		)
	);

	$wp_customize->add_control(
		'sidebar_notice',
		array(
			'label'       => __( '侧栏提示文案', 'wordbook-next' ),
			'section'     => 'wordbook_next_theme',
			'type'        => 'textarea',
			'description' => __( '出现在左侧目录底部。', 'wordbook-next' ),
		)
	);
}
add_action( 'customize_register', 'wordbook_next_customize_register' );
