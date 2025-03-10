<?php

namespace Tenweb_Builder\Apps;

class RemoveUpsell extends BaseApp
{

  protected static $instance = null;

  public static function getInstance(){
    if(self::$instance === null) {
        self::$instance = new self();
    }
    return self::$instance;
  }

  public function removeSubmenus(){
    /* remove Go Pro from Dashboard->Overview Footer */
    add_filter( 'elementor/admin/dashboard_overview_widget/footer_actions', function( $additions_actions ) {
      unset( $additions_actions['go-pro'] );
      unset( $additions_actions['find_an_expert'] );

      return $additions_actions;
    }, 550 );
  }

  public function removeGoProMenu() {
      remove_action( 'admin_menu', [ \Elementor\Plugin::instance()->settings, 'register_pro_menu' ], \Elementor\Settings::MENU_PRIORITY_GO_PRO );
  }

  /*
   * For Elementor up to 3.7
   */
  public function removeProMenus() {
    \Elementor\Plugin::instance()->admin_menu_manager->unregister('go_elementor_pro');
    \Elementor\Plugin::instance()->admin_menu_manager->unregister('e-form-submissions');
    \Elementor\Plugin::instance()->admin_menu_manager->unregister('elementor_custom_code');
    \Elementor\Plugin::instance()->admin_menu_manager->unregister('elementor_custom_fonts');
    \Elementor\Plugin::instance()->admin_menu_manager->unregister('elementor_custom_icons');
  }

  public function pluginActionLinks($links){
    unset($links['go_pro']);

    return $links;
  }

  /**
   * Remove Elementor Pro promotion widgets from list.
   *
   * @param $settings
   * @return mixed
   */
  public function localizeSettings( $settings ) {
    unset( $settings[ 'promotionWidgets' ] );

    return $settings;
  }

  public function removeAttributesSection($section, $section_id) {
    if( $section_id === 'section_custom_attributes_pro' ) {
      $section->remove_control('section_custom_attributes_pro');
    }
  }

  public function removeUpsellInPreview() { ?>
      <style>
          .e-ai-layout-button.elementor-add-section-area-button{ display:none !important;}
      </style>
   <?php
  }

