<?php
namespace Tenweb_Builder\Widgets\Portfolio;

use Tenweb_Builder\Posts;
use Tenweb_Builder\Builder;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Portfolio
 */
class Portfolio extends Posts {

	/**
	 * @var \WP_Query
	 */
	private $_query = null;

	protected $_has_template_content = false;

	public function get_name() {
		return Builder::$prefix . '_portfolio';
	}

	public function get_title() {
		return esc_html__( 'Portfolio', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-portfolio twbb-widget-icon';
	}

    public function get_categories() {
        return [ 'tenweb-widgets' ];
    }

	public function get_keywords() {
		return [ 'posts', 'cpt', 'item', 'loop', 'query', 'portfolio', 'custom post type' ];
	}

	public function get_script_depends() {
		return [ 'imagesloaded' ];
	}

	public function on_import( $element ) {
		if ( isset( $element['settings']['posts_post_type'] ) && ! get_post_type_object( $element['settings']['posts_post_type'] ) ) {
			$element['settings']['posts_post_type'] = 'post';
		}

		return $element;
	}

	public function get_query() {
		return $this->_query;
	}

	protected function register_controls() {
		$this->register_query_section_controls();
	}

	protected function register_query_section_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => esc_html__( 'Columns', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'prefix_class' => 'elementor-grid%s-',
				'frontend_available' => true,
				'selectors' => [
					'.elementor-msie {{WRAPPER}} .elementor-portfolio-item' => 'width: calc( 100% / {{SIZE}} )',
				],
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Posts Per Page', 'tenweb-builder' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail_size',
				'exclude' => [ 'custom' ], //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude
				'default' => 'medium',
				'prefix_class' => 'elementor-portfolio--thumbnail-size-',
			]
		);

