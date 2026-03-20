<?php
if ( post_password_required() ) {
	return;
}

$has_comment_list = have_comments();
$can_comment      = comments_open() && is_user_logged_in();

if ( ! $has_comment_list && ! $can_comment ) {
	return;
}
?>

<section id="comments" class="wb-comments">
	<?php if ( $has_comment_list ) : ?>
		<h2 class="wb-comments__title">
			<?php
			printf(
				/* translators: %d: comment count */
				esc_html( _n( '%d 条评论', '%d 条评论', get_comments_number(), 'wordbook-next' ) ),
				number_format_i18n( get_comments_number() )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="wb-comments__closed"><?php esc_html_e( '评论已关闭。', 'wordbook-next' ); ?></p>
	<?php endif; ?>

	<?php if ( $can_comment ) : ?>
		<?php
		comment_form(
			array(
				'title_reply'          => __( '参与讨论', 'wordbook-next' ),
				'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
				'title_reply_after'    => '</h3>',
				'comment_notes_before' => '',
				'comment_notes_after'  => '',
				'logged_in_as'         => '',
				'label_submit'         => __( '提交评论', 'wordbook-next' ),
				'class_submit'         => 'wb-button',
			)
		);
		?>
	<?php endif; ?>
</section>
