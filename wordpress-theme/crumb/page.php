<?php
/**
 * Template for static pages (About, Menu, Contact...).
 *
 * @package Crumb
 */

get_header();
?>
<main id="primary" class="site-main">
	<div class="container">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="post-thumbnail"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'crumb' ) . '">',
							'after'  => '</nav>',
						)
					);
					?>
				</div>
			</article>
			<?php
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
