<?php
/**
 * Sets up the theme and provides some helper functions; also adds theme's custom widgets.
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package Clge
 */

if ( ! function_exists( 'clge_setup' ) ) :
/**
 * Theme setup
 */
function clge_setup() {
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
	add_image_size( 'clge-post-image', 1400, 9999 );
	add_image_size( 'clge-post-thumbnail', 600, 9999 );

	/**
	 * Add support for post formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );

	/**
	 * Add support for custom backgrounds
	 */
	add_theme_support( 'custom-background', apply_filters( 'clge_custom_background_args', array(
		'default-color' => 'f1f1f1',
		'default-image' => '',
	) ) );

	/**
	 * Add support for styles in WYSIWYG editor
	 */
	add_editor_style( array( 'editor-style.css', clge_fonts_url() ) );

	/**
	 * Add navigation menu
	 */
	register_nav_menu( 'menu-1', 'Header' );

	/**
	 * Make the theme translation-ready
	 */
	load_theme_textdomain( 'clge', get_template_directory() . '/languages' );

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
add_action( 'after_setup_theme', 'clge_setup' );
endif;

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function clge_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'clge_content_width', 736 );
}
add_action( 'after_setup_theme', 'clge_content_width', 0 );

if ( ! function_exists( 'clge_fonts_url' ) ) :
/**
 * Define Google Fonts
 */
function clge_fonts_url() {
	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	* supported by Roboto, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$roboto = esc_html_x( 'on', 'Roboto font: on or off', 'clge' );

	/* Translators: If there are characters in your language that are not
	* supported by Roboto Slab, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$robotoslab = esc_html_x( 'on', 'Roboto Slab font: on or off', 'clge' );

	/* Translators: If there are characters in your language that are not
	* supported by Pacifico, translate this to 'off'. Do not translate
	* into your own language.
	*/
	$pacifico = esc_html_x( 'on', 'Pacifico font: on or off', 'clge' );

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


if ( ! function_exists( 'clge_scripts' ) ) :
/**
 * Enqueue scripts and styles.
 */
function clge_scripts() {
	wp_enqueue_style( 'clge-style', get_stylesheet_uri() );
	wp_enqueue_style( 'clge-fonts', clge_fonts_url(), array(), null );
	wp_enqueue_script( 'clge-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );
	wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/fontawesome/font-awesome.css', array(), '4.3.0' );
	wp_enqueue_script( 'clge-flexslider', get_template_directory_uri() . '/js/flexslider.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'clge-global', get_template_directory_uri() . '/js/global.js', array( 'jquery', 'masonry' ), '', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'clge_scripts' );
endif;


if ( ! function_exists( 'clge_sidebar_reg' ) ) :
/**
 * Add Widget Areas to footer and sidebar
 */
function clge_sidebar_reg() {
	register_sidebar( array(
		'name' => esc_html__( 'Sidebar', 'clge' ),
		'id' => 'sidebar-1',
		'description' => esc_html__( 'Widgets in this area will be shown in the sidebar.', 'clge' ),
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content clear">',
		'after_widget' => '</div></div>',
	));
	register_sidebar( array(
		'name' => esc_html__( 'Footer 1', 'clge' ),
		'id' => 'sidebar-2',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content clear">',
		'after_widget' => '</div></div>',
	));
	register_sidebar( array(
		'name' => esc_html__( 'Footer 2', 'clge' ),
		'id' => 'sidebar-3',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content clear">',
		'after_widget' => '</div></div>',
	));
	register_sidebar( array(
		'name' => esc_html__( 'Footer 3', 'clge' ),
		'id' => 'sidebar-4',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'before_widget' => '<div id="%1$s" class="widget %2$s"><div class="widget-content clear">',
		'after_widget' => '</div></div>',
	));
}
add_action( 'widgets_init', 'clge_sidebar_reg' );
endif;


if ( ! function_exists( 'clge_posts_link_attributes_1' ) ) :
/**
 * Add classes to next_posts_link and previous_posts_link
 */
function clge_posts_link_attributes_1() {
	return 'class="post-nav-older fleft"';
}
add_filter( 'next_posts_link_attributes', 'clge_posts_link_attributes_1' );
endif;

if ( ! function_exists( 'clge_posts_link_attributes_2' ) ) :
function clge_posts_link_attributes_2() {
	return 'class="post-nav-newer fright"';
}
add_filter( 'previous_posts_link_attributes', 'clge_posts_link_attributes_2' );
endif;

if ( ! function_exists( 'clge_clearfix_class' ) ) :
/**
 * Add class to posts for clearfix
 */
function clge_clearfix_class( $classes ) {
	$classes[] = 'clear';
	return $classes;
}
add_filter( 'post_class', 'clge_clearfix_class', 10, 3 );
endif;


if ( ! function_exists( 'clge_new_excerpt_more' ) ) :
/**
 * Add more link text to excerpt
 */
function clge_new_excerpt_more( $more ) {
	return '... <a class="more-link" href="'. esc_url( get_permalink( get_the_ID() ) ) . '#more-' . esc_attr ( get_the_ID() ) . '">' . esc_html__( 'Continue Reading &rarr;', 'clge' ) . '</a>';
}
add_filter( 'excerpt_more', 'clge_new_excerpt_more' );
endif;


if ( ! function_exists( 'clge_url_to_domain' ) ) :
/**
 * Get domain name from URL
 */
function clge_url_to_domain( $url ) {
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
function clge_get_link_url() {
	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}

function clge_block_editor_styles() {
	// Block Styles.
	wp_enqueue_style( 'clge-block-editor-style', get_theme_file_uri( '/editor-blocks.css' ) );
}
add_action( 'enqueue_block_editor_assets', 'clge_block_editor_styles' );

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
