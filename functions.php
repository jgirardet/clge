<?php
/**
 * Sets up the theme and provides some helper functions; also adds theme's custom widgets.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package Baskerville 2
 */

if ( ! function_exists( 'baskerville_2_setup' ) ) :
/**
 * Theme setup
 */
function baskerville_2_setup() {
	/**
	 * Adds RSS feed links to <head> for posts and comments.
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/**
	 * Adds support for post thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	/*
	 * Switches default core markup for search form to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/**
	 * Set theme image sizes
	 */
	add_image_size( 'baskerville-2-post-image', 1400, 9999 );
	add_image_size( 'baskerville-2-post-thumbnail', 600, 9999 );

	/**
	 * Add support for post formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );

	/**
	 * Add support for custom backgrounds
	 */
	add_theme_support( 'custom-background', apply_filters( 'baskerville_2_custom_background_args', array(
		'default-color' => 'f1f1f1',
		'default-image' => '',
	) ) );

	/**
	 * Add support for styles in WYSIWYG editor
	 */
	add_editor_style( array( 'editor-style.css', baskerville_2_fonts_url() ) );

	/**
	 * Add navigation menu
	 */
	register_nav_menu( 'menu-1', 'Header' );

	/**
	 * Make the theme translation-ready
	 */
	load_theme_textdomain( 'baskerville-2', get_template_directory() . '/languages' );

	/**
	 * Add theme support for selective refresh for widgets.
	 */
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add theme support for custom logos
	add_theme_support( 'custom-logo',
		array(
			'width'       => 1200,
			'height'      => 300,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'baskerville_2_setup' );
endif;

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function baskerville_2_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'baskerville_2_content_width', 736 );
}
add_action( 'after_setup_theme', 'baskerville_2_content_width', 0 );

if ( ! function_exists( 'baskerville_2_fonts_url' ) ) :
/**
 * Define Google Fonts
 */
function baskerville_2_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	* supported by Roboto, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$roboto = esc_html_x( 'on', 'Roboto font: on or off', 'baskerville-2' );

	/* Translators: If there are characters in your language that are not
	* supported by Roboto Slab, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$robotoslab = esc_html_x( 'on', 'Roboto Slab font: on or off', 'baskerville-2' );

	/* Translators: If there are characters in your language that are not
	* supported by Pacifico, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$pacifico = esc_html_x( 'on', 'Pacifico font: on or off', 'baskerville-2' );

	if ( 'off' !== $roboto || 'off' !== $robotoslab || 'off' !== $pacifico ) {
		$font_families = array();

		if ( 'off' !== $robotoslab ) {
			$font_families[] = 'Roboto Slab:400,700';
		}

		if ( 'off' !== $roboto ) {
			$font_families[] = 'Roboto:400,400italic,700,700italic,300';
		}

		if ( 'off' !== $pacifico ) {
			$font_families[] = 'Pacifico:400';
		}

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return $fonts_url;
}
endif;


if ( ! function_exists( 'baskerville_2_scripts' ) ) :
/**
 * Enqueue scripts and styles.
 */
