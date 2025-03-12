<?php
get_header();
do_action('twbb_before_archive');
\Tenweb_Builder\archiveProductsTemplate::print_twbb_template(\Tenweb_Builder\Templates::get_instance()->get_page_template_id());
do_action('twbb_after_archive');
get_footer();