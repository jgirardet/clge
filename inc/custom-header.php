<?php
/**
 * Implement Custom Header functionality for Clge
 *
 * @package Clge
 */


/**
 * Set up the WordPress core custom header settings.
 *
 */
function clge_custom_header_setup() {
	/**
	 * Filter Clge custom-header support arguments.
	 *
	 */

	add_theme_support( 'custom-header', apply_filters( 'clge_custom_header_args', array(
		'width'	=> 1440,
		'height' => 221,
		'flex-height' => true,
		'default-image' => get_template_directory_uri() . '/images/header.jpg',
		'uploads' => true,
		'header-text' => true,
		'wp-head-callback' => 'clge_header_style',
	) ) );

	register_default_headers( array(
		'default' => array(
			'url'           => '%s/images/header.jpg',
			'thumbnail_url' => '%s/images/header.jpg',
			'description'   => esc_html__( 'Default Header', 'clge' )
		),
	) );
}
add_action( 'after_setup_theme', 'clge_custom_header_setup' );


if ( ! function_exists( 'clge_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 */
function clge_header_style() {
	$text_color = get_header_textcolor();

	// If no custom color for text is set, let's bail.
	if ( display_header_text() && $text_color === get_theme_support( 'custom-header', 'default-text-color' ) ) :
		return;
	endif;

	// If we get this far, we have custom styles.
	?>
	<style type="text/css" id="clge-header-css">
	<?php
		// Has the text been hidden?
		if ( ! display_header_text() ) :
	?>
		.site-title,
		.site-description {
			clip: rect(1px 1px 1px 1px); /* IE7 */
			clip: rect(1px, 1px, 1px, 1px);
			position: absolute;
		}
	<?php
		// If the user has set a custom color for the text, use that.
		elseif ( $text_color != get_theme_support( 'custom-header', 'default-text-color' ) ) :
	?>
		.site-title a {
			color: #<?php echo esc_attr( $text_color ); ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // clge_header_style
