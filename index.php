<?php
/**
 * This is the main generic template file of the theme
 *
 * @package Baskerville 2
 */
get_header(); ?>

<div class="wrapper section medium-padding clear">

	<main class="content section-inner" id="content" role="main">
		<?php if ( have_posts() ) :

			if ( is_home() && ! is_front_page() ) : ?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>

			<?php
			endif; ?>
			<div class="posts" id="posts">

				<div class="spinner-container">
					<div id="spinner">
						<div class="double-bounce1"></div>
						<div class="double-bounce2"></div>
					</div>
				</div>

				<?php while ( have_posts() ) : the_post();
					get_template_part( 'content', get_post_format() );
				endwhile; ?>
			</div> <!-- /posts -->
			<?php the_posts_navigation(); ?>
			<?php else :
				get_template_part( 'content', 'none' );
			?>
		<?php endif; ?>

	</main> <!-- /content -->
</div> <!-- /wrapper -->

<?php get_footer(); ?>