  public function hideUpsellInAdmin() {
    if ( is_admin() ) {
      ?>
      <style>
        .elementor-role-row .elementor-role-go-pro,
        #menu-posts-elementor_library .elementor-app-link,
        .tenweb-editor .elementor-template-library-template-remote.elementor-template-library-pro-template,
        .elementor-control-type-text.elementor-control-address .elementor-control-dynamic-switcher.elementor-control-unit-1,
        .elementor-control-type-number .elementor-control-dynamic-switcher.elementor-control-unit-1,
        .elementor-control-type-gallery .elementor-control-dynamic-switcher.elementor-control-unit-1,
        .elementor-control-type-slider .elementor-control-dynamic-switcher.elementor-control-unit-1,
        .elementor-color-picker__header .elementor-control-dynamic-switcher.e-control-tool,
        .elementor-control-background_size_width_height ul li:nth-child(2),
        .elementor-control-background_size_width_height ul li:nth-child(3),
        .elementor-control-background_size_width_height ul li:nth-child(5),
        .elementor-control-background_size_width_height.elementor-control-type-dimensions label.elementor-control-dimension-label,
        .elementor-control-background_size_width_height_tablet ul li:nth-child(2),
        .elementor-control-background_size_width_height_tablet ul li:nth-child(3),
        .elementor-control-background_size_width_height_tablet ul li:nth-child(5),
        .elementor-control-background_size_width_height_tablet.elementor-control-type-dimensions label.elementor-control-dimension-label,
        .elementor-control-background_size_width_height_mobile ul li:nth-child(2),
        .elementor-control-background_size_width_height_mobile ul li:nth-child(3),
        .elementor-control-background_size_width_height_mobile ul li:nth-child(5),
        .elementor-control-background_size_width_height_mobile.elementor-control-type-dimensions label.elementor-control-dimension-label,
        .tenweb-editor #e-notice-bar,
        #elementor-panel-get-pro-elements, #elementor-notice-bar,
        .elementor-control-media__warnings, .elementor-control-media__promotions,
        .elementor-nerd-box.elementor-nerd-box--upsale, #elementor-navigator__footer__promotion,
        #elementor-panel-get-pro-elements-sticky, .elementor-context-menu-list__item-open_notes,
        .elementor-context-menu-list__item.elementor-context-menu-list__item-ai,
        #e-admin-top-bar-root .e-admin-top-bar__secondary-area,
        .components-panel__body .wp-list-table.widefat.fixed.striped.table-view-list thead tr th:last-child,
        .components-panel__body .wp-list-table.widefat.fixed.striped.table-view-list tbody tr td:last-child,
        .components-panel__body .components-panel__row:nth-last-child(1), .components-panel__body .components-panel__row:nth-last-child(2),
        .elementor-control-scrolling_effects_pro,
        .elementor-control-mouse_effects_pro,
        .elementor-control-sticky_pro,
        .elementor-control-display_conditions_pro,
        #e-announcements-root, .e-ai-button:not(.twb-ai-button),
        #e-checklist,
        button.MuiButtonBase-root[aria-label="Checklist"],
        .MuiToolbar-root .MuiBox-root .MuiGrid-root:nth-child(3) .MuiStack-root .MuiBox-root:nth-child(3) button,
        div.MuiDrawer-root.MuiDrawer-modal[role="presentation"],
        button.MuiButtonBase-root[aria-label="What's New"] {
            display: none !important;
        }
      </style>
      <script>
        jQuery( window ).on( 'elementor:init', function () {
          /* Adding class to hide pro templates only if ElementorPro is not active.
           * had to do so as in Elementor version after 2.6.8 js events are not working
           * TODO: find a better solution. */
          jQuery( 'body' ).addClass( 'tenweb-editor' );

          /* Hook into templates show function. */
          var showTemplates = elementor.templates.showTemplates;
          elementor.templates.showTemplates = function () {
            elementor.templates.loadTemplates();
            tenweb_remove_pro_templates();
            showTemplates();
          }
          /* Remove 'Theme Builder' menu form sidebar & 'View Page' open in new tab */
          jQuery( document ).on( 'click', '#elementor-panel-header-menu-button', function () {
            jQuery( '.elementor-panel-menu-item.elementor-panel-menu-item-site-editor' ).remove();
            jQuery( '.elementor-panel-menu-item.elementor-panel-menu-item-view-page a' ).attr('target','_blank');
          } );
        } );

        /* Hide Pro and Expert kits from list. */
        jQuery( 'body' ).on( 'DOMSubtreeModified', '.eps-app__content.e-kit-library__index-layout-main', function() {
          jQuery( '.e-kit-library__kit-item-subscription-plan-badge' ).each( function () {
            jQuery( this ).parents( '.e-kit-library__kit-item' ).hide();
          } );
        } );

        /* Remove pro templates from template library. */
        function tenweb_remove_pro_templates() {
          if ( elementor.templates.getTemplatesCollection() ) {
            var arraha = false;
            elementor.templates.getTemplatesCollection().each( function (model) {
              if (model && model.get('isPro')) {
                elementor.templates.getTemplatesCollection().remove(model);
                arraha = true;
              }
            } );
            if ( arraha ) {
              tenweb_remove_pro_templates();
            }
          }
        }
      </script>
      <?php
    }
  }

  public function hideUpsellInFront(){
    ?>
    <style>
      /*custom css*/
      .elementor-control.elementor-control-section_custom_css_pro {
          display: none;
      }
      .MuiButtonBase-root[aria-label="What's New"] {
          display: none;
      }
    </style>
    <?php
  }

  private function __construct() {
    $this->process();
  }

  private static function visibilityCheck(){
      return !defined( 'ELEMENTOR_PRO_VERSION' );
  }

  private function process() {
      if( self::visibilityCheck() ) {
            $this->run();
      }
  }

  private function run() {
      add_action( 'admin_menu', [ $this, 'removeGoProMenu' ], 0 );
      add_action( 'admin_menu', [ $this, 'removeSubmenus' ], 99999999999 );
      add_filter( 'plugin_action_links_' . ELEMENTOR_PLUGIN_BASE, [ $this, 'pluginActionLinks' ], 99999999999 );
      // Using 'wp_print_footer_scripts' to have this code in Kit Library as well.
      add_action( 'wp_print_footer_scripts', [ $this, 'hideUpsellInAdmin' ] );
      add_action( 'admin_footer', [ $this, 'hideUpsellInAdmin' ] );
      add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'hideUpsellInFront' ] );
      add_filter( 'elementor/editor/localize_settings', array( $this, 'localizeSettings' ) );
      add_action( 'elementor/element/before_section_end', array( $this, 'removeAttributesSection' ), 10, 3 );
      add_action( 'elementor/preview/enqueue_styles', array($this, 'removeUpsellInPreview'));
      update_option( 'elementor_allow_tracking', 'no' );
      update_option( 'elementor_tracker_notice', '1' );
      add_action( 'elementor/admin/menu/register', array($this,'removeProMenus'), 99999999999 );
      // Hide Elementor AI. Returning null as Elementor handles the false value as default and fallback to true.
      add_filter( 'get_user_option_elementor_enable_ai', '__return_null' );
  }
}
