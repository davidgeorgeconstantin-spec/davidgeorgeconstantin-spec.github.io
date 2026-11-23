<?php
/**
 * Main template: blog index and archive fallback.
 *
 * @package Crumb
 */

get_header();
?>
<main id="primary" class="site-main">
	<div class="container">
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1 class="page-title"><?php single_post_title(); ?></h1>
		<?php elseif ( is_archive() ) : ?>
			<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
		<?php elseif ( is_search() ) : ?>
			<h1 class="page-title">
				<?php
				/* translators: %s: search query. */
				printf( esc_html__( 'Results for: %s', 'crumb' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
				?>
			</h1>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="posts-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="post-thumbnail" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'crumb-card', array( 'loading' => 'lazy' ) ); ?>
							</a>
						<?php endif; ?>
						<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="entry-meta"><?php crumb_posted_on(); ?></p>
						<?php the_excerpt(); ?>
					</article>
				<?php endwhile; ?>
			</div>

			<div class="pagination">
				<?php
				the_posts_pagination(
					array(
						'prev_text' => esc_html__( 'Previous', 'crumb' ),
						'next_text' => esc_html__( 'Next', 'crumb' ),
					)
				);
				?>
			</div>
		<?php else : ?>
			<section class="entry">
				<h1 class="entry-title"><?php esc_html_e( 'Nothing found', 'crumb' ); ?></h1>
				<p><?php esc_html_e( 'Try a search instead.', 'crumb' ); ?></p>
				<?php get_search_form(); ?>
			</section>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
