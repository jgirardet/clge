<?php
/**
 * This template is for displaying the website's footer and widgets
 *
 * @package Clge
 */
?>

	<div class="footer bg-graphite" id="footer">
		<div class="section-inner row clear" role="complementary">

			<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>

				<div class="column column-1 one-third medium-padding">
					<div class="widgets">
						<?php dynamic_sidebar( 'sidebar-2' ); ?>
					</div>
				</div>

			<?php endif; ?> <!-- /sidebar-2 -->

			<?php if ( is_active_sidebar( 'sidebar-3' ) ) : ?>

				<div class="column column-2 one-third medium-padding">
					<div class="widgets">
						<?php dynamic_sidebar( 'sidebar-3' ); ?>
					</div> <!-- /widgets -->
				</div>

			<?php endif; ?> <!-- /sidebar-3 -->

			<?php if ( is_active_sidebar( 'sidebar-4' ) ) : ?>

				<div class="column column-3 one-third medium-padding">
					<div class="widgets">
						<?php dynamic_sidebar( 'sidebar-4' ); ?>
					</div> <!-- /widgets -->
				</div>

			<?php endif; ?> <!-- /sidebar-4 -->

		</div> <!-- /section-inner -->
	</div> <!-- /footer -->


	<div class="credits section bg-dark small-padding">
		<div class="credits-inner section-inner clear">

			<p class="credits-left fleft">
				<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'clge' ) ); ?>"><?php printf( esc_html__( 'Proudly powered by %s', 'clge' ), 'WordPress' ); ?></a>
				<span class="sep"> | </span>
				<?php printf( esc_html__( 'Theme: %1$s by %2$s.', 'clge' ), 'Clge', '<a href="http://www.andersnoren.se/teman/clge-wordpress-theme/" rel="designer">Anders Noren</a>' ); ?>
			</p>

			<p class="credits-right fright">
				<a class="tothetop" title="<?php esc_attr_e( 'To the top', 'clge' ); ?>" href="#"><?php esc_attr_e( 'Up', 'clge' ); ?> &uarr;</a>
			</p>
		</div> <!-- /credits-inner -->
	</div> <!-- /credits -->

<?php wp_footer(); ?>

</body>
</html>
