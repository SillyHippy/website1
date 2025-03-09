<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Logos extends Widget_Base {

    public function get_name()
    {
        return Builder::$prefix . '_logos';
    }

    public function get_title()
    {
        return __('Logos', Builder::$prefix);
    }

    public function get_icon() {
        return 'twbb-widget-icon twbb-logos';
    }

    public function get_categories() {
        return [ 'tenweb-widgets' ];
    }

    public function get_keywords() {
        return [ 'slides', 'carousel', 'image', 'slider', 'logo' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_logos',
            [
                'label' => esc_html__( 'Logos', 'tenweb-builder' ),
            ]
        );
        $this->add_control(
            'logos_gallery',
            [
                'label' => esc_html__( 'Add Logos', 'tenweb-builder' ),
                'type' => Controls_Manager::GALLERY,
                'show_label' => false,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );
        $this->add_control(
            'logos_grayscale',
            [
                'label' => __( 'Grayscale', 'tenweb-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __( 'Off', 'tenweb-builder' ),
                'label_on' => __( 'On', 'tenweb-builder' ),
                'default' => 'yes',
                'return_value' => 'yes',
                'selectors' => [
                    '{{WRAPPER}} .twbb-logos .twbb-logos__item' => '-webkit-filter: grayscale(100%); filter: grayscale(100%);',
                ],
            ]
        );
        $this->add_control(
            'logos_direction',
            [
                'label' => __( 'Direction', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => [
                    'normal' => __( 'Normal', 'tenweb-builder' ),
                    'reverse' => __( 'Reverse', 'tenweb-builder' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .twbb-main-logos-slider-container .twbb-logos-slider-container .twbb-logos.twbb-logos-animated' => 'animation-direction: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'logos_animation_speed',
            [
                'label' => esc_html__( 'Animation Speed', 'tenweb-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => [
                    'slow' => __( 'Slow', 'tenweb-builder' ),
                    'normal' => __( 'Normal', 'tenweb-builder' ),
                    'fast' => __( 'Fast', 'tenweb-builder' ),
                ],
                'render_type' => 'template',
                'prefix_class' => 'twbb-logos-animation-speed-',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_logos_style',
            [
                'label' => esc_html__( 'Logos', 'tenweb-builder' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'logos_height',
            [
                'label' => esc_html__( 'Height', 'tenweb-builder' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 56,
                'selectors' => [
                    '{{WRAPPER}} .twbb-logos-slider-container .twbb-logos .twbb-logos__item' => 'height: {{SIZE}}px;',
                ],
                'render_type' => 'template',
            ]
        );
        $this->end_controls_section();
    }

    protected function render()
    {
        ?>
        <div class="twbb-main-logos-slider-container">
            <div class="twbb-logos-slider-container">
                <div class="twbb-logos">
                    <?php
                    $settings = $this->get_settings_for_display();
                    $logos = $settings['logos_gallery'];
                    if( empty($logos) ) {
                        //set default logos for beautiful design
                        ?>
                        <svg class="twbb-logos__item" xmlns="http://www.w3.org/2000/svg" width="176" height="40" fill="none" viewBox="0 0 176 40"><path fill="#283841" fill-rule="evenodd" d="M15 28a5 5 0 0 1-5-5V0H0v23c0 8.284 6.716 15 15 15h11V28H15ZM45 10a9 9 0 1 0 0 18 9 9 0 0 0 0-18Zm-19 9C26 8.507 34.507 0 45 0s19 8.507 19 19-8.507 19-19 19-19-8.507-19-19ZM153 10a9 9 0 0 0-9 9 9 9 0 0 0 9 9 9 9 0 0 0 9-9 9 9 0 0 0-9-9Zm-19 9c0-10.493 8.507-19 19-19s19 8.507 19 19-8.507 19-19 19-19-8.507-19-19ZM85 0C74.507 0 66 8.507 66 19s8.507 19 19 19h28c1.969 0 3.868-.3 5.654-.856L124 40l5.768-10.804A19.007 19.007 0 0 0 132 20.261V19c0-10.493-8.507-19-19-19H85Zm37 19a9 9 0 0 0-9-9H85a9 9 0 1 0 0 18h28a9 9 0 0 0 9-8.93V19Z" clip-rule="evenodd"/><path fill="#283841" d="M176 2.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"/></svg>
                        <svg class="twbb-logos__item" xmlns="http://www.w3.org/2000/svg" width="169" height="40" viewBox="0 0 169 40" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0148 2.5V40H0V2.5H10.0148Z" fill="#283841"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0222 2.5H36.3037C43.2175 2.5 48.8222 8.09644 48.8222 15C48.8222 21.9036 43.2175 27.5 36.3037 27.5H25.037V40H15.0222V2.5ZM25.037 17.5H36.3037C37.6865 17.5 38.8074 16.3807 38.8074 15C38.8074 13.6193 37.6865 12.5 36.3037 12.5H25.037V17.5Z" fill="#283841"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M86.3778 2.5V21.875C86.3778 26.3623 90.0208 30 94.5148 30C99.0088 30 102.652 26.3623 102.652 21.875V2.5H112.667V21.875C112.667 31.8852 104.54 40 94.5148 40C84.4898 40 76.363 31.8852 76.363 21.875V2.5H86.3778Z" fill="#283841"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M52.5778 20C52.5778 10.335 60.4244 2.5 70.1037 2.5H72.6074V12.5H70.1037C65.9554 12.5 62.5926 15.8579 62.5926 20V21.25C62.5926 31.6053 54.1855 40 43.8148 40H42.563V30H43.8148C48.6545 30 52.5778 26.0825 52.5778 21.25V20Z" fill="#283841"/>
                            <path d="M169 3.75C169 5.82107 167.319 7.5 165.244 7.5C163.17 7.5 161.489 5.82107 161.489 3.75C161.489 1.67893 163.17 0 165.244 0C167.319 0 169 1.67893 169 3.75Z" fill="#283841"/>
                            <path d="M123.42 40L128.199 20.0181L131.752 32.0393C133.87 39.2091 144.041 39.2091 146.16 32.0393L149.712 20.0181L154.491 40H164.787L157.273 8.57949C155.486 1.10744 144.941 0.830781 142.763 8.19891L138.956 21.0833L135.148 8.19892C132.971 0.830824 122.425 1.1074 120.638 8.57948L113.124 40H123.42Z" fill="#283841"/>
                        </svg>
                        <svg class="twbb-logos__item" xmlns="http://www.w3.org/2000/svg" width="220" height="40" fill="none" viewBox="0 0 220 40"><path fill="#0E1534" d="M20 40c11.046 0 20-8.954 20-20V6a6 6 0 0 0-6-6H21v8.774c0 2.002.122 4.076 1.172 5.78a9.999 9.999 0 0 0 6.904 4.627l.383.062a.8.8 0 0 1 0 1.514l-.383.062a10 10 0 0 0-8.257 8.257l-.062.383a.8.8 0 0 1-1.514 0l-.062-.383a10 10 0 0 0-4.627-6.904C12.85 21.122 10.776 21 8.774 21H.024C.547 31.581 9.29 40 20 40Z"/><path fill="#0E1534" d="M0 19h8.774c2.002 0 4.076-.122 5.78-1.172a10.018 10.018 0 0 0 3.274-3.274C18.878 12.85 19 10.776 19 8.774V0H6a6 6 0 0 0-6 6v13ZM46.455 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM211.711 12.104c5.591 0 8.289 3.905 8.289 8.428v8.495h-5.851V21.54c0-2.05-.748-3.742-2.893-3.742-2.145 0-2.86 1.692-2.86 3.742v7.486h-5.851V21.54c0-2.05-.715-3.742-2.861-3.742-2.145 0-2.893 1.692-2.893 3.742v7.486h-5.85v-8.495c0-4.523 2.697-8.428 8.288-8.428 3.056 0 5.266 1.204 6.274 3.189 1.072-1.985 3.413-3.19 6.208-3.19ZM180.427 23.82c1.885 0 2.698-1.725 2.698-3.776v-7.29h5.85v8.006c0 4.784-2.795 8.755-8.548 8.755-5.754 0-8.549-3.97-8.549-8.755v-8.006h5.851v7.29c0 2.05.812 3.776 2.698 3.776ZM163.275 29.547c-3.673 0-6.046-1.269-7.444-3.742l4.226-2.376c.585 1.041 1.462 1.562 2.925 1.562 1.203 0 1.755-.423 1.755-.944 0-1.985-8.581.033-8.581-6.28 0-3.06 2.6-5.533 7.021-5.533 3.868 0 5.981 1.887 6.924 3.71l-4.226 2.408c-.357-.976-1.463-1.562-2.568-1.562-.845 0-1.3.358-1.3.846 0 2.018 8.581.163 8.581 6.281 0 3.417-3.348 5.63-7.313 5.63ZM142.833 36.512h-5.851V20.858c0-4.98 3.738-8.592 8.939-8.592 5.071 0 8.939 3.873 8.939 8.592 0 5.207-3.446 8.657-8.614 8.657-1.203 0-2.405-.358-3.413-.912v7.909Zm3.088-12.497c1.853 0 3.088-1.432 3.088-3.125 0-1.724-1.235-3.124-3.088-3.124s-3.088 1.4-3.088 3.125c0 1.692 1.235 3.124 3.088 3.124ZM131.121 11.03c-1.918 0-3.51-1.595-3.51-3.515 0-1.92 1.592-3.515 3.51-3.515 1.918 0 3.511 1.595 3.511 3.515 0 1.92-1.593 3.515-3.511 3.515Zm-2.925 1.724h5.851v16.273h-5.851V12.754ZM116.97 29.515c-5.071 0-8.939-3.905-8.939-8.657 0-4.719 3.868-8.624 8.939-8.624s8.939 3.905 8.939 8.624c0 4.752-3.868 8.657-8.939 8.657Zm0-5.5c1.853 0 3.088-1.432 3.088-3.125 0-1.724-1.235-3.156-3.088-3.156s-3.088 1.432-3.088 3.156c0 1.693 1.235 3.125 3.088 3.125ZM96.983 37c-4.03 0-6.956-1.79-8.451-4.98l4.843-2.603c.52 1.107 1.495 2.246 3.51 2.246 2.114 0 3.511-1.335 3.674-3.678-.78.684-2.016 1.204-3.868 1.204-4.519 0-8.16-3.482-8.16-8.364 0-4.718 3.869-8.559 8.94-8.559 5.201 0 8.939 3.613 8.939 8.592v6.444c0 5.858-4.064 9.698-9.427 9.698Zm.39-13.31c1.755 0 3.088-1.205 3.088-2.995 0-1.757-1.332-2.929-3.088-2.929-1.723 0-3.088 1.172-3.088 2.93 0 1.79 1.365 2.993 3.088 2.993ZM78.607 29.515c-5.071 0-8.94-3.905-8.94-8.657 0-4.719 3.869-8.624 8.94-8.624 5.07 0 8.939 3.905 8.939 8.624 0 4.752-3.869 8.657-8.94 8.657Zm0-5.5c1.853 0 3.088-1.432 3.088-3.125 0-1.724-1.235-3.156-3.088-3.156s-3.088 1.432-3.088 3.156c0 1.693 1.235 3.125 3.088 3.125ZM59.013 7.06v16.434H68.7v5.533H58.2c-3.705 0-5.2-1.953-5.2-5.045V7.06h6.013Z"/></svg>
                        <?php
                    }
                    foreach ($logos as $logo) {
                        if (!empty($logo['url'])) {
                            $title = isset($logo['title']) ? $logo['title'] : 'Logo';
                            echo '<img class="twbb-logos__item" src="' . esc_url($logo['url']) . '" alt="' . esc_attr($title) . '">';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Logos());