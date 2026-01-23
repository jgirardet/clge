<?php
/**
 * This template is used in the Loop to display posts in the Search Results
 *
 * @package Baskerville 2
 */
?>


<div class="post-container">

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php
		/**
		 * Post Title - only show on single.php view
		 */
		$before_title = '<header class="post-header"><h1 class="post-title entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
		$after_title = '</a></h1></header>';
		the_title( $before_title, $after_title );

		/**
		 * Post Thumbnail
		 */
		if ( baskerville_2_has_post_thumbnail() ) { ?>
			<div class="featured-media">
				<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>">
					<?php the_post_thumbnail( 'baskerville-2-post-thumbnail' ); ?>
				</a>
			</div> <!-- /featured-media -->
		<?php }

		/**
		 * Post Content
		 */
		?>

		<div class="post-content clear">
			<?php the_excerpt(); ?>
		</div>

		<?php baskerville_2_post_meta(); ?>

	</article> <!-- /post -->

</div>
