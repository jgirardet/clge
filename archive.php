<?php
/**
 * This template is used for generic archive pages.
 *
 * @package Baskerville 2
 */

get_header(); ?>

<main class="wrapper section medium-padding clear" role="main">
	<header class="page-header section-inner">
		<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
		<?php the_archive_description( '<div class="tag-archive-meta">', '</div>' ); ?>
	</header> <!-- /page-title -->

	<div class="content section-inner" id="content">

		<?php if ( have_posts() ) : ?>

			<div class="posts" id="posts">

				<div class="spinner-container">
					<div id="spinner">
						<div class="double-bounce1"></div>
						<div class="double-bounce2"></div>
					</div>
				</div>

				<?php while ( have_posts() ) : the_post();
					get_template_part( 'content', get_post_format() );
				endwhile;
				?>
			</div> <!-- /posts -->
			<?php the_posts_navigation(); ?>
		<?php else :
			get_template_part( 'content', 'none' );
			?>
		<?php endif; ?>

	</div> <!-- /content -->
</main> <!-- /wrapper -->

<?php get_footer(); ?>
