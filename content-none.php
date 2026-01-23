<?php
/**
 * This template is used when there are no posts to return.
 *
 * @package Clge
 */
?>

	<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

		<div class="post-content">
			<p><?php printf( esc_html__( 'Ready to publish your first post? %1$sGet started here%2$s.', 'clge' ), '<a href="' . esc_url( admin_url( 'post-new.php' ) ) . '">', '</a>'  ); ?></p>
		</div> <!-- /post-content -->

	<?php elseif ( is_search() ) : ?>

		<div class="post-content">
			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'clge' ); ?></p>
			<?php get_search_form(); ?>
		</div>

	<?php else : ?>

		<div class="post no-results">
			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'clge' ); ?></p>
			<?php get_search_form(); ?>
		</div>

	<?php endif; ?>
</div> <!-- /post -->
