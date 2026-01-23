<?php
/**
 * This template is used on 404 error pages.
 *
 * @package Baskerville 2
 */

get_header(); ?>

<div class="wrapper section medium-padding">
	<main class="section-inner clear" role="main">
		<?php
			$content_class = is_active_sidebar( 'sidebar-1' ) ? "fleft" : "center";
		?>
		<section class="content clear <?php echo $content_class; // WPCS: XSS OK. ?>" id="content">
			<div class="post">

				<header class="post-header">
					<h1 class="post-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'baskerville-2' ); ?></h1>
				</header>

				<div class="post-content">

					<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'baskerville-2' ); ?></p>

					<?php get_search_form(); ?>

					<?php the_widget( 'WP_Widget_Recent_Posts' ); ?>

					<?php
						$archive_content = '<p>' . esc_html__( 'Try looking in the monthly archives.', 'baskerville-2' ) . '</p>';
						the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
					?>

					<div class="clear">
						<?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>
					</div>

				</div> <!-- /post-content -->

			</div> <!-- /post -->
		</section> <!-- /content -->

		<?php get_sidebar(); ?>

	</main> <!-- /section-inner -->
</div> <!-- /wrapper -->

<?php get_footer(); ?>
