<?php
/**
 * This template is used on 404 error pages.
 *
 * @package Clge
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
					<h1 class="post-title" style="color: #f29816;"><?php esc_html_e( 'Page, non trouvée. Essayez les questions ouvertes', 'clge' ); ?></h1>
				</header>

				

			</div> <!-- /post -->
		</section> <!-- /content -->

		<?php get_sidebar(); ?>

	</main> <!-- /section-inner -->
</div> <!-- /wrapper -->

