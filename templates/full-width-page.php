<?php
/**
 * This template has a content area that takes up the full width of the page
 *
 * @package Baskerville 2
 * Template Name: Full Width, No Sidebar
 */

get_header();

/**
 *Increase the content width to match the wider template
 */
global $content_width;
$content_width = 1120;
?>

<div class="wrapper section medium-padding">
	<div class="section-inner">
		<div class="content full-width clear" id="content">

			<?php
			if ( have_posts() ) : while ( have_posts() ) : the_post();
				get_template_part( 'content', 'page' );
			endwhile;
			else :
				get_template_part( 'content', 'none' );
			endif;
			?>

		</div> <!-- /content -->
	</div> <!-- /section-inner -->
</div> <!-- /wrapper -->

<?php get_footer(); ?>