function baskerville_2_scripts() {
	wp_enqueue_style( 'baskerville-2-style', get_stylesheet_uri() );
	wp_enqueue_style( 'baskerville-2-fonts', baskerville_2_fonts_url(), array(), null );
	wp_enqueue_script( 'baskerville-2-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );
	wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/fontawesome/font-awesome.css', array(), '4.3.0' );
	wp_enqueue_script( 'baskerville-2-flexslider', get_template_directory_uri() . '/js/flexslider.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'baskerville-2-global', get_template_directory_uri() . '/js/global.js', array( 'jquery', 'masonry' ), '', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'baskerville_2_scripts' );
endif;


if ( ! function_exists( 'baskerville_2_sidebar_reg' ) ) :
/**
 * Add Widget Areas to footer and sidebar
 */
function baskerville_2_sidebar_reg() {
	register_sidebar( array(
		'name' => esc_html__( 'Sidebar', 'baskerville-2' ),
		'id' => 'sidebar-1',
		'description' => esc_html__( 'Widgets in this area will be shown in the sidebar.', 'baskerville-2' ),
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content clear">',
		'after_widget' => '</div></div>',
	));
	register_sidebar( array(
		'name' => esc_html__( 'Footer 1', 'baskerville-2' ),
		'id' => 'sidebar-2',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content clear">',
		'after_widget' => '</div></div>',
	));
	register_sidebar( array(
		'name' => esc_html__( 'Footer 2', 'baskerville-2' ),
		'id' => 'sidebar-3',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content clear">',
		'after_widget' => '</div></div>',
	));
	register_sidebar( array(
		'name' => esc_html__( 'Footer 3', 'baskerville-2' ),
		'id' => 'sidebar-4',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content clear">',
		'after_widget' => '</div></div>',
	));
}
add_action( 'widgets_init', 'baskerville_2_sidebar_reg' );
endif;


if ( ! function_exists( 'baskerville_2_posts_link_attributes_1' ) ) :
/**
 * Add classes to next_posts_link and previous_posts_link
 */
function baskerville_2_posts_link_attributes_1() {
	return 'class="post-nav-older fleft"';
}
add_filter( 'next_posts_link_attributes', 'baskerville_2_posts_link_attributes_1' );
endif;

if ( ! function_exists( 'baskerville_2_posts_link_attributes_2' ) ) :
function baskerville_2_posts_link_attributes_2() {
	return 'class="post-nav-newer fright"';
}
add_filter( 'previous_posts_link_attributes', 'baskerville_2_posts_link_attributes_2' );
endif;

if ( ! function_exists( 'baskerville_2_clearfix_class' ) ) :
/**
 * Add class to posts for clearfix
 */
function baskerville_2_clearfix_class( $classes ) {
	$classes[] = 'clear';
	return $classes;
}
add_filter( 'post_class', 'baskerville_2_clearfix_class', 10, 3 );
endif;


if ( ! function_exists( 'baskerville_2_new_excerpt_more' ) ) :
/**
 * Add more link text to excerpt
 */
function baskerville_2_new_excerpt_more( $more ) {
	return '... <a class="more-link" href="'. esc_url( get_permalink( get_the_ID() ) ) . '#more-' . esc_attr ( get_the_ID() ) . '">' . esc_html__( 'Continue Reading &rarr;', 'baskerville-2' ) . '</a>';
}
add_filter( 'excerpt_more', 'baskerville_2_new_excerpt_more' );
endif;


if ( ! function_exists( 'baskerville_2_url_to_domain' ) ) :
/**
 * Get domain name from URL
 */
function baskerville_2_url_to_domain( $url ) {
	$host = parse_url( $url, PHP_URL_HOST );

	if ( ! $host ) {
		$host = $url;
	}

	if ( 'www.' == substr( $host, 0, 4 ) ) {
		$host = substr( $host, 0 );
	}

	return $host;
}
endif;

/**
 * Return the post URL.
 *
 * @uses get_url_in_content() to get the URL in the post meta (if it exists) or
 * the first link found in the post content.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 * Borrowed from Twenty Thirteen.
 */
function baskerville_2_get_link_url() {
	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}

function baskerville_2_block_editor_styles() {
	// Block Styles.
	wp_enqueue_style( 'baskerville-2-block-editor-style', get_theme_file_uri( '/editor-blocks.css' ) );
}
add_action( 'enqueue_block_editor_assets', 'baskerville_2_block_editor_styles' );

/**
 * Includes & required files:
 */

// Custom header functions for this theme
require get_template_directory() . '/inc/custom-header.php';

// Jetpack functions for this theme
require get_template_directory() . '/inc/jetpack.php';

// Custom functions that act independently of the theme templates
require get_template_directory() . '/inc/extras.php';

// Custom template tags for this theme
require get_template_directory() . '/inc/template-tags.php';


// updater for WordPress.com themes
if ( is_admin() )
	include dirname( __FILE__ ) . '/inc/updater.php';
