<!DOCTYPE html>
<html <?php language_attributes(); ?> class="twbb">
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
      <title>
		  <?php echo esc_html( wp_get_document_title() ); ?>
      </title>
	<?php endif; ?>
	<?php do_action( 'twbb_header_before' ); ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
do_action( '10web_after_body_open_tag' );
// This is supposed to fix missing Admin Bar with some themes.
do_action( 'wp_body_open' );
?>