<?php
/**
 * This template is used in the Loop to display posts with the Image format
 *
 * @package Baskerville 2
 */
?>

<?php if ( ! is_single() ) { ?>
	<div class="post-container">
<?php } ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php if ( is_single() ) {
			/**
			 * Post Title - only show on single.php view
			 */
			$before_title = '<header class="post-header"><h1 class="post-title entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
			$after_title = '</a></h1></header>';
			the_title( $before_title, $after_title );
		}


		/**
		 * Post Thumbnail
		 */
		if ( baskerville_2_has_post_thumbnail() ) { ?>
			<div class="featured-media">
				<?php if ( ! is_single() ) { ?>
					<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>">
						<?php the_post_thumbnail( 'baskerville-2-post-thumbnail' ); ?>
					</a>
				<?php } else {
					the_post_thumbnail( 'baskerville-2-post-image' );
				} ?>
			</div> <!-- /featured-media -->
		<?php }


		/**
		 * Post Content
		 */
		$content = get_the_content();
		if ( ! empty( $content ) ) { ?>

			<div class="post-content clear">
				<?php
				the_content( sprintf(
					/* translators: %s: Name of current post. */
					wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'baskerville-2' ), array( 'span' => array( 'class' => array() ) ) ),
					the_title( '<span class="screen-reader-text">"', '"</span>', false )
				) );
				wp_link_pages();
				?>
			</div>

		<?php
		}


		/**
		 * Post Meta
		 */
		if ( is_single() ) { ?>

			<footer class="post-meta-container clear">
				<?php baskerville_2_author_bio(); ?>
				<div class="post-meta clear">
					<?php baskerville_2_single_post_meta(); ?>
					<?php the_post_navigation(); ?>
					<?php edit_post_link(
							sprintf(
								/* translators: %1$s: Pencil icon, %2$s: Name of current post */
								esc_html__( '%1$s Edit %2$s', 'baskerville-2' ),
								'<i class="fa fa-pencil-square-o"></i>',
								the_title( '<span class="screen-reader-text">"', '"</span>', false )
							),
							'<span class="edit-link">',
							'</span>'
					); ?>
				</div>
			</footer> <!-- /post-meta-container -->

			<?php comments_template( '', true );

		} else {
			baskerville_2_post_meta();
		} ?>

	</article> <!-- /post -->

<?php if ( ! is_single() ) { ?>
	</div>
<?php } ?>
