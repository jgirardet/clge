<?php
/**
 * This template is for displaying all the blog's contributors
 *
 * @package Baskerville 2
 * Template Name: Contributors
 */

$all_contributors = get_users( 'order=ASC&who=authors&orderby=nicename' );
$contributors = array();

foreach ( $all_contributors as $current_contributor ) {
	if ( ( ! 0 == count_user_posts( $current_contributor->ID ) )  // If the user has published one post or more...
	&& ( ! 'yes' == $current_contributor->hideauthor ) ) { // ...and the user hasn't been checked as hidden on the user profile...
		$contributors[] = $current_contributor; // ...add the user to the array of users that is going to be displayed
	}
}

get_header(); ?>

<div class="wrapper section medium-padding">
	<div class="section-inner clear">

		<?php
			$content_class = is_active_sidebar( 'sidebar-1' ) ? "fleft" : "center";
		?>
		<div class="content clear <?php echo $content_class; // WPCS: XSS OK. ?>" id="content">

			<div <?php post_class( 'single post' ); ?>>
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<div class="post-header">
						<h2 class="post-title"><?php the_title(); ?></h2>
					</div> <!-- /post-header -->

					<?php if ( $post->post_content != '' ) : ?>
						<div class="post-content">
							<?php the_content(); ?>
						</div> <!-- /post-content -->
					<?php endif; ?>

					<?php
					if ( ! empty( $contributors ) ) { ?>

						<div class="contributors-container">

							<?php
							$i = 0;
							foreach ( $contributors as $contributor ) {
								if ( 0 == $i % 2 ) {
									echo $i > 0 ? '</div>' : ''; // close div if it's not the first
									echo "<div class='authors-row row clear'>";
								} ?>

									<div class="one-half author-info">
										<a href="<?php echo esc_url( get_author_posts_url( $contributor->ID ) ); ?>" class="author-avatar"><?php echo get_avatar( get_the_author_meta( 'user_email', $contributor->ID ), '256' ); ?></a>
										<h4><a href="<?php echo esc_url( get_author_posts_url( $contributor->ID ) ); ?>"><?php the_author_meta( 'display_name', $contributor->ID ); ?></a></h4>
										<h5>
											<a href="<?php echo esc_url( get_author_posts_url( $contributor->ID ) ); ?>">
												<?php printf( esc_html( _n( '%s post', '%s posts', count_user_posts( $contributor->ID ), 'baskerville-2' ) ), intval( count_user_posts( $contributor->ID ) ) ); ?>
											</a>
										</h5>

										<p class="author-description"><?php the_author_meta( 'description', $contributor->ID ); ?></p>

										<div class="author-links">
											<a class="author-link-posts" title="<?php esc_attr_e( 'Author archive', 'baskerville-2' ); ?>" href="<?php echo esc_url( get_author_posts_url( $contributor->ID ) ); ?>">
												<i class="fa fa-archive"></i> <?php esc_html_e( 'Author archive', 'baskerville-2' ); ?>
											</a>
											<?php if ( ! empty( $contributor->user_url ) ) : ?>
												<a class="author-link-website" href="<?php the_author_meta( 'user_url', $contributor->ID ); ?>">
													<i class="fa fa-home"></i> <?php esc_html_e( 'Website', 'baskerville-2' ); ?>
												</a>
											<?php endif; ?>
										</div> <!-- /author-links -->
									</div> <!-- /author-info -->

									<?php $i++; ?>

								<?php } ?>
							</div> <!-- /authors-row -->
						</div> <!-- /contributors-container -->

					<?php } ?>

				<?php comments_template( '', true ); ?>

				<?php
					endwhile;
					endif;
				?>
			</div> <!-- /post -->
		</div> <!-- /content -->

		<?php get_sidebar(); ?>

	</div> <!-- /section-inner -->
</div> <!-- /wrapper -->

<?php get_footer(); ?>