		$this->add_control(
			'masonry',
			[
				'label' => esc_html__( 'Masonry', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'tenweb-builder' ),
				'label_on' => esc_html__( 'On', 'tenweb-builder' ),
				'condition' => [
					'columns!' => '1',
				],
				'render_type' => 'ui',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'item_ratio',
			[
				'label' => esc_html__( 'Item Ratio', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.66,
				],
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__thumbnail__link' => 'padding-bottom: calc( {{SIZE}} * 100% )',
					'{{WRAPPER}}:after' => 'content: "{{SIZE}}"; position: absolute; color: transparent;',
				],
				'condition' => [
					'masonry' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => esc_html__( 'Show Title', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => esc_html__( 'Off', 'tenweb-builder' ),
				'label_on' => esc_html__( 'On', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'Title HTML Tag', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Query', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$query_controls_data = $this->get_query_controls();
		$query_controls_data['tx_options'] = '';
		$query_controls_data['pt_options']['manual'] = __('Manual Selection', 'tenweb-builder');

		$this->add_control(
			'post_types',
			[
				'label' => __('Source', 'tenweb-builder'),
				'type' => Controls_Manager::SELECT,
				'options' => $query_controls_data['pt_options'],
				'default' => 'post'
			]
		);

		$this->add_control(
			'post_author',
			[
				'label' => __('Author', 'tenweb-builder'),
				'type' => 'TWBBSelectAjax',
				'multiple' => true,
				'options' => [],
				'label_block' => true,
				'filter_by' => 'author',
				'condition' => [
					'post_types!' => 'manual'
				]
			]
		);

		$this->add_control(
			'manual_selected_posts',
			[
				'label' => __('Search & Select', 'tenweb-builder'),
				'type' => 'TWBBSelectAjax',
				'multiple' => true,
				'options' => [],
				'label_block' => true,
				'filter_by' => 'post',
				'condition' => [
					'post_types' => 'manual'
				]
			]
		);


		foreach($query_controls_data['tax_options'] as $pt => $taxs_options) {

			foreach($taxs_options as $tax => $tax_opt) {

				$this->add_control(
					'taxonomy_' . $pt . '_' . $tax,
					[
						'label' => $tax_opt['title'],
						'type' => Controls_Manager::SELECT2,
						'options' => $tax_opt['terms'],
						'multiple' => true,
						'label_block' => true,
						'condition' => [
							'post_types' => $pt,
						],
					]
				);

			}
		}

		$this->add_control(
			'advanced',
			[
				'label' => __('Advanced', 'tenweb-builder'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label' => __('Order By', 'tenweb-builder'),
				'type' => Controls_Manager::SELECT,
				'default' => 'post_date',
				'options' => [
					'post_date' => __('Date', 'tenweb-builder'),
					'post_title' => __('Title', 'tenweb-builder'),
					'rand' => __('Random', 'tenweb-builder'),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __('Order', 'tenweb-builder'),
				'type' => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc' => __('ASC', 'tenweb-builder'),
					'desc' => __('DESC', 'tenweb-builder'),
				],
			]
		);

		$this->add_control(
			'exclude_posts',
			[
				'label' => __('Exclude posts', 'tenweb-builder'),
				'type' => 'TWBBSelectAjax',
				'multiple' => true,
				'options' => [],
				'label_block' => true,
				'filter_by' => 'post',
				'condition' => [
					'post_types!' => 'manual'
				]
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'filter_bar',
			[
				'label' => esc_html__( 'Filter Bar', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_filter_bar',
			[
				'label' => esc_html__( 'Show', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'tenweb-builder' ),
				'label_on' => esc_html__( 'On', 'tenweb-builder' ),
			]
		);

		$this->add_control(
			'taxonomy',
			[
				'label' => esc_html__( 'Taxonomy', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => [],
				'options' => $this->get_taxonomies(),
				'condition' => [
					'show_filter_bar' => 'yes'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_layout',
			[
				'label' => esc_html__( 'Items', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		/*
		 * The `item_gap` control is replaced by `column_gap` and `row_gap` controls since v 2.1.6
		 * It is left (hidden) in the widget, to provide compatibility with older installs
		 */

		$this->add_control(
			'item_gap',
			[
				'label' => esc_html__( 'Item Gap', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}; --grid-column-gap: {{SIZE}}{{UNIT}};',
				],
				'frontend_available' => true,
				'classes' => 'elementor-hidden',
			]
		);

		$this->add_control(
			'column_gap',
			[
				'label' => esc_html__( 'Columns Gap', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => ' --grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'row_gap',
			[
				'label' => esc_html__( 'Rows Gap', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'tenweb-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-portfolio-item__img, {{WRAPPER}} .elementor-portfolio-item__overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_overlay',
			[
				'label' => esc_html__( 'Item Overlay', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color_background',
			[
				'label' => esc_html__( 'Background Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} a .elementor-portfolio-item__overlay' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'color_title',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'separator' => 'before',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a .elementor-portfolio-item__title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-portfolio-item__title',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_filter',
			[
				'label' => esc_html__( 'Filter Bar', 'tenweb-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_filter_bar' => 'yes',
				],
			]
		);

		$this->add_control(
			'color_filter',
			[
				'label' => esc_html__( 'Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-portfolio__filter' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'color_filter_active',
			[
				'label' => esc_html__( 'Active Color', 'tenweb-builder' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-portfolio__filter.elementor-active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_filter',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-portfolio__filter',
			]
		);

		$this->add_control(
			'filter_item_spacing',
			[
				'label' => esc_html__( 'Space Between', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-portfolio__filter:not(:last-child)' => 'margin-right: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-portfolio__filter:not(:first-child)' => 'margin-left: calc({{SIZE}}{{UNIT}}/2)',
				],
			]
		);

		$this->add_control(
			'filter_spacing',
			[
				'label' => esc_html__( 'Spacing', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-portfolio__filters' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function get_taxonomies() {
		$taxonomies = get_taxonomies( [ 'show_in_nav_menus' => true ], 'objects' );

		$options = [ '' => '' ];

		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		return $options;
	}

	protected function get_posts_tags() {
		$taxonomy = $this->get_settings( 'taxonomy' );

		foreach ( $this->_query->posts as $post ) {
			if ( ! $taxonomy ) {
				$post->tags = [];
				continue;
			}

			$tags = wp_get_post_terms( $post->ID, $taxonomy );
			$tags_slugs = [];

			foreach ( $tags as $tag ) {
				$tags_slugs[ $tag->term_id ] = $tag;
			}

			$post->tags = $tags_slugs;
		}
	}

	public function query_posts() {

		$query_args = $this->get_query_args();

		$query = new \WP_Query( $query_args );

		do_action( 'elementor/query/query_results', $query, $this );

		$this->_query = $query;
	}

	public function render() {
		$this->query_posts();

		$wp_query = $this->get_query();

		if ( ! count( $wp_query->posts ) ) {
			return;
		}

		$this->get_posts_tags();

		$this->render_loop_header();

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();

			$this->render_post();
		}

		$this->render_loop_footer();

		wp_reset_postdata();
	}

	protected function render_thumbnail() {
		$settings = $this->get_settings();

		$settings['thumbnail_size'] = [
			'id' => get_post_thumbnail_id(),
		];
		?>
		<div class="elementor-portfolio-item__img elementor-post__thumbnail">
			<?php Group_Control_Image_Size::print_attachment_image_html( $settings, 'thumbnail_size' ); ?>
		</div>
		<?php
	}

	protected function render_filter_menu() {
		$taxonomy = $this->get_settings( 'taxonomy' );

		if ( ! $taxonomy ) {
			return;
		}

		$terms = [];

		foreach ( $this->_query->posts as $post ) {
			$terms += $post->tags;
		}

		if ( empty( $terms ) ) {
			return;
		}

		usort( $terms, function( $a, $b ) {
			return strcmp( $a->name, $b->name );
		} );

		?>
		<ul class="elementor-portfolio__filters">
			<li class="elementor-portfolio__filter elementor-active" data-filter="__all"><?php echo esc_html__( 'All', 'tenweb-builder' ); ?></li>
			<?php foreach ( $terms as $term ) { ?>
				<li class="elementor-portfolio__filter" data-filter="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></li>
			<?php } ?>
		</ul>
		<?php
	}

	protected function render_title() {
		if ( ! $this->get_settings( 'show_title' ) ) {
			return;
		}

		$tag = $this->get_settings( 'title_tag' );
		?>
		<<?php Utils::print_validated_html_tag( $tag ); ?> class="elementor-portfolio-item__title">
		<?php the_title(); ?>
		</<?php Utils::print_validated_html_tag( $tag ); ?>>
		<?php
	}

	protected function render_post_header() {
		global $post;

		$tags_classes = array_map( function( $tag ) {
			return 'elementor-filter-' . $tag->term_id;
		}, $post->tags );

		$classes = [
			'elementor-portfolio-item',
			'elementor-post',
			implode( ' ', $tags_classes ),
		];

		// PHPCS - `get_permalink` is safe.
		?>
		<article <?php post_class( $classes ); ?>>
			<a class="elementor-post__thumbnail__link" href="<?php echo get_permalink(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
		<?php
	}

	protected function render_post_footer() {
		?>
		</a>
		</article>
		<?php
	}

	protected function render_overlay_header() {
		?>
		<div class="elementor-portfolio-item__overlay">
		<?php
	}

	protected function render_overlay_footer() {
		?>
		</div>
		<?php
	}

	protected function render_loop_header() {
		if ( $this->get_settings( 'show_filter_bar' ) ) {
			$this->render_filter_menu();
		}
		?>
		<div class="elementor-portfolio elementor-grid elementor-posts-container">
		<?php
	}

	protected function render_loop_footer() {
		?>
		</div>
		<?php
	}

	protected function render_post() {
		$this->render_post_header();
		$this->render_thumbnail();
		$this->render_overlay_header();
		$this->render_title();
		$this->render_overlay_footer();
		$this->render_post_footer();
	}

	public function render_plain_content() {}

	public function get_group_name() {
		return 'posts';
	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new Portfolio());
