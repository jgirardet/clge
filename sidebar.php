<?php
/**
 * This template is for displaying the website's sidebar and widgets
 *
 * @package Baskerbille
 */
?>

<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>

	<aside class="sidebar fright" role="complementary">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</aside><!-- /sidebar -->

<?php endif; ?>
