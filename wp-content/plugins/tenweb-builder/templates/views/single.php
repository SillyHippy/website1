<?php
get_header();
do_action('twbb_before_single');
\Tenweb_Builder\SingleTemplate::print_twbb_template(\Tenweb_Builder\Templates::get_instance()->get_page_template_id());
do_action('twbb_after_single');
get_footer();