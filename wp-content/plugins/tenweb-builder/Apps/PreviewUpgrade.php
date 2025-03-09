<?php
namespace Tenweb_Builder\Apps;

class PreviewUpgrade extends BaseApp {

    protected static $instance = null;

    public static function getInstance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct(){
        add_action( 'admin_bar_menu', [ $this, 'process' ], 500 );
    }

    public function process() {
        if( self::visibilityCheck() ) {
            $this->run();
        }
    }

    public function enqueueSidebar() {
        //TODO minify scripts and styles and add DEV check
        wp_enqueue_style( TWBB_PREFIX . '-preview-upgrade-style', TWBB_URL . '/Apps/PreviewUpgrade/assets/style/preview_upgrade.css', array(), TWBB_VERSION );
        wp_enqueue_script( TWBB_PREFIX . '-preview-upgrade-script', TWBB_URL . '/Apps/PreviewUpgrade/assets/script/preview_upgrade.js', [ 'jquery' ], TWBB_VERSION );

      $domain_id = get_option(TENWEB_PREFIX . '_domain_id');
      //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
      $ref = isset($_SERVER['REQUEST_URI']) ? sanitize_url($_SERVER['REQUEST_URI']) : '';
      //phpcs:ignore WordPress.WP.AlternativeFunctions.parse_url_parse_url
      $ref = parse_url($ref, PHP_URL_PATH);
      //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
      $upgrade_url = add_query_arg(array('showUpgradePopup' => 1, 'step' => 2, 'referrer' => urlencode($ref)),TENWEB_DASHBOARD . '/websites/'. $domain_id . '/ai-builder');
      wp_localize_script( TWBB_PREFIX . '-preview-upgrade-script', 'twbb_sidebar_vars', array(
        'nonce' => wp_create_nonce("twb_pu_nonce"),
        'ajax_url' => admin_url('admin-ajax.php'),
        'upgrade_url' => $upgrade_url,
        'remove_sidebar_url' => get_site_url() . '/?rest_route=/tenweb-builder/v1/remove_sidebar',
      ));
    }

    /**
     * Print the bottom bar.
     *
     * @return void
     */
    public function bottomBar() {
      ?>
      <div class="twbb-pu-bar twbb-pu-bottom-bar" style="display: none;">
        <div>
          <span><?php esc_html_e('Edit your website with 10Web editor based on Elementor.', 'twbb'); ?></span>
          <button class="twbb-pu-button twbb-button-blue"><?php esc_html_e('Edit', 'twbb'); ?></button>
        </div>
      </div>
      <?php
    }

    public function run() {
        $this->insertStylesScripts();
        $this->enqueueSidebar();
        $this->bottomBar();
        $this->upgradePopup();
    }

