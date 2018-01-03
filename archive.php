<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Zeit Online Blogs Twentyeighteen
 */

get_header(); ?>
  <div class="site-content__navigation__category">
    <?php
    print zb_cats_nav();
    ?>
  </div>
	<div id="primary" class="content-area">
		<div class="site-main<?php if( '1' == $GLOBALS['wp_query']->found_posts ) echo ' one-post-only'; ?>">

		<?php if ( have_posts() ) : ?>
			<?php if (get_the_archive_description()): ?>
			<header class="page-header">
				<?php
					//the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header><!-- .page-header -->
			<?php endif; ?>
			<?php /* Start the Loop */
			$counter = 1;
			?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content', get_post_format() );
					if ( $counter == 1) {
						zb_render_ad( 'mobile', '3', 'ad-wrapper ad-wrapper-fullwidth ad-wrapper-mobile', 'blog', 'ad-medium-rectangle' );
					}
					if ( $counter == 2) {
  						zb_render_ad( 'desktop', '8', 'ad-wrapper ad-wrapper-fullwidth', 'blog', 'Anzeige', 'ad-medium-rectangle' );
					}
					if ( $counter == 5) {
						zb_render_ad( 'desktop', '4', 'ad-wrapper ad-wrapper-breaking', 'blog', 'Anzeige' );
					}
					if ( $counter == 8) {
						zb_render_ad( 'mobile', '4', 'ad-wrapper ad-wrapper-fullwidth ad-wrapper-mobile', 'blog', '', 'ad-medium-rectangle' );
					}
					$counter++;
				?>

			<?php endwhile; ?>
      <br style="clear: both;" />
			<?php zb_paging_nav(); ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</div><!-- .div-main -->
		<br style="clear: both;" />
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
