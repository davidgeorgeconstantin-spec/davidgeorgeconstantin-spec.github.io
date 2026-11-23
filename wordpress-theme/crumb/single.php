<?php
/**
 * Template for a single blog post.
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
				<header>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<p class="entry-meta"><?php crumb_posted_on(); ?></p>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="post-thumbnail"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
				<footer class="entry-meta">
					<?php
					$crumb_categories = get_the_category_list( esc_html__( ', ', 'crumb' ) );
					if ( $crumb_categories ) {
						/* translators: %s: list of categories. */
						printf( '<p>' . esc_html__( 'Filed under: %s', 'crumb' ) . '</p>', wp_kses_post( $crumb_categories ) );
					}
					?>
				</footer>
			</article>

			<?php
			the_post_navigation(
				array(
					'prev_text' => '&larr; %title',
					'next_text' => '%title &rarr;',
				)
			);

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
		endwhile;
		?>
	</div>
</main>
<?php
get_footer();
