<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package tenweb-website-builder-theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="search-content-row">
    <header class="entry-header">
      <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
    </header><!-- .entry-header -->

    <div class="entry-summary">
      <?php the_excerpt(); ?>
      <?php if ( 'post' === get_post_type() ) : ?>
        <div class="entry-meta">
          <?php
          tenweb_builder_theme_posted_on();
          ?>
        </div><!-- .entry-meta -->
      <?php endif; ?>

    </div><!-- .entry-summary -->
  </div>
</article><!-- #post-<?php the_ID(); ?> -->
