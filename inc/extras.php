<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Baskerville 2
 */
/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function baskerville_2_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Add class to body if the post/page has a featured image
	if ( isset( $post ) && has_post_thumbnail() ) {
		$classes[] = 'has-featured-image';
	} else {
		$classes[] = 'no-featured-image';
	}

	// Add class to body if it's a single page
	if ( is_page() || is_404() || is_attachment() ) {
		$classes[] = 'single single-post';
	}

	return $classes;
}
add_filter( 'body_class', 'baskerville_2_body_classes' );


function baskerville_2_comment_classes( $classes ) {
	global $comment_depth;

	if ( $comment_depth >= intval( get_option( 'thread_comments_depth' ) ) || ! comments_open() ) {
		$classes[] = 'no-reply';
	}

	return $classes;
}
add_filter( 'comment_class', 'baskerville_2_comment_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function baskerville_2_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'baskerville_2_pingback_header' );
