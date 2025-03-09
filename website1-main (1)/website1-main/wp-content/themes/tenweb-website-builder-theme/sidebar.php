<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package tenweb-website-builder-theme
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area">
  <?php
  if ( has_nav_menu( 'sidebar_menu' ) ) { ?>
  <nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Sidebar Menu', 'tenweb-website-builder-theme' ); ?>">
    <?php
    wp_nav_menu( array(
                   'theme_location' => 'sidebar_menu',
                 ) );
    ?>
  </nav><!-- .footer-navigation -->
  <?php } ?>
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- #secondary -->
