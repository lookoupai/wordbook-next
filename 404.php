<?php
get_header();
?>

<section class="wb-empty-state">
	<p class="wb-empty-state__eyebrow"><?php esc_html_e( '404', 'wordbook-next' ); ?></p>
	<h1 class="wb-empty-state__title"><?php esc_html_e( '页面没有找到', 'wordbook-next' ); ?></h1>
	<p class="wb-empty-state__description"><?php esc_html_e( '可以尝试返回首页，或者直接搜索你要找的内容。', 'wordbook-next' ); ?></p>

	<div class="wb-empty-state__actions">
		<a class="wb-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '返回首页', 'wordbook-next' ); ?></a>
	</div>

	<form class="wb-inline-search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
		<label class="screen-reader-text" for="wb-inline-search-field"><?php esc_html_e( '搜索', 'wordbook-next' ); ?></label>
		<input id="wb-inline-search-field" type="search" name="s" placeholder="<?php esc_attr_e( '搜索内容', 'wordbook-next' ); ?>">
		<button type="submit"><?php esc_html_e( '搜索', 'wordbook-next' ); ?></button>
	</form>
</section>

<?php
get_footer();
