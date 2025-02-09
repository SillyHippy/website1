<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package tenweb-website-builder-theme
 */
$is_search = is_search();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'tenweb-website-builder-theme' ); ?></a>

	<header id="masthead" class="site-header <?php echo $is_search ? 'site-header-search' : ''; ?>">
    <div class="site-header-container">
      <div class="site-header-main">
        <div class="site-branding">
          <?php
          the_custom_logo();
          if ( is_front_page() && is_home() ) :
            ?>
            <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
            <?php
          else :
            ?>
            <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
            <?php
          endif;
          $tenweb_builder_theme_description = get_bloginfo( 'description', 'display' );
          if ( $tenweb_builder_theme_description || is_customize_preview() ) :
            ?>
            <p class="site-description"><?php echo $tenweb_builder_theme_description; /* WPCS: xss ok. */ ?></p>
          <?php endif; ?>
        </div><!-- .site-branding -->
      </div>
      <?php
      if ( $is_search ) {
        get_search_form();
      }
      ?>
    </div>
  </header><!-- #masthead -->

	<div id="content" class="site-content">
