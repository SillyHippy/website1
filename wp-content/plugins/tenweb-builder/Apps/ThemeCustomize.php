<?php
namespace Tenweb_Builder\Apps;

class ThemeCustomize  extends BaseApp
{
    public $from_demo = FALSE;
    public $fonts = [];
    public $colors = [];
    public $active_font = 'Asap';
    public $active_color = 'han_blue';
    protected static $instance = null;
    public function __construct( $from_demo ) {
        $this->from_demo = $from_demo;

        $this->active_font = \Tenweb_Builder\Modules\ElementorKit\ElementorKit::getUltimateKitStyle('font_family');
        $this->active_color = \Tenweb_Builder\Modules\ElementorKit\ElementorKit::getUltimateKitStyle('color_pallet');
        $this->set_defaults();
        $this->register_script_style();
    }

    public function set_defaults() {
        $this->fonts = [
            "Arial",
            "Tahoma",
            "Verdana",
            "Helvetica",
            "Times New Roman",
            "Trebuchet MS",
            "Georgia",
            "Roboto",
            "Open Sans",
            "Montserrat",
            "Poppins",
            "Lato",
            "Noto Sans JP",
            "Inter",
            "Roboto Condensed",
            "Oswald",
            "Roboto Mono",
            "Noto Sans",
            "Raleway",
            "Ubuntu",
            "Rubik",
            "Nunito Sans",
            "Merriweather",
            "Playfair Display",
            "Roboto Slab",
            "Noto Sans KR",
            "Nunito",
            "PT Sans",
            "Work Sans",
            "Mulish",
            "Kanit",
            "Noto Sans TC",
            "Barlow",
            "Fira Sans",
            "Lora",
            "DM Sans",
            "Quicksand",
            "Titillium Web",
            "Nanum Gothic",
            "IBM Plex Sans",
            "Heebo",
            "Manrope",
            "PT Serif",
            "Inconsolata",
            "Libre Franklin",
            "Mukta",
            "Hind Siliguri",
            "Karla",
            "Noto Serif",
            "Bebas Neue",
            "Arimo",
            "Dosis",
            "Cabin",
            "Bitter",
            "Barlow Condensed",
            "Noto Color Emoji",
            "Abel",
            "Archivo",
            "Source Code Pro",
            "Jost",
            "Josefin Sans",
            "Hind Madurai",
            "Dancing Script",
            "Libre Baskerville",
            "Anton",
            "Material Symbols Rounded",
            "EB Garamond",
            "Cairo",
            "Oxygen",
            "Noto Sans SC",
            "PT Sans Narrow",
            "Hind",
            "Noto Serif JP",
            "Outfit",
            "Teko",
            "Noto Sans HK",
            "Pacifico",
            "Crimson Text",
            "Nanum Gothic Coding",
            "Public Sans",
            "Comfortaa",
            "Lobster",
            "Exo 2",
            "Space Grotesk",
            "Assistant",
            "Prompt",
            "Fjalla One",
            "Signika Negative",
            "Varela Round",
            "Figtree",
            "M PLUS Rounded 1c",
            "Overpass",
            "Chakra Petch",
            "Red Hat Display",
            "Arvo",
            "Rajdhani",
            "Maven Pro",
            "Source Sans 3",
            "Caveat",
            "IBM Plex Mono",
            "Play",
            "Slabo 27px",
            "Zilla Slab",
            "Cormorant Garamond",
            "Fira Sans Condensed",
            "Noto Sans Arabic",
            "Shadows Into Light",
            "Lexend",
            "Asap",
            "Barlow Semi Condensed",
            "Plus Jakarta Sans",
            "Abril Fatface",
            "Satisfy",
            "Archivo Black",
            "Lilita One",
            "Permanent Marker",
            "DM Serif Display",
            "Tajawal",
            "Indie Flower",
            "IBM Plex Serif",
            "Nanum Myeongjo",
            "Merriweather Sans",
            "Domine",
            "Questrial",
            "M PLUS 1p",
            "Rowdies",
            "Sora",
            "Yanone Kaffeesatz",
            "Signika",
            "Catamaran",
            "Crimson Pro",
            "IBM Plex Sans Arabic",
            "Urbanist",
            "Source Serif 4",
            "Roboto Flex",
            "Almarai",
            "Sarabun",
            "Noticia Text",
            "Archivo Narrow",
            "Noto Kufi Arabic",
            "Didact Gothic",
            "Acme",
            "Orbitron",
            "Vollkorn",
            "Cinzel",
            "Frank Ruhl Libre",
            "Montserrat Alternates",
            "Exo",
            "Russo One",
            "Bree Serif",
            "Alegreya Sans",
            "Amatic SC",
            "ABeeZee",
            "Marcellus",
            "Alegreya",
            "Kalam",
            "Be Vietnam Pro",
            "Tinos",
            "Spectral",
            "Zeyada",
            "Alfa Slab One",
            "Saira Condensed",
            "Mate",
            "Philosopher",
            "Great Vibes",
            "Encode Sans",
            "Chivo",
            "Cormorant",
            "Changa",
            "Lexend Deca",
            "Libre Caslon Text",
            "Khand",
            "Concert One",
            "Noto Serif KR",
            "Amiri",
            "Prata",
            "Noto Sans Thai",
            "Martel",
            "Courgette",
            "Old Standard TT",
            "Passion One",
            "Patua One",
            "Righteous",
            "Merienda",
            "Lobster Two",
            "Cardo",
            "Noto Sans Display",
            "PT Sans Caption",
            "Asap Condensed",
            "Ubuntu Condensed",
            "IBM Plex Sans Condensed",
            "Cantarell",
            "League Spartan",
            "Josefin Slab",
            "Sawarabi Mincho",
            "Space Mono",
            "Neuton",
            "Francois One",
            "Titan One",
            "Sacramento",
            "Noto Serif TC",
            "Alice",
            "Yantramanav",
            "Luckiest Guy",
            "Oleo Script",
            "Quattrocento Sans",
            "Crete Round",
            "Arsenal",
            "Paytone One",
            "Mate SC",
            "Kaushan Script",
            "Noto Sans Bengali",
            "Gloria Hallelujah",
            "Gothic A1",
            "Ubuntu Mono",
            "Bodoni Moda",
            "Saira",
            "Unbounded",
            "Readex Pro",
            "Alata",
            "Pathway Gothic One",
            "Rubik Mono One",
            "Yellowtail",
            "DM Serif Text",
            "El Messiri",
            "Quattrocento",
            "Gruppo",
            "Black Ops One",
            "Noto Sans Mono",
            "Architects Daughter",
            "Sawarabi Gothic",
            "Albert Sans",
            "Roboto Serif",
            "Zen Kaku Gothic New",
            "Eczar",
            "Inter Tight",
            "Noto Naskh Arabic",
            "Yeseva One",
            "Libre Barcode 39",
            "Sanchez",
            "Encode Sans Condensed",
            "Rokkitt",
            "Antic Slab",
            "Fraunces",
            "Cookie",
            "Commissioner",
            "Macondo",
            "Cuprum",
            "Poiret One",
            "Fira Sans Extra Condensed",
            "Special Elite",
            "Bungee",
            "Sen",
            "Unna",
            "Gelasio",
            "Literata",
            "Silkscreen",
            "Alegreya Sans SC",
            "Patrick Hand",
            "Allura",
            "Tangerine",
            "Bangers",
            "Creepster",
            "Advent Pro",
            "Aleo",
            "News Cycle",
            "Taviraj",
            "Staatliches",
            "Mitr",
            "DM Mono",
            "Tenor Sans",
            "Playfair Display SC",
            "Antonio",
            "Comic Neue",
            "Rubik Bubbles",
            "Kumbh Sans",
            "Red Hat Text",
            "JetBrains Mono",
            "Carter One",
            "Fugaz One",
            "Courier Prime",
            "Kosugi Maru",
            "Handlee",
            "STIX Two Text",
            "Secular One",
            "PT Mono",
            "Mukta Malar",
            "Amaranth",
            "Abhaya Libre",
            "Parisienne",
            "Adamina",
            "Yatra One",
            "Vidaloka",
        ];
        asort($this->fonts);
        $this->colors = \Tenweb_Builder\Modules\ElementorKit\ColorPallets::getColorPalletsForPreview();
    }

