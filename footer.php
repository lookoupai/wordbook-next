		</main>

		<footer class="wb-footer">
			<div class="wb-footer__inner">
				<div class="wb-footer__text"><?php echo wp_kses_post( wordbook_next_get_footer_text() ); ?></div>

				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => 'nav',
							'container_class'=> 'wb-footer__nav',
							'menu_class'     => 'wb-footer__menu',
							'depth'          => 1,
						)
					);
				}
				?>
			</div>
		</footer>
	</div>
</div>
<?php wp_footer(); ?>
</body>
</html>
