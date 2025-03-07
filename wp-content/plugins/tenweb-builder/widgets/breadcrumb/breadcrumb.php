<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Breadcrumb extends Widget_Base {

	public function get_name() {
		return 'twbb_breadcrumb';
	}

	public function get_title() {
		return __( 'Breadcrumbs', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-template-breadcrumbs twbb-widget-icon';
	}

	public function get_categories() {
        return [ 'tenweb-builder-widgets' ];
	}

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'plugin-name' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __( 'Text Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .twbb-breadcrumb > li' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'link_color',
            [
                'label' => __( 'Link Color', 'tenweb-builder' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .twbb-breadcrumb > li > a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .twbb-breadcrumb > li, {{WRAPPER}} .twbb-breadcrumb > li > a',
            ]
        );

        $this->add_responsive_control(
            'alignment',
            [
                'label' => __( 'Alignment', 'tenweb-builder' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'tenweb-builder' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'tenweb-builder' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'tenweb-builder' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-breadcrumb' => 'justify-content: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'separator_icon',
            [
                'label' => __('Separator Icon', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fa fa-angle-right',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'tenweb-builder' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 5,
                    ],
                    'rem' => [
                        'min' => 0.5,
                        'max' => 5,
                    ],
                ],
                'default' => [
                    'size' => 12,
                ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-breadcrumb .breadcrumb-separator i' => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .twbb-breadcrumb .breadcrumb-separator svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'hide_current',
            [
                'label' => esc_html__( 'Hide the current page', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'tenweb-builder' ),
                'label_off' => esc_html__( 'No', 'tenweb-builder' ),
                'default' => 'no',
                'separator' => 'before',
            ]
        );




        $this->end_controls_section();
    }

    protected function render() {
        $home_title         = __( 'Home', 'tenweb-builder' );
        $settings = $this->get_settings_for_display();
        global $post;
        if ( !is_front_page() ) {
            $url_home = apply_filters('twbb_breadcrumb_url_home', get_home_url());
            echo '<ul class="twbb-breadcrumb">';
            echo '<li><a href="' . $url_home . '">' . $home_title . '</a><span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
            echo '</span></li>';
            if ( is_home() ) {
                if( $settings['hide_current'] !== 'yes' ) {
                    echo '<li>' . get_the_title( get_option('page_for_posts', true) ) . '</li>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }
            } elseif ( is_single() && !is_attachment() ) {
                if ( get_post_type() !== 'post' ) {
                    $post_type = get_post_type_object( get_post_type() );
                    $slug = $post_type->rewrite;
                    echo '<li><a href="' . get_post_type_archive_link( get_post_type() ) . '">' . $post_type->labels->singular_name . '</a><span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';
                }
                if ( get_the_category() ) {
                    $cat = get_the_category();
                    $cat = $cat[0];
                    $cats = get_category_parents( $cat, TRUE, '' );
                        echo '<li>' . $cats . '<span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                        echo '</span></li>';
                }
                if( $settings['hide_current'] !== 'yes' ) {
                    echo '<li>' . get_the_title() . '</li>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }
            } elseif ( is_page() ) {
                if ( $post->post_parent ) {
                    $anc = get_post_ancestors( $post->ID );
                    $anc = array_reverse( $anc );
                    foreach ( $anc as $ancestor ) {
                        echo '<li><a href="' . get_permalink( $ancestor ) . '">' . get_the_title( $ancestor ) . '</a><span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                        echo '</span></li>';
                    }
                }
                if( $settings['hide_current'] !== 'yes' ) {
                    echo '<li>' . get_the_title() . '<span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';
                }
            } elseif ( is_category() ) {
                if( $settings['hide_current'] !== 'yes' ) {
                    echo '<li>' . single_cat_title('', false) . '<span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';
                }

            } elseif ( is_tag() ) {
                if( $settings['hide_current'] !== 'yes' ) {
                    echo '<li>' . single_tag_title('', false) . '<span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';
                }
            } elseif ( is_day() ) {
                if( $settings['hide_current'] !== 'yes' ) {
                    echo '<li>' . get_the_time('Y') . '<span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';

                    echo '<li>' . get_the_time('F') . '<span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';

                    echo '<li>' . get_the_time('d') . '<span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';
                }

            } elseif ( is_month() ) {
                if( $settings['hide_current'] !== 'yes' ) {
                    echo '<li>' . get_the_time('Y') . '<span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';

                    echo '<li>' . get_the_time('F') . '<span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';
                }

            } elseif ( is_year() ) {
                if( $settings['hide_current'] !== 'yes' ) {
                    echo '<li>' . get_the_time('Y') . '<span class="breadcrumb-separator">'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';
                }

            } elseif ( is_author() ) {
                if( $settings['hide_current'] !== 'yes' ) {
                    echo '<li>' . get_the_author() . '<span class="breadcrumb-separator">';
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';
                }
            } elseif ( is_archive() && !is_tax() && !is_category() && !is_tag() ) {
                if( $settings['hide_current'] !== 'yes' ) {
                    echo '<li>' . post_type_archive_title('', false) . '<span class="breadcrumb-separator">';
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';
                }
            } elseif ( is_search() ) {
                if( $settings['hide_current'] !== 'yes' ) {
                    echo '<li>Search results for: ' . get_search_query() . '<span class="breadcrumb-separator">';
                    \Elementor\Icons_Manager::render_icon($settings['separator_icon'], ['aria-hidden' => 'true']);
                    echo '</span></li>';
                }
            }
            echo '</ul>';
        }
    }

}

\Elementor\Plugin::instance()->widgets_manager->register( new Breadcrumb() );
