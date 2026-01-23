<?php
/**
 * This template is used in the Loop to display content
 *
 * @package Baskerville 2
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	/**
	 * Page Title
	 */
	the_title( '<header class="post-header"><h1 class="post-title entry-title">', '</h1></header>' );

	/**
	 * Page Thumbnail
	 */
	if ( has_post_thumbnail() ) : ?>
		<div class="featured-media">
			<?php the_post_thumbnail( 'baskerville-2-post-image' ); ?>
		</div> <!-- /featured-media -->
	<?php endif;


	/**
	 * Page Content
	 */
	?>

	<div class="post-content clear">
		<?php
		the_content( sprintf(
			/* translators: %s: Name of current post. */
			wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'baskerville-2' ), array( 'span' => array( 'class' => array() ) ) ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		) );
		wp_link_pages();
		?>

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
	</div> <!-- /post-content -->

	<?php comments_template( '', true ); ?>

</article> <!-- /post -->
