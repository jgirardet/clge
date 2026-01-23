<?php
/**
 * Custom template tags for Baskerville 2
 *
 * @package Baskerville 2
 */

if ( ! function_exists( 'baskerville_2_post_meta' ) ) :
/**
 * Display date posted, zilla likes and comments on posts
 *
 */

function baskerville_2_post_meta() { ?>

	<?php if ( is_sticky() && ! is_paged() && ! is_archive() ) : ?>
		<span class="sticky-post"><i class="fa fa-thumb-tack"></i><?php esc_attr_e( 'Featured post', 'baskerville-2' ); ?></span>
	<?php endif; ?>

	<div class="post-meta clear">
		<?php if ( is_sticky() && ! is_paged() && ! is_archive() ) : ?>
			<a class="post-date" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
				<i class="fa fa-thumb-tack"></i><?php esc_html_e( 'Featured', 'baskerville-2' ); ?>
			</a>
		<?php else : ?>
			<time class="updated" datetime="<?php the_time( 'Y-m-d' ); ?>">
				<a class="post-date" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
					<i class="fa fa-clock-o"></i><?php the_time( get_option( 'date_format' ) ); ?>
				</a>
			</time>
		<?php endif; ?>
		<?php
			if ( comments_open() ) :
				if ( ! post_password_required() ) :
					comments_popup_link( '0', '1', '%', 'post-comments' );
				endif;
			endif;

			edit_post_link( sprintf( esc_html__( '%s Edit Post ', 'baskerville-2' ), '<i class="fa fa-pencil-square-o"></i>' ) );
		?>
	</div>

<?php
}
endif;

if ( ! function_exists( 'baskerville_2_single_post_meta' ) ) :

/**
 * Display date posted, categories and tags on single.php posts
 *
 */
function baskerville_2_single_post_meta() { ?>

	<time class="post-date updated" datetime="<?php the_time( 'Y-m-d' ); ?>">
		<i class="fa fa-clock-o"></i>
		<?php the_time( get_option( 'date_format' ) ); ?>
	</time>

	<?php
		$categories_list = get_the_category_list( esc_html__( ', ', 'baskerville-2' ) );
		if ( $categories_list ) { ?>
			<p class="post-categories"><i class="fa fa-folder-open"></i><?php the_category( ', ' ); ?></p>
	<?php } ?>

	<?php
		/* translators: used between list items, there is a space after the comma */
		the_tags( '<p class="post-tags"><i class="fa fa-tag"></i>', esc_html__( ', ', 'baskerville-2' ), '</p>' );
	?>

<?php
}
endif;

if ( ! function_exists( 'baskerville_2_flexslider' ) ) :
/**
 * Turn photos into flexslider gallery for Gallery posts
 * see http://www.woothemes.com/flexslider/
 */
function baskerville_2_flexslider( $size = 'thumbnail' ) {

	if ( is_page() ) :
		$attachment_parent = $post->ID;
	else :
		$attachment_parent = get_the_ID();
	endif;

	$images = get_posts( array(
		'post_parent' => $attachment_parent,
		'post_type' => 'attachment',
		'numberposts' => 999, // show a large but unlikely number
		'post_status' => null,
		'post_mime_type' => 'image',
		'orderby' => 'menu_order',
		'order' => 'ASC',
	) );

	/*
	 * Grab the IDs of all the image attachments for this post
	 */
	if ( $images ) {
	?>
		<div class="flexslider">
			<ul class="slides">
				<?php foreach ( $images as $image ) {
					$attimg = wp_get_attachment_image( $image->ID, $size ); ?>
					<li>
						<?php echo wp_kses_post( $attimg ); ?>
						<?php if ( ! empty( $image->post_excerpt ) && is_single() ) : ?>
							<div class="media-caption-container">
								<p class="media-caption"><?php echo esc_html( $image->post_excerpt ); ?></p>
							</div>
						<?php endif; ?>
					</li>
				<?php } ?>
			</ul>
		</div><?php
	}
}
endif;
