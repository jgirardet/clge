<?php
/**
 * This template is for displaying comments
 *
 * @package Baskerville 2
 */

if ( post_password_required() ) {
		return;
	}
?>

<?php if ( have_comments() ) : ?>
	<div class="comments">

		<a name="comments"></a>
		<div class="comments-title-container clear">
			<h2 class="comments-title fleft">
				<?php
					printf( // WPCS: XSS OK.
						esc_html( _nx( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'baskerville-2' ) ),
						number_format_i18n( get_comments_number() ),
						'<span>' . get_the_title() . '</span>'
					);
				?>
			</h2><!-- .comments-title -->

			<?php if ( comments_open() ) : ?>
				<h2 class="add-comment-title fright"><a href="#respond"><?php esc_html_e( 'Add yours', 'baskerville-2' ) . ' &rarr;'; ?></a></h2>
			<?php endif; ?>

		</div> <!-- /comments-title-container -->

		<ol class="comment-list">
		    <?php wp_list_comments(
					array( 'avatar_size' => 80,
						   'short_ping'  => true,
					       'style'       => 'ol', )
			); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<div class="comment-nav-below clear" role="navigation">
				<div class="post-nav-older fleft"><?php previous_comments_link( '&laquo; ' . esc_html__( 'Older Comments', 'baskerville-2' ) ); ?></div>
				<div class="post-nav-newer fright"><?php next_comments_link( esc_html__( 'Newer Comments', 'baskerville-2' ) . ' &raquo;' ); ?></div>
			</div> <!-- /comment-nav-below -->
		<?php endif; ?>

	</div><!-- /comments -->
<?php endif; ?>

<?php if ( ! comments_open() && ! is_page() ) : ?>

	<p class="nocomments"><?php esc_html_e( 'Comments are closed.', 'baskerville-2' ); ?></p>

<?php endif; ?>

<?php comment_form(); ?>
