<?php
/**
 * This template has no sidebar
 *
 * @package Baskerville 2
 * Template Name: No Sidebar
 */

get_header(); ?>

<div class="wrapper section medium-padding">
	<div class="section-inner">
		<div class="content center clear" id="content">

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