    private function register_script_style() {
        wp_enqueue_style(TWBB_PREFIX . '-theme-customize-style', TWBB_URL . '/Apps/ThemeCustomize/assets/style/theme-customize.css', array(), TWBB_VERSION);
        wp_enqueue_script(TWBB_PREFIX . '-theme-customize-script', TWBB_URL . '/Apps/ThemeCustomize/assets/script/theme-customize.js', ['jquery'], TWBB_VERSION);
        wp_localize_script( TWBB_PREFIX . '-theme-customize-script', 'twbb_theme_customize', array(
            'from_demo' => $this->from_demo,
            'fonts' => $this->fonts,
            'colors' => $this->colors,
            'active_font' => $this->active_font,
            'active_color' => $this->active_color,
            'typography_ids' => $this->get_typography_ids(),
            'ajaxnonce' => wp_create_nonce('wp_rest'),
            "rest_route" => get_rest_url(null, 'tenweb-builder/v1'),
        ));

    }

    public function html_template() {
        ob_start();
        if( !$this->from_demo ) { ?>
            <?php if( !\Tenweb_Builder\Modules\ElementorKit\ElementorKit::isUltimateKitActive() ) { ?>
            <script type='text/template' id='twbb-customize-activate-popup-template'>
                <div class='twbb-customize-enable-popup-layout' style="display:none"></div>
                <div class='twbb-customize-enable-popup' style="display:none">
                    <span class='twbb-customize-enable-popup-close'></span>
                    <span class='twbb-customize-enable-popup-title'><?php esc_html_e('Enable Section generation and Global style management', 'tenweb-builder'); ?></span>
                    <span class='twbb-customize-enable-popup-description'><?php esc_html_e('Activate Section generation and Global style management to easily edit and customize your website with AI.', 'tenweb-builder'); ?></span>
                    <span class='twbb-customize-enable-popup-info'><?php esc_html_e('Enabling these features will add new global settings without changing its current design.', 'tenweb-builder'); ?></span>
                    <div class='twbb-customize-enable-popup-button-row'>
                        <span class='twbb-customize-enable-popup-cancel-button twbb-customize-enable-popup-button'><?php esc_html_e('Cancel', 'tenweb-builder'); ?></span>
                        <span class='twbb-customize-enable-popup-activate-button twbb-customize-enable-popup-button'>
                            <span><?php esc_html_e('Activate Now', 'tenweb-builder'); ?></span>
                            <i></i>
                        </span>
                    </div>
                </div>
            </script>
            <!--Customize reload template-->
            <script type="text/template" id="twbb-customize-empty-reload-content-template">
                <div class="twbb-customize-empty-reload-content">
                    <i class="twbb-customize-empty-reload-info"></i>
                    <span class="twbb-customize-empty-reload-info-title"><?php esc_html_e( 'Please save any unsaved changes and reload the page to use global styles.', 'tenweb-builder' ); ?></span>
                </div>
            </script>
        <?php } ?>
            <script type="text/template" id="twbb-customize-preview-layout-template">
                <div class='twbb-customize-preview-layout'></div>
            </script>
            <script type="text/template" id="twbb-customize-template">
                <div class='twbb-customize-layout'>

        <?php } ?>
                <div class='twbb-theme-customize-container'>
                    <?php if( !$this->from_demo ) { ?>
                    <div class='twbb-theme-customize-header'>
                        <?php esc_html_e('Global styles', 'tenweb-builder'); ?>
                        <span class='twbb-theme-customize-close'></span>
                    </div>
                    <?php } ?>
            <div class='twbb-theme-customize-tabs'>
                <div id="twbb-theme-customize-font" class='twbb-theme-customize-tab'><?php esc_html_e('Font', 'tenweb-builder'); ?></div>
                <div id="twbb-theme-customize-color" class='twbb-theme-customize-tab twbb-theme-customize-tab-active'><?php esc_html_e('Color', 'tenweb-builder'); ?></div>
            </div>
            <div class='twbb-theme-customize-content'>
                <div class='twbb-theme-customize-tab-content twbb-theme-customize-font-content'  style="display:none">
                    <div class="twbb-font-search-row">
                        <span class="twbb-font-search-button"></span>
                        <input class="twbb-font-search-input" type="text" placeholder="Search fonts">
                    </div>
                    <div class="twbb-font-list">
                        <?php
                        foreach ( $this->fonts as $font ) {
                            if( $font === $this->active_font ) {
                                ?>
                                <div class="twbb-font-active"><span style="font-family: <?php echo esc_html($font); ?>"><?php echo esc_html($font); ?></span></div>
                                <?php
                            } else {
                                ?>
                                <div><span style="font-family: <?php echo esc_html($font); ?>"><?php echo esc_html($font); ?></span></div>
                                <?php
                            }
                        } ?>
                    </div>
                    <?php if( $this->from_demo ) { ?>
                    <div class="twbb-google-fonts-logo"></div>
                    <?php } ?>
                </div>
                <div class='twbb-theme-customize-tab-content twbb-theme-customize-color-content'>
                    <?php foreach ( $this->colors as $color ) { ?>
                    <div class="twbb-color-item<?php echo ($this->active_color === $color['id']) ? ' twbb-color-active' : ''; ?>" data-pallet_id="<?php esc_attr_e($color['id']); ?>">
                            <div class="twbb-color-cont">
                                <span class="twbb-color-circle" style="background-color: <?php echo esc_html($color['secondary_color']); ?>;"></span>
                                <span class="twbb-color-circle" style="background-color: <?php echo esc_html($color['accent_color']); ?>;"></span>
                            </div>
                            <span class="twbb-color-title"><?php echo esc_html($color['title']); ?></span>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class='twbb-theme-customize-footer'>
                <?php if( !$this->from_demo ) { ?>
                    <span class="twbb-theme-customize-save twbb-theme-customize-button-editor"><span><?php esc_html_e('Save changes', 'tenweb-builder'); ?></span><i></i></span>
                <?php } else { ?>
                <span class="twbb-theme-customize-save twbb-theme-customize-button-demo"><?php esc_html_e('Save', 'tenweb-builder');?></span>
                <?php } ?>
            </div>
        </div>
        <?php
        if( !$this->from_demo ) { ?>
                </div>
            </script>

            <script type="text/template" id="twbb-customize-save-popup-template">
                <div class='twbb-customize-save-popup-layout'></div>
                <div class="twbb-customize-save-popup">
                        <span class="twbb-customize-save-popup-close"></span>
                        <span class="twbb-customize-save-popup-title"><?php esc_html_e('Save global styles', 'tenweb-builder') ?></span>
                        <span class="twbb-customize-save-popup-description"><?php esc_html_e('Applying these changes will update the default global styles, affecting the website’s entire font and color scheme.', 'tenweb-builder') ?></span>
                        <span class="twbb-customize-save-popup-note"><?php esc_html_e('Any missing global styles will be automatically restored.', 'tenweb-builder') ?></span>
                        <div class="twbb-customize-save-popup-button-container">
                            <span class="twbb-customize-save-popup-button twbb-customize-cancel"><?php esc_html_e('Cancel', 'tenweb-builder') ?></span>
                            <span class="twbb-customize-save-popup-button twbb-customize-save"><?php esc_html_e('Save', 'tenweb-builder') ?></span>
                        </div>
                </div>
            </script>

        <?php
        }

        return ob_get_clean();
    }

    private function get_typography_ids(){
      $kit = \Elementor\Plugin::instance()->kits_manager->get_active_kit_for_frontend()->get_settings();
      $typographies = array_merge($kit['system_typography'], $kit['custom_typography']);
      $ids = [];
      foreach($typographies as $typography){
          $ids[] = $typography['_id'];
      }
      return $ids;
    }

    public static function getInstance(){
        if(self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}