    private function insertStylesScripts() {
        //phpcs:disable
        ?>
        <script type="text/javascript">
            let url, new_url;
            url = window.location.href;
            new_url = url.split('?')[0];
            window.history.pushState({}, document.title, new_url);
        </script>
        <style>
            html {
                margin-top: 0 !important;
            }

            #wpadminbar {
                display: none;
            }
        </style>
        <?php
        //phpcs:enable
        update_option( 'twbb_sidebar', true );
    }

    private function upgradePopup() {
    $videos = array(
      array('title' => esc_html__('Drag & drop builder for effortless editing', 'twbb'), 'video_url' => 'https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/drag_drop.mp4', 'duration' => '17'),
      array('title' => esc_html__('Responsive design for any screen size', 'twbb'), 'video_url' => 'https://10-web-for-upload.s3.eu-central-1.amazonaws.com/builder-plugin-files/mobile_desktop.mp4', 'duration' => '12'),
    );

    ?>
    <div class="twbb-pu-upgrade-layout twbb-pu-hidden" style="display: none"></div>
    <?php
        if( ( class_exists('AIBuilderDemo') &&  \AIBuilderDemo::get_instance()->get_option('twbb_generated_templates_type') !== 'ecommerce' )
             || !class_exists('AIBuilderDemo') ) { ?>
    <div class="twbb-pu-upgrade-container twbb-pu-hidden" style="display: none">
      <span class="twbb-pu-upgrade-close"></span>
      <div class="twbb-pu-upgrade-left">
        <div class="twbb-pu-upgrade-left-header">
          <p class="twbb-pu-upgrade-title"><?php esc_html_e('Try AI Builder Pro for free for 7 days', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-descr"><?php esc_html_e('Edit your website, generate more content and images, and host a superfast website on 10Web. Own all the content and images you generate.', 'twbb'); ?></p>
          <a class="twbb-pu-upgrade-button twbb-pu-upgrade-button-mobile"><?php esc_html_e('Try 10Web Pro for 7 Days', 'twbb'); ?></a>
        </div>
          <p class="twbb-pu-upgrade-subtitle"><?php esc_html_e('AI Builder', 'twbb'); ?></p>
        <ul class="twbb-pu-upgrade-videos">
          <?php
          foreach ( $videos as $key => $video ) { ?>
            <li class="twbb-pu-video-item<?php echo $key === 0 ? ' twbb-pu-video-active' : ''; ?>" data-index="<?php echo esc_attr($key); ?>" data-video_url="<?php echo esc_url($video['video_url']) ?>" data-video_duration="<?php echo esc_attr($video['duration']) ?>">
              <span></span>
              <?php echo esc_html($video['title']); ?>
            </li>
          <?php } ?>
            <li class="twbb-pu-video-item-text">
                <span></span>
                <?php esc_html_e('Regenerate website content with AI', 'twbb') ?>
            </li>
            <li class="twbb-pu-video-item-text twbb-pu-item-text-domain">
                <span></span>
                <?php esc_html_e('Free custom domain up to $30', 'twbb') ?>
            </li>
        </ul>
        <div class="twbb-pu-info-container">
          <p class="twbb-pu-upgrade-subtitle"><?php esc_html_e('Unlock all of 10Web', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Get a reliable Google Cloud Partner hosting', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Get 90+ PageSpeed with 10Web Booster', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Enable real-time automated backups', 'twbb'); ?></p>


          <p class="twbb-pu-upgrade-subdescr twbb-pu-cancel-row"><b><?php esc_html_e('Cancel Anytime. ', 'twbb-pu'); ?></b><?php esc_html_e('We will send you a reminder email 24 hours before the end of the period.', 'twbb'); ?></p>
        </div>
        <a  class="twbb-pu-upgrade-button twbb-pu-upgrade-button-desktop"><?php esc_html_e('Try 10Web Pro for 7 Days', 'twbb'); ?></a>
      </div>
      <div class="twbb-pu-upgrade-right">
        <video width="740" height="600" muted>
          <source src="<?php echo esc_url($videos[0]['video_url']); ?>" type="video/mp4">
        </video>
        <div class="twbb-pu-info-container twbb-pu-info-container-mobile">
          <p class="twbb-pu-upgrade-subtitle"><?php esc_html_e('Unlock all of 10Web', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Get a reliable Google Cloud Partner hosting', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Get 90+ PageSpeed with 10Web Booster', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Enable real-time automated backups', 'twbb'); ?></p>

          <p class="twbb-pu-upgrade-subdescr twbb-pu-cancel-row"><b><?php esc_html_e('Cancel Anytime. ', 'twbb-pu'); ?></b><?php esc_html_e('We will send you a reminder email 24 hours before the end of the period.', 'twbb'); ?></p>
        </div>
      </div>
    </div>
    <?php } else { ?>
    <div class="twbb-pu-ecommerce twbb-pu-upgrade-container twbb-pu-hidden" style="display: none">
        <span class="twbb-pu-upgrade-close"></span>
        <div class="twbb-pu-upgrade-left">
            <div class="twbb-pu-left-icon"></div>
            <div class="twbb-pu-ecommerce-heading">
                <h1><?php esc_html_e('Explore 10Web Ecommerce for Free', 'twbb'); ?></h1>
            </div>
            <div class="twbb-pu-ecommerce-description">
                <p><?php esc_html_e('Personalize your online store, add your products, and start selling.', 'twbb'); ?></p>
            </div>
            <div class="twbb-pu-ecommerce-button">
                <a  class="twbb-pu-upgrade-button twbb-pu-upgrade-button-desktop"><?php esc_html_e('Try It Free for 7 Days', 'twbb'); ?></a>
            </div>
        </div>
        <div class="twbb-pu-upgrade-right">
            <div class="twbb-transparent-row"></div>
            <div class="twbb-pu-ecommerce-list">
                <p class="twbb-pu-ecommerce-list-heading">
                    <?php esc_html_e('Personalize your store', 'twbb');?>
                </p>
                <ul class="twbb-pu-ecommerce-ul">
                    <li class="twbb-pu-ecommerce-li-item">
                        <?php esc_html_e('Customize your store with a no-code AI drag and drop editor', 'twbb');?>
                    </li>
                    <li class="twbb-pu-ecommerce-li-item">
                        <?php esc_html_e('Connect a free custom domain', 'twbb');?>
                    </li>
                    <li class="twbb-pu-ecommerce-li-item">
                        <?php esc_html_e('Use a built-in design system to craft your brand', 'twbb');?>
                    </li>
                </ul>
            </div>
            <div class="twbb-pu-ecommerce-list">
                <p class="twbb-pu-ecommerce-list-heading">
                    <?php esc_html_e('Add unlimited products', 'twbb');?>
                </p>
                <ul class="twbb-pu-ecommerce-ul">
                    <li class="twbb-pu-ecommerce-li-item">
                        <?php esc_html_e('Generate product descriptions and images with AI', 'twbb');?>
                    </li>
                    <li class="twbb-pu-ecommerce-li-item">
                        <?php esc_html_e('Manage your inventory', 'twbb');?>
                    </li>
                    <li class="twbb-pu-ecommerce-li-item">
                        <?php esc_html_e('Create product variations', 'twbb');?>
                    </li>
                </ul>
            </div>
            <div class="twbb-pu-ecommerce-list">
                <p class="twbb-pu-ecommerce-list-heading">
                    <?php esc_html_e('Start selling', 'twbb');?>
                </p>
                <ul class="twbb-pu-ecommerce-ul">
                    <li class="twbb-pu-ecommerce-li-item">
                        <?php esc_html_e('Activate 10Web Payments', 'twbb');?>
                    </li>
                    <li class="twbb-pu-ecommerce-li-item">
                        <?php esc_html_e('Grow your sales with the high-conversion checkout', 'twbb');?>
                    </li>
                    <li class="twbb-pu-ecommerce-li-item">
                        <?php esc_html_e('Track your sales, taxes, and refunds', 'twbb');?>
                    </li>
                    <li class="twbb-pu-ecommerce-li-item">
                        <?php esc_html_e('Optimized website speed for higher conversions', 'twbb');?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php }
  }

    private static function visibilityCheck() {
      $edit_url = \Tenweb_Builder\Builder::get_edit_url();
        return !is_admin() &&
                ($edit_url &&
                1 === get_post_meta( get_the_ID(), 'twbb_ai_created', TRUE ) &&
                (isset( $_GET['preview_sidebar'] ) || get_option('twbb_sidebar'))) && //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            !class_exists('AIBuilderDemo');
    }
}