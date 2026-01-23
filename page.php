<?php
/**
 * This template is used for displaying pages.
 *
 * @package Baskerville 2
 */

get_header(); ?>

<div class="wrapper section medium-padding">
	<main class="section-inner clear" role="main">

		<?php
			$content_class = is_active_sidebar( 'sidebar-1' ) ? "fleft" : "center";
		?>
		<div class="content clear <?php echo $content_class; // WPCS: XSS OK. ?>" id="content">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

			<?php endwhile; else : ?>

				<?php get_template_part( 'content', 'none' ); ?>

			<?php endif; ?>

		</div> <!-- /content -->

		<?php get_sidebar(); ?>

	</main> <!-- /section-inner -->
</div> <!-- /wrapper -->

<?php get_footer(); ?>
