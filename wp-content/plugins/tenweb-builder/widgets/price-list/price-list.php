<?php
namespace Tenweb_Builder\Widgets\Price_List;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Tenweb_Builder\Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Price_List extends Widget_Base {

	public function get_name() {
		return Builder::$prefix . '_price-list';
	}

	public function get_title() {
		return __( 'Price List', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-price-list twbb-widget-icon';
	}

	public function get_categories() {
		return [ 'tenweb-widgets' ];
	}

	public function get_keywords() {
		return [ 'pricing', 'list', 'product', 'image', 'menu' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_list',
			[
				'label' => __( 'List', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'price',
			[
				'label' => __( 'Price', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title & Description', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => 'true',
			]
		);

		$repeater->add_control(
			'item_description',
			[
				'label' => __( 'Description', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'show_label' => false,
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'tenweb-builder' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __( 'Link', 'tenweb-builder' ),
				'type' => Controls_Manager::URL,
				'default' => [ 'url' => '#' ],
			]
		);

		$this->add_control(
			'price_list',
			[
				'label' => __( 'List Items', 'tenweb-builder' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __( 'First item on the list', 'tenweb-builder' ),
						'item_description' => __( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'tenweb-builder' ),
						'price' => '$20',
						'link' => [ 'url' => '#' ],
					],
					[
						'title' => __( 'Second item on the list', 'tenweb-builder' ),
						'item_description' => __( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'tenweb-builder' ),
						'price' => '$9',
						'link' => [ 'url' => '#' ],
					],
					[
						'title' => __( 'Third item on the list', 'tenweb-builder' ),
						'item_description' => __( 'I am item content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'tenweb-builder' ),
						'price' => '$32',
						'link' => [ 'url' => '#' ],
					],
				],
				'title_field' => '{{{ title }}}', //phpcs:ignore WordPressVIPMinimum.Security.Mustache.OutputNotation
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_list_style',
			[
				'label' => __( 'List', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading__title',
			[
				'label' => __( 'Title & Price', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY,
                ],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-list-header' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'heading_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .elementor-price-list-header',
			]
		);

		$this->add_control(
			'heading_item_description',
			[
				'label' => __( 'Description', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_TEXT,
                ],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-list-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
				'selector' => '{{WRAPPER}} .elementor-price-list-description',
			]
		);

		$this->add_control(
			'heading_separator',
			[
				'label' => __( 'Separator', 'tenweb-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'separator_style',
			[
				'label' => __( 'Style', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid' => __( 'Solid', 'tenweb-builder' ),
					'dotted' => __( 'Dotted', 'tenweb-builder' ),
					'dashed' => __( 'Dashed', 'tenweb-builder' ),
					'double' => __( 'Double', 'tenweb-builder' ),
					'none' => __( 'None', 'tenweb-builder' ),
				],
				'default' => 'dotted',
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .elementor-price-list-separator' => 'border-bottom-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'separator_weight',
			[
				'label' => __( 'Weight', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 10,
					],
				],
				'condition' => [
					'separator_style!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-list-separator' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
				'default' => [
					'size' => 2,
				],
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label' => __( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
                'global' => [
                    'default' => Global_Colors::COLOR_SECONDARY,
                ],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-list-separator' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'separator_style!' => 'none',
				],
			]
		);

		$this->add_control(
			'separator_spacing',
			[
				'label' => __( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 40,
					],
				],
				'condition' => [
					'separator_style!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-list-separator' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[
				'label' => __( 'Image', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_size',
				'default' => 'thumbnail',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-list-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_spacing',
			[
				'label' => __( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'body.rtl {{WRAPPER}} .elementor-price-list-image' => 'padding-left: calc({{SIZE}}{{UNIT}}/2);',
					'body.rtl {{WRAPPER}} .elementor-price-list-image + .elementor-price-list-text' => 'padding-right: calc({{SIZE}}{{UNIT}}/2);',
					'body:not(.rtl) {{WRAPPER}} .elementor-price-list-image' => 'padding-right: calc({{SIZE}}{{UNIT}}/2);',
					'body:not(.rtl) {{WRAPPER}} .elementor-price-list-image + .elementor-price-list-text' => 'padding-left: calc({{SIZE}}{{UNIT}}/2);',
				],
				'default' => [
					'size' => 20,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_item_style',
			[
				'label' => __( 'Item', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_control(
			'row_gap',
			[
				'label' => __( 'Rows Gap', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
						'step' => 0.1,
					],
				],
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-list li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'default' => [
					'size' => 20,
				],
			]
		);

		$this->add_control(
			'vertical_align',
			[
				'label' => __( 'Vertical Align', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'top' => __( 'Top', 'tenweb-builder' ),
					'bottom' => __( 'Bottom', 'tenweb-builder' ),
					'center' => __( 'Center', 'tenweb-builder' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-price-list-item' => 'align-items: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'bottom' => 'flex-end',
				],
				'default' => 'top',
			]
		);

		$this->end_controls_section();
	}

	private function render_image( $item, $instance ) {
		$image_id = $item['image']['id'];
		$image_size = $instance['image_size_size'];
		if ( 'custom' === $image_size ) {
			$image_src = Group_Control_Image_Size::get_attachment_image_src( $image_id, 'image_size', $instance );
		} else {
			$image_src = wp_get_attachment_image_src( $image_id, $image_size );
			$image_src = $image_src[0];
		}

		return sprintf( '<img src="%s" alt="%s" />', $image_src, $item['title'] );
	}

	private function render_item_header( $item ) {
		$url = $item['link']['url'];

		$item_id = $item['_id'];

		if ( $url ) {
			$unique_link_id = 'item-link-' . $item_id;

			$this->add_render_attribute( $unique_link_id, [
				'href' => $url,
				'class' => 'elementor-price-list-item',
			] );

			if ( $item['link']['is_external'] ) {
				$this->add_render_attribute( $unique_link_id, 'target', '_blank' );
			}

			return '<li><a ' . $this->get_render_attribute_string( $unique_link_id ) . '>';
		} else {
			return '<li class="elementor-price-list-item">';
		}
	}

	private function render_item_footer( $item ) {
		if ( $item['link']['url'] ) {
			echo '</a></li>';
		} else {
			echo '</li>';
		}
	}

	protected function render() {
		$settings = $this->get_settings(); ?>

		<ul class="elementor-price-list">

		<?php foreach ( $settings['price_list'] as $index => $item ) : ?>
			<?php if ( ! empty( $item['title'] ) || ! empty( $item['price'] ) || ! empty( $item['item_description'] ) ) : ?>
				<?php
                // PHPCS - the method render_item_header is safe.
                echo $this->render_item_header( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( ! empty( $item['image']['url'] ) ) : ?>
					<div class="elementor-price-list-image">
					<?php
                    // PHPCS - the method render_image is safe.
                    echo $this->render_image( $item, $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<?php endif; ?>

				<div class="elementor-price-list-text">
				<?php if ( ! empty( $item['title'] ) || ! empty( $item['price'] ) ) : ?>
					<div class="elementor-price-list-header">
					<?php if ( ! empty( $item['title'] ) ) : ?>
						<span class="elementor-price-list-title">
                            <?php $this->print_unescaped_setting( 'title', 'price_list', $index ); ?>
                        </span>
					<?php endif; ?>
						<?php if ( 'none' !== $settings['separator_style'] ) : ?>
							<span class="elementor-price-list-separator"></span>
						<?php endif; ?>
						<?php if ( ! empty( $item['price'] ) ) : ?>
							<span class="elementor-price-list-price">
                                <?php $this->print_unescaped_setting( 'price', 'price_list', $index ); ?>
                            </span>
						<?php endif; ?>
				</div>
				<?php endif; ?>
					<?php if ( ! empty( $item['item_description'] ) ) : ?>
						<p class="elementor-price-list-description">
                            <?php $this->print_unescaped_setting( 'item_description', 'price_list', $index ); ?>
                        </p>
					<?php endif; ?>
			</div>
				<?php $this->render_item_footer( $item ); ?>
			<?php endif; ?>
		<?php endforeach; ?>

		</ul>

		<?php
	}
//phpcs:disable
	protected function content_template() {
		?>
		<ul class="elementor-price-list">
			<#
				for ( var i in settings.price_list ) {
					var item = settings.price_list[i],
						item_open_wrap = '<li class="elementor-price-list-item">',
						item_close_wrap = '</li>';
					if ( item.link.url ) {
						item_open_wrap = '<li><a href="' + item.link.url + '" class="elementor-price-list-item">';
						item_close_wrap = '</a></li>';
					}

					if ( ! _.isEmpty( item.title ) || ! _.isEmpty( item.price ) || ! _.isEmpty( item.description ) || ! _.isEmpty( item.image ) ) { #>

					{{{ item_open_wrap }}}
					<# if ( item.image && item.image.id ) {

						var image = {
							id: item.image.id,
							url: item.image.url,
							size: settings.image_size_size,
							dimension: settings.image_size_custom_dimension,
							model: view.getEditModel()
						};

						var image_url = elementor.imagesManager.getImageUrl( image );

						if ( image_url ) { #>
							<div class="elementor-price-list-image"><img src="{{ image_url }}" alt="{{ item.title }}"></div>
						<# } #>

					<# } #>


					<# if ( ! _.isEmpty( item.title ) || ! _.isEmpty( item.price ) || ! _.isEmpty( item.item_description ) ) { #>
						<div class="elementor-price-list-text">

							<# if ( ! _.isEmpty( item.title ) || ! _.isEmpty( item.price ) ) { #>
								<div class="elementor-price-list-header">

								<# if ( ! _.isEmpty( item.title ) ) { #>
									<span class="elementor-price-list-title">{{{ item.title }}}</span>
								<# } #>

								<# if ( 'none' != settings.separator_style ) { #>
									<span class="elementor-price-list-separator"></span>
								<# } #>

								<# if ( ! _.isEmpty( item.price ) ) { #>
									<span class="elementor-price-list-price">{{{ item.price }}}</span>
								<# } #>

								</div>
							<# } #>

							<# if ( ! _.isEmpty( item.item_description ) ) { #>
								<p class="elementor-price-list-description">{{{ item.item_description }}}</p>
							<# } #>

						</div>
					<# } #>

					{{{ item_close_wrap }}}

					<# } #>
			 <# } #>
		</ul>
		<?php
	}
    //phpcs:enable
}
\Elementor\Plugin::instance()->widgets_manager->register(new Price_List());
