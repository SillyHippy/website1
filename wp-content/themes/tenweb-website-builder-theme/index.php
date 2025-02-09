<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package tenweb-website-builder-theme
 */

get_header();

$twb_is_built_with_elementor = FALSE;

if ( defined('ELEMENTOR_VERSION') ) {
    $twb_page = Elementor\Plugin::instance()->documents->get( get_the_ID() );
    if ( !is_bool($twb_page) && $twb_page->is_built_with_elementor() ) {
        $twb_is_built_with_elementor = TRUE;
    }
}


if ( !$twb_is_built_with_elementor ) {
	?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">
		<?php
		if ( have_posts() ) :
			if ( is_home() && ! is_front_page() ) :
				?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>
				<?php
			endif;

			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_type() );

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>
		</main><!-- #main -->
	</div><!-- #primary -->
	<?php
}
else {
  if ( have_posts() ) :
    // If there is no archive template use this template with "Posts" widget.
    $data = [json_decode('{"id":"814276a","elType":"section","settings":[],"elements":[{"id":"eee1e11","elType":"column","settings":{"_column_size":100},"elements":[{"id":"bb4a27a","elType":"widget","settings":{"meta_separator":"\u2022","read_more_text":"Read More","pagination_prev_label":"Prev","pagination_next_label":"Next","pagination_first_label":"First","pagination_last_label":"Last"},"elements":[],"widgetType":"twbb-posts"}],"isInner":false}],"isInner":false}', true)];
    $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( get_the_ID() );
    if ($data != NULL && !is_bool($document)) {
      $document->print_elements_with_wrapper( $data );
    }
  endif;
}
get_footer();
