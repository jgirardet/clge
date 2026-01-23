<?php
/**
 * Adds file support for Jetpack-specific theme functions.
 * See: http://jetpack.me/
 *
 * @package Baskerville 2
 */

function baskerville_2_jetpack_setup() {
	// Add support for Responsive Videos.
	add_theme_support( 'jetpack-responsive-videos' );

	// Add support for Infinite Scroll.
	add_theme_support( 'infinite-scroll', array(
		'container'      => 'posts',
		'footer_widgets' => array( 'sidebar-2', 'sidebar-3', 'sidebar-4', ),
		'footer'         => 'content',
		'wrapper'        => false,
		'render'         => 'baskerville_2_infinite_scroll_render',
	) );

	// Add theme support for Social Menu.
	add_theme_support( 'jetpack-social-menu', 'svg' );

	// Add theme support for Content Options.
	add_theme_support( 'jetpack-content-options', array(
		'author-bio'      => true,
		'blog-display'    => 'content',
		'masonry'         => '#posts',
		'post-details'    => array(
			'stylesheet'  => 'baskerville-2-style',
			'date'        => '.post-date, .single .hentry .post-meta .post-date',
			'categories'  => '.post-categories, .single .hentry .post-meta .post-categories',
			'tags'        => '.post-tags, .single .hentry .post-meta .post-tags',
		),
		'featured-images' => array(
			'archive'     => true,
			'post'        => true,
			'page'        => true,
			'fallback'    => true,
		),
	) );
}
add_action( 'after_setup_theme', 'baskerville_2_jetpack_setup' );

/**
 * Return early if Author Bio is not available.
 */
function baskerville_2_author_bio() {
	if ( ! function_exists( 'jetpack_author_bio' ) ) {
		get_template_part( 'content', 'author' );
	} else {
		jetpack_author_bio();
	}
}

/**
 * Custom render function for Infinite Scroll.
 */
function baskerville_2_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		if ( is_search() ) :
			get_template_part( 'content', 'search' );
		else :
			get_template_part( 'content', get_post_format() );
		endif;
	}
}

/**
 * Author Bio Avatar Size.
 */
function baskerville_2_author_bio_avatar_size() {
	return 90;
}
add_filter( 'jetpack_author_bio_avatar_size', 'baskerville_2_author_bio_avatar_size' );

/**
 * Return early if Social Menu is not available.
 */
function baskerville_2_social_menu() {
	if ( ! function_exists( 'jetpack_social_menu' ) ) {
		return;
	} else {
		jetpack_social_menu();
	}
}

/**
 * Custom function to check for a post thumbnail;
 * If Jetpack is not available, fall back to has_post_thumbnail()
 */
function baskerville_2_has_post_thumbnail( $post = null ) {
	if ( function_exists( 'jetpack_has_featured_image' ) ) {
		return jetpack_has_featured_image( $post );
	} else {
		return has_post_thumbnail( $post );
	}
}
