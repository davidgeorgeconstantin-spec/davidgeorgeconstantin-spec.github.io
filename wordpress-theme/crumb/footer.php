<?php
/**
 * Site footer.
 *
 * @package Crumb
 */
?>
<footer class="site-footer">
	<div class="container">
		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<div class="footer-widgets">
				<?php dynamic_sidebar( 'footer-1' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav aria-label="<?php esc_attr_e( 'Footer', 'crumb' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php endif; ?>

		<p>
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> &middot;
			<?php esc_html_e( 'Crumb theme by David Constantin', 'crumb' ); ?>
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
