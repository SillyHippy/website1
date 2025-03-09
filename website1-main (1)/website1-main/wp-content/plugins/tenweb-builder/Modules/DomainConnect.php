<?php
namespace Tenweb_Builder\Modules;

class DomainConnect {

    public $fromDemo = FALSE;
    public $twbbDomainNameSuggestion = FALSE;

    public function __construct( $fromDemo, $twbbDomainNameSuggestion ) {
        $this->fromDemo = $fromDemo;
        $this->twbbDomainNameSuggestion = $twbbDomainNameSuggestion;
        $this->htmlTemplate();
        if( $this->twbbDomainNameSuggestion ) {
            $this->registerScriptStyle();
        }
    }

    private function registerScriptStyle() {
        wp_enqueue_style(TWBB_PREFIX . '-domain-connect-style', TWBB_URL . '/Apps/PreviewUpgrade/assets/style/domain_connect.css', array(), TWBB_VERSION);
        wp_enqueue_script(TWBB_PREFIX . '-domain-connect-script', TWBB_URL . '/Apps/PreviewUpgrade/assets/script/domain_connect.js', ['jquery'], TWBB_VERSION);
    }

    private function htmlTemplate() {
        /* Domain top bar template */
        $domain_id = get_site_option('tenweb_domain_id');
        $dashboard_url = TENWEB_DASHBOARD . '/websites/'. $domain_id . '/domains/?domain=';
        if( !$this->fromDemo ) {
            $this->twbbDomainNameSuggestion = get_option('twbb_domain_name_suggestion');
        }

        $is_custom_domain = true;
        $domain_name = '';
        $connect_title = esc_html__( 'Get free custom domain', 'tenweb-builder' );
        $description = '';

        if( $this->twbbDomainNameSuggestion ) {
            $twbbDomainNameSuggestion = json_decode($this->twbbDomainNameSuggestion, 1);
            $want_domain = isset($twbbDomainNameSuggestion['want_domain']) ? intval($twbbDomainNameSuggestion['want_domain']) : 0;
            if( !$want_domain ) {
                return;
            }
            if( !empty($twbbDomainNameSuggestion['domain_name']) ) {
                $domain_name = esc_html($twbbDomainNameSuggestion['domain_name']);
                $dashboard_url .= $domain_name;
                $connect_title = esc_html__( 'Connect your domain', 'tenweb-builder' );
                $description = esc_html__( 'is avalable.', 'tenweb-builder' );
                $is_custom_domain = false;
            }
        } else {
            return;
        }


        if( $domain_id ) {
            $dashboard_url .= '&domain_id='.intval($domain_id);
        }


        ?>
        <script type="text/template" id="twbb-topbar-domain-template">
            <div class='twbb-topbar-domain twbb-custom-topbars'>
                <img class='twbb-topbar-domain-icon' src="<?php echo esc_url( TWBB_URL ); ?>/Apps/PreviewUpgrade/assets/images/domain_white.svg">
                <?php
                if ( $domain_name !== '' ) {
                    ?>
                    <span class="twbb-topbar-subdomain-title"><?php echo esc_html( $domain_name ); ?></span>
                    <?php
                }
                if ( !$is_custom_domain ) {
                    ?>
                    <span class="twbb-topbar-subdomain-descr"><?php echo esc_html( $description ); ?></span>
                    <?php
                }
                if ( $this->fromDemo ) { ?>
                    <span class="twbb-topbar-domain-connect twbb-topbar-domain-connect-demo"><?php echo esc_html( $connect_title ); ?></span>
                <?php
                } else {
                ?>
                    <a class="twbb-topbar-domain-connect" target="_blank" href="<?php echo esc_url($dashboard_url);?>"><?php echo esc_html( $connect_title ); ?></a>
                <?php } ?>
            </div>
        </script>
        <?php
    }
}
