<?php
/**
 * Implement Custom Header functionality for Baskerville 2
 *
 * @package Baskerville 2
 */


/**
 * Set up the WordPress core custom header settings.
 *
 */
function baskerville_2_custom_header_setup() {
	/**
	 * Filter Baskerville 2 custom-header support arguments.
	 *
	 */

	add_theme_support( 'custom-header', apply_filters( 'baskerville_2_custom_header_args', array(
		'width'	=> 1440,
		'height' => 221,
		'flex-height' => true,
		'default-image' => get_template_directory_uri() . '/images/header.jpg',
		'uploads' => true,
		'header-text' => true,
		'wp-head-callback' => 'baskerville_2_header_style',
	) ) );

	register_default_headers( array(
		'default' => array(
			'url'           => '%s/images/header.jpg',
			'thumbnail_url' => '%s/images/header.jpg',
			'description'   => esc_html__( 'Default Header', 'baskerville-2' )
		),
	) );
}
add_action( 'after_setup_theme', 'baskerville_2_custom_header_setup' );


if ( ! function_exists( 'baskerville_2_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 */
function baskerville_2_header_style() {
	$text_color = get_header_textcolor();

	// If no custom color for text is set, let's bail.
	if ( display_header_text() && $text_color === get_theme_support( 'custom-header', 'default-text-color' ) ) :
		return;
	endif;

	// If we get this far, we have custom styles.
	?>
	<style type="text/css" id="baskerville-header-css">
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
endif; // baskerville_2_header_style
