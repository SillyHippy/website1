<?php
/*
	 * Add Front Banners
	 */

namespace Tenweb_Builder;
class Banners {
	public $trial_user;

	public function __construct() {
		$this->trial_user = $this->check_user();
		if ( TRUE ) {
			add_action( 'wp_footer', array( $this, 'builder_bottom_banner' ), 12 );
		}
	}

	public function check_user() {
		$if_trial_user = FALSE;
		if ( class_exists( '\Tenweb_Manager\Manager' ) ) {
			$user_agreements_info = \Tenweb_Manager\Helper::get_tenweb_user_info()[ 'agreement_info' ];
			$if_trial_user        = ( $user_agreements_info[ 'subscription_category' ] === 'starter' && $user_agreements_info[ 'hosting_trial_expire_date' ] !== '' ) ? TRUE : FALSE;
		}

		return $if_trial_user;
	}

	public function builder_bottom_banner() {
		wp_enqueue_style( 'twbb-builder-bottom-banner-style', TWBB_URL . '/banners/assets/style/builder-bottom-banner.css', array(), TWBB_VERSION );
		wp_enqueue_script( 'twbb-builder-bottom-banner-script', TWBB_URL . '/banners/assets/script/builder-bottom-banner.js', array( 'jquery' ), TWBB_VERSION ); ?>

      <div class="builder-bottom-banner">
        <div class="builder-bottom-banner-container">
          <div class="builder-bottom-banner_text">Explore All 10Web Premium Features,<br> Use Drag & Drop Editor Based
            on Elementor.
          </div>
            <?php  $server_name = isset( $_SERVER[ 'SERVER_NAME' ] ) ? sanitize_text_field($_SERVER[ 'SERVER_NAME' ]) : '';?>
          <div class="builder-bottom-banner_button"><a id="builder-bottom-banner" class="twbb_button-blue"
                                                       href='<?php echo esc_url(TENWEB_DASHBOARD . '/websites/?from_builder_free_offer=https://' .$server_name); ?>'>GET
              IT FREE</a></div>
        </div>
      </div>
		<?php
		delete_option( 'free_plan_preview' );
	}
}