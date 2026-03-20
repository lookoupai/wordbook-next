<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text skip-link" href="#primary"><?php esc_html_e( '跳转到正文', 'wordbook-next' ); ?></a>
<div class="wb-site-shell">
	<div class="wb-site-shell__overlay" data-wb-overlay hidden></div>
	<aside class="wb-sidebar" id="wb-sidebar" aria-label="<?php esc_attr_e( '文档导航', 'wordbook-next' ); ?>" data-wb-sidebar tabindex="-1">
		<div class="wb-sidebar__inner">
			<div class="wb-brand">
				<?php wordbook_next_render_brand(); ?>
			</div>

			<form class="wb-search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
				<label class="screen-reader-text" for="wb-search-field"><?php esc_html_e( '搜索', 'wordbook-next' ); ?></label>
				<input
					id="wb-search-field"
					type="search"
					name="s"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					placeholder="<?php esc_attr_e( '搜索内容', 'wordbook-next' ); ?>"
				>
			</form>

				<nav class="wb-doc-nav" aria-label="<?php esc_attr_e( '章节目录', 'wordbook-next' ); ?>" data-wb-doc-tree>
					<?php
					wp_nav_menu(
					array(
						'theme_location' => wordbook_next_get_docs_menu_location(),
						'container'      => false,
						'menu_class'     => 'wb-doc-nav__list',
						'depth'          => 3,
						'fallback_cb'    => 'wordbook_next_fallback_docs_menu',
					)
					);
					?>
				</nav>

				<div class="wb-sidebar__footer">
					<?php if ( wordbook_next_has_utility_menu() ) : ?>
						<nav class="wb-utility-nav" aria-label="<?php esc_attr_e( '功能导航', 'wordbook-next' ); ?>">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'utility',
									'container'      => false,
									'menu_class'     => 'wb-utility-nav__list',
									'depth'          => 1,
								)
							);
							?>
						</nav>
					<?php endif; ?>

					<?php wordbook_next_render_reading_controls( 'wb-reading-controls--sidebar' ); ?>

					<?php if ( wordbook_next_get_sidebar_notice() ) : ?>
						<div class="wb-sidebar__note"><?php echo wp_kses_post( wordbook_next_get_sidebar_notice() ); ?></div>
					<?php endif; ?>
				</div>
			</div>
		</aside>

		<div class="wb-main" data-wb-main>
			<header class="wb-topbar">
				<button
					class="wb-topbar__toggle"
				type="button"
				data-wb-action="toggle-nav"
				aria-controls="wb-sidebar"
				aria-expanded="false"
			>
				<?php esc_html_e( '目录', 'wordbook-next' ); ?>
			</button>

			<div class="wb-topbar__title"><?php echo esc_html( wordbook_next_get_view_title() ); ?></div>

				<?php wordbook_next_render_reading_controls( 'wb-reading-controls--topbar' ); ?>
				</header>

			<main id="primary" class="wb-content" role="main">
