<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package tenweb-website-builder-theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
    ?>
	</header><!-- .entry-header -->
	<div class="entry-content">
		<?php
    if ( is_singular() ) {
      the_content(sprintf(wp_kses(/* translators: %s: Name of current post. Only visible to screen readers */ __('Continue reading<span class="screen-reader-text"> "%s"</span>', 'tenweb-website-builder-theme'), array(
                                                                                                                                                                                                                   'span' => array(
                                                                                                                                                                                                                     'class' => array(),
                                                                                                                                                                                                                   ),
                                                                                                                                                                                                                 )), get_the_title()));
    } else {
      the_excerpt();
    }
    if ( 'post' === get_post_type() ) :
      ?>
      <div class="entry-meta">
        <?php
        tenweb_builder_theme_posted_on();
        ?>
      </div><!-- .entry-meta -->
    <?php endif;

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'tenweb-website-builder-theme' ),
			'after'  => '</div>',
		) );
		?>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
