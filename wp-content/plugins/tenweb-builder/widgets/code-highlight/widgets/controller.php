<?php
namespace Tenweb_Builder\Widgets\CodeHighlight\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Tenweb_Builder\Builder;
use Tenweb_Builder\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Code_Highlight extends Widget_Base {

	public function get_name() {
		return Builder::$prefix . '_code-highlight';
	}

	public function get_title() {
		return esc_html__( 'Code Highlight', 'tenweb-builder' );
	}

	public function get_icon() {
		return 'twbb-widget-icon twbb-code-highlight';
	}

    public function get_categories() {
        return [ 'tenweb-widgets' ];
    }

	public function get_keywords() {
		return [ 'code', 'highlight', 'syntax', 'highlighter', 'javascript', 'css', 'php', 'html', 'java', 'js' ];
	}

	public function get_style_depends() {
		return [ 'prismjs_style' ];
	}

	public function get_script_depends() {
		$post = get_post();
		$depends = [
			'prismjs_core' => true,
			'prismjs_loader' => true,
			'prismjs_normalize' => true,
			'highlight_handler' => true,
			'prismjs_line_numbers' => true,
			'prismjs_line_highlight' => true,
			'prismjs_copy_to_clipboard' => true,
		];

		if ( ! \Elementor\Plugin::instance()->preview->is_preview_mode($post->ID) ) {
			$settings = $this->get_settings_for_display();

			if ( ! $settings['line_numbers'] ) {
				unset( $depends['prismjs_line_numbers'] );
			}

			if ( ! $settings['highlight_lines'] ) {
				unset( $depends['prismjs_line_highlight'] );
			}

			if ( ! $settings['copy_to_clipboard'] ) {
				unset( $depends['prismjs_copy_to_clipboard'] );
			}
		}

		return array_keys( $depends );
	}

	public function get_css_config() {
		// This widget is loading its own CSS using get_style_depends.
		return [
			'key' => $this->get_group_name(),
			'version' => TWBB_VERSION,
			'file_path' => '',
			'data' => [],
		];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Code Highlight', 'tenweb-builder' ),
			]
		);

		$language_option = [
			'markup' => 'Markup',
			'html' => 'HTML',
			'css' => 'CSS',
			'sass' => 'Sass (Sass)',
			'scss' => 'Sass (Scss)',
			'less' => 'Less',
			'javascript' => 'JavaScript',
			'typescript' => 'TypeScript',
			'jsx' => 'React JSX',
			'tsx' => 'React TSX',
			'php' => 'PHP',
			'ruby' => 'Ruby',
			'json' => 'JSON + Web App Manifest',
			'http' => 'HTTP',
			'xml' => 'XML',
			'svg' => 'SVG',
			'rust' => 'Rust',
			'csharp' => 'C#',
			'dart' => 'Dart',
			'git' => 'Git',
			'java' => 'Java',
			'sql' => 'SQL',
			'go' => 'Go',
			'kotlin' => 'Kotlin + Kotlin Script',
			'julia' => 'Julia',
			'python' => 'Python',
			'swift' => 'Swift',
			'bash' => 'Bash + Shell',
			'scala' => 'Scala',
			'haskell' => 'Haskell',
			'perl' => 'Perl',
			'objectivec' => 'Objective-C',
			'visual-basic,' => 'Visual Basic + VBA',
			'r' => 'R',
			'c' => 'C',
			'cpp' => 'C++',
			'aspnet' => 'ASP.NET (C#)',
		];

		$this->add_control(
			'language',
			[
				'label' => esc_html__( 'Language', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => apply_filters( 'tenweb-builder/code_highlight/languages', $language_option ),
				'default' => 'javascript',
			]
		);

		$this->add_control(
			'code',
			[
				'label' => esc_html__( 'Code', 'tenweb-builder' ),
				'type' => Controls_Manager::CODE,
				'default' => 'console.log( \'Code is Poetry\' );',
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::TEXT_CATEGORY,
					],
				],
			]
		);

		$this->add_control(
			'line_numbers',
			[
				'label' => esc_html__( 'Line Numbers', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'line-numbers',
				'default' => 'line-numbers',
			]
		);

		$this->add_control(
			'copy_to_clipboard',
			[
				'label' => esc_html__( 'Copy to Clipboard', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'tenweb-builder' ),
				'label_off' => esc_html__( 'Off', 'tenweb-builder' ),
				'return_value' => 'copy-to-clipboard',
				'default' => 'copy-to-clipboard',
			]
		);

		$this->add_control(
			'highlight_lines',
			[
				'label' => esc_html__( 'Highlight Lines', 'tenweb-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => '1, 3-6',
			]
		);

		$this->add_control(
			'word_wrap',
			[
				'label' => esc_html__( 'Word Wrap', 'tenweb-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'tenweb-builder' ),
				'label_off' => esc_html__( 'Off', 'tenweb-builder' ),
				'return_value' => 'word-wrap',
				'default' => '',
			]
		);

		$this->add_control(
			'theme',
			[
				'label' => esc_html__( 'Theme', 'tenweb-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => 'Solid',
					'dark' => 'Dark',
					'okaidia' => 'Okaidia',
					'solarizedlight' => 'Solarizedlight',
					'tomorrow' => 'Tomorrow',
					'twilight' => 'Twilight',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => esc_html__( 'Height', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh', 'em' ],
				'range' => [
					'px' => [
						'min' => 115,
						'max' => 1000,
					],
					'em' => [
						'min' => 6,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .highlight-height' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'font_size',
			[
				'label' => esc_html__( 'Font Size', 'tenweb-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
					'vw' => [
						'min' => 0.1,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'responsive' => ['max' => 'desktop'],
				'selectors' => [
					'{{WRAPPER}} pre, {{WRAPPER}} code, {{WRAPPER}} .line-numbers .line-numbers-rows' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="<?php echo 'prismjs-' . esc_attr( $settings['theme'] ); ?> <?php echo esc_attr( $settings['copy_to_clipboard'] ); ?> <?php echo esc_attr( $settings['word_wrap'] ); ?>">
			<pre data-line="<?php echo esc_attr( $settings['highlight_lines'] ); ?>" class="highlight-height language-<?php echo esc_attr( $settings['language'] ); ?> <?php echo esc_attr( $settings['line_numbers'] ); ?>">
				<code readonly="true" class="language-<?php echo esc_attr( $settings['language'] ); ?>">
					<xmp><?php $this->print_unescaped_setting( 'code' ); ?></xmp>
				</code>
			</pre>
		</div>
		<?php
	}
// phpcs:disable
	protected function content_template() {
		?>
		<div class="prismjs-{{{ settings.theme }}} {{{settings.copy_to_clipboard}}} {{{settings.word_wrap}}}">
			<pre data-line="{{{settings.highlight_lines }}}" class="highlight-height language-{{{ settings.language }}} {{{ settings.line_numbers }}}">
				<code readonly="true" class="language-{{{ settings.language }}}">
					<xmp>{{{ settings.code }}}</xmp>
				</code>
			</pre>
		</div>
		<?php
	}
    // phpcs:enable
}

\Elementor\Plugin::instance()->widgets_manager->register( new Code_Highlight() );
