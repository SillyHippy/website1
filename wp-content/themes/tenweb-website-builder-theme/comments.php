<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package tenweb-website-builder-theme
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) :
		?>
		<h2 class="comments-title">
			<?php
			$tenweb_builder_theme_comment_count = get_comments_number();
			if ( '1' === $tenweb_builder_theme_comment_count ) {
				printf(
					/* translators: 1: title. */
					esc_html__( 'One thought on &ldquo;%1$s&rdquo;', 'tenweb-website-builder-theme' ),
					'<span>' . get_the_title() . '</span>'
				);
			} else {
				printf( // WPCS: XSS OK.
					/* translators: 1: comment count number, 2: title. */
					esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $tenweb_builder_theme_comment_count, 'comments title', 'tenweb-website-builder-theme' ) ),
					number_format_i18n( $tenweb_builder_theme_comment_count ),
					'<span>' . get_the_title() . '</span>'
				);
			}
			?>
		</h2><!-- .comments-title -->

		<?php the_comments_navigation(); ?>

		<ul class="comment-list">
			<?php wp_list_comments(array('callback' => 'tenweb_builder_comment', 'style' => 'ul')); ?>
		</ul><!-- .comment-list -->

		<?php
		the_comments_navigation();

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'tenweb-website-builder-theme' ); ?></p>
			<?php
		endif;

	endif; // Check for have_comments().

    $commenter = wp_get_current_commenter();
    $consent           = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';
    $fields = array(
      'author' => '<div class="clear comment_fields"><p class="comment-form-author comments-form-field"><input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" required placeholder="' . __('Name*', 'theme_10web') . '" /></p>',
      'email' => '<p class="comment-form-email comments-form-field"><input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" required placeholder="' . __('Email*', 'theme_10web') . '" /><span>Your email will not be published.</span></p>',
      'url' => '<p class="comment-form-url comments-form-field"><input id="url" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" placeholder="' . __('Website', 'theme_10web') . '" /></p></div>'
    );
    $defaults = array(
      'title_reply' => '',
      'fields' => apply_filters('comment_form_default_fields', $fields),
      'comment_field' => '<div class="comment-form-comment"><div class="comment-form-comment-text"><textarea  placeholder="' . __('Write a comment...', 'theme_10web') . '" id="comment" name="comment" cols="45" rows="5" aria-required="true" required></textarea></div></div>'
    );
    comment_form($defaults);
	?>

</div><!-- #comments -->
