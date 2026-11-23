<?php
/**
 * Site header.
 *
 * @package Crumb
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#primary"><?php esc_html_e( 'Skip to content', 'crumb' ); ?></a>

<header class="site-header">
	<div class="container">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
			<?php endif; ?>
			<?php $crumb_description = get_bloginfo( 'description', 'display' ); ?>
			<?php if ( $crumb_description ) : ?>
				<p class="site-description"><?php echo esc_html( $crumb_description ); ?></p>
			<?php endif; ?>
		</div>

		<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary', 'crumb' ); ?>">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Menu', 'crumb' ); ?></button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => 'wp_page_menu',
					'depth'          => 2,
				)
			);
			?>
		</nav>
	</div>
</header>
