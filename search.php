<?php
/**
 * The template for displaying Search Results.
 *
 * @package Baskerville 2
 */
get_header(); ?>

<main class="wrapper section medium-padding clear" role="main">
	<header class="page-header section-inner">
		<h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'baskerville-2' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
	</header>

	<div class="content section-inner" id="content">
		<?php if ( have_posts() ) : ?>

			<div class="spinner-container">
				<div id="spinner">
					<div class="double-bounce1"></div>
					<div class="double-bounce2"></div>
				</div>
			</div>

			<div class="posts" id="posts">

				<?php while ( have_posts() ) : the_post();
					get_template_part( 'content', 'search' );
				endwhile; ?>
				</div> <!-- /posts -->
				<?php the_posts_navigation(); ?>
			<?php
			else :
				get_template_part( 'content', 'none' );
			endif;
		?>

	</div> <!-- /content -->
</main> <!-- /wrapper -->

<?php get_footer(); ?>
