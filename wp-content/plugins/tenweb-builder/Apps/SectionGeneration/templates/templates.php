<!--Section Generation header button template-->
<script type="text/template" id="twbb-sg-header-button-template">
    <span class="twbb-sg-header-button-container">
    <span class="twbb-sg-header-button"><span class="twbb_changed-elements-plus">+</span> Sections
        <img src="<?php echo esc_url( TWBB_URL . '/Apps/SectionGeneration/assets/images/sg_ai_logo.png' );?>">
    </span>
    <span class="twbb-sg-header-new"> <?php esc_html_e( 'New', 'tenweb-builder' ); ?> </span>
    </span>
</script>
<?php
$section_generation_types = \Tenweb_Builder\Apps\SectionGeneration::getInstance()->sectionGenerationTypes;
$section_generation_ecommerceSections_types = \Tenweb_Builder\Apps\SectionGeneration::getInstance()->ecommerceSections;
ksort($section_generation_types);
ksort($section_generation_ecommerceSections_types);
$all_sections = array_merge($section_generation_types, $section_generation_ecommerceSections_types);
?>
<!--Section Generation sidebar template-->
<script type="text/template" id="twbb-sg-sidebar-template">
    <div class="twbb-sg-sidebar">
        <div class="twbb-sg-sidebar-header">
            <span class="twbb-sg-sidebar-header-title"><?php esc_html_e( 'Sections', 'tenweb-builder' ); ?></span>
            <span class="twbb-sg-sidebar-header-close" onclick="twbb_animate_sidebar(0)"></span>
        </div>
        <div class="twbb-sg-sidebar-content">
            <?php if( empty($section_generation_types) ) { ?>
            <div class="twbb-sg-sidebar-empty-loading-content">
                <i class="twbb-sg-sidebar-empty-loading"></i>
                <span><?php esc_html_e( 'Loading...', 'tenweb-builder' ); ?></span>
            </div>
            <div class="twbb-sg-sidebar-empty-error-content" style="display:none">
                <i class="twbb-sg-sidebar-empty-info"></i>
                <span class="twbb-sg-sidebar-empty-info-title"><?php esc_html_e( 'Something went wrong during section activation', 'tenweb-builder' ); ?></span>
                <span class="twbb-sg-sidebar-empty-info-descr">
                    <?php
                    echo sprintf(__( 'Please contact %scustomer care%s for assistance.', 'tenweb-builder' ), '<a href="https://help.10web.io/hc/en-us/requests/new" target="_blank">','</a>');
                    ?>
                </span>
            </div>
            <?php } else { ?>
            <div class="twbb-sg-sidebar-navigator-menu">
                <ul class="twbb-sg-sidebar-navigator-menu-ul">
                    <li class="twbb-sg-sidebar-navigator-menu-li selected twbb-sg-navigation-item"
                        data-type="generate-with-ai">
                        <?php esc_html_e( 'AI Generator', 'tenweb-builder' ); ?>
                        <img src="<?php echo esc_url( TWBB_URL . '/Apps/SectionGeneration/assets/images/sg_ai_logo.png' );?>">
                    </li>
                    <?php foreach ( $section_generation_types as $key => $value ) {
                    foreach ( $value as $sg_type => $section ) {?>
                        <li class="twbb-sg-sidebar-navigator-menu-li twbb-sg-navigation-item" data-type="<?php echo esc_attr( $sg_type ); ?>" data-post_id="<?php echo esc_attr( $section['post_id'] ); ?>">
                            <?php echo esc_html__($section['title'], 'tenweb-builder'); ?>
                        </li>
                    <?php }
                    }
                    if( is_plugin_active('woocommerce/woocommerce.php') && !empty($section_generation_ecommerceSections_types)) { ?>
                    <li class="twbb-separator-sg-types"><?php echo esc_html__('For online stores', 'tenweb-builder'); ?></li>
                    <?php foreach ( $section_generation_ecommerceSections_types as $key => $value ) {
                        foreach ( $value as $sg_type => $section ) {?>
                        <li class="twbb-sg-sidebar-navigator-menu-li twbb-sg-navigation-item" data-type="<?php echo esc_attr( $sg_type ); ?>" data-post_id="<?php echo esc_attr( $section['post_id'] ); ?>">
                            <?php echo esc_html__($section['title'], 'tenweb-builder'); ?>
                        </li>
                    <?php }
                        }
                    }?>
                </ul>
            </div>
            <div class="twbb-sg-sidebar-navigated-contents-container twbb-sg-sidebar-navigated-contents-container-ai-generated">
                <div class="twbb-sg-sidebar-navigated-content-header-button twbb-sg-navigation-item twbb-header-button_types" data-type="generate-with-ai">
                    <?php esc_html_e( 'Generate New Section with AI', 'tenweb-builder' ); ?><img src="<?php echo esc_url( TWBB_URL . '/Apps/SectionGeneration/assets/images/sg_ai_logo.png' );?>">
                </div>

                <div class="twbb-sg-sidebar-navigated-content-description">
                    <?php esc_html_e( 'or choose a premade section', 'tenweb-builder' ); ?>
                </div>

                <?php ai_generated_navigated_content($all_sections); ?>
                    <div class="twbb-sg-sidebar-navigated-content" data-type="all">
                        <!-- Add an iframe to the page -->
                        <iframe id="twbb-sg-all-sections-iframe" src="about:blank"></iframe>
                    </div>
            </div>
            <?php } ?>
        </div>
    </div>
</script>

<!--Section Generation sidebar reload template-->
<script type="text/template" id="twbb-sg-sidebar-empty-reload-content-template">
    <div class="twbb-sg-sidebar-empty-reload-content">
        <i class="twbb-sg-sidebar-empty-info"></i>
        <span class="twbb-sg-sidebar-empty-info-title"><?php esc_html_e( 'Please save any unsaved changes and reload the page to use sections.', 'tenweb-builder' ); ?></span>
    </div>
</script>

<!--AI Section Generation overlay template-->
<script type="text/template" id="twbb-sg-sidebar-generated-with-ai_overlay-template">
    <div class="twbb-sg-sidebar-generated-with-ai_overlay">
        <div class="twbb-sg-loading-container">
            <div id="loading-progress">
                <div id="loading-progress-bar"></div>
            </div>
            <div class="twbb-sg-loading-text step-1"><?php esc_html_e( 'Creating structure..', 'tenweb-builder' ); ?></div>
            <div class="twbb-sg-loading-text step-2"><?php esc_html_e( 'Writing your texts..', 'tenweb-builder' ); ?></div>
            <div class="twbb-sg-loading-text step-3"><?php esc_html_e( 'Generating your images..', 'tenweb-builder' ); ?></div>
            <div class="twbb-sg-loading-text step-4"><?php esc_html_e( 'Putting together your section..', 'tenweb-builder' ); ?></div>
        </div>
    </div>
</script>

<script type="text/template" id="twbb-sg-iframe-lazy-load-template">
    <div class="twbb-sg-iframe-lazy-load-layer">
    </div>
    <div class="twbb-sg-iframe-lazy-load-container">
        <div class="twbb-sg-iframe-lazy-load"></div>
        <div class="twbb-sg-iframe-lazy-load"></div>
        <div class="twbb-sg-iframe-lazy-load"></div>
        <div class="twbb-sg-iframe-lazy-load"></div>
    </div>
</script>


<?php
function ai_generated_navigated_content($all_sections) { ?>
    <div class="twbb-sg-sidebar-navigated-content twbb-sg-sidebar-generated-with-ai selected" data-type="<?php echo esc_attr( 'generate-with-ai' ); ?>">
        <div class="twbb-generate-with-ai-general-description">
            <p class="twbb-generate-with-ai-general-description-title">
                <img src="<?php echo esc_url( TWBB_URL . '/Apps/SectionGeneration/assets/images/sg_ai_logo_white.png' );?>">
                <?php esc_html_e( 'Generate new sections with AI', 'tenweb-builder' ); ?>
            </p>
            <p class="twbb-generate-with-ai-general-description-text">
                <?php esc_html_e( 'Get a ready-to-use section with unique content by describing your needs.', 'tenweb-builder' ); ?>
            </p>
        </div>

        <div class="twbb-generate-with-ai-input">
            <p class="twbb-generate-with-ai-input-title">
                <?php esc_html_e( 'Choose section', 'tenweb-builder' ); ?>
            </p>
            <select id="twbb-select-generate-section_types">
              <option class="twbb-generate-section_type" value="">
	              <?php echo esc_html( 'Custom' ); ?>
              </option>
                <?php foreach ( $all_sections as $key => $value ) {
                foreach ( $value as $type => $section ) {
                    if( $type !== 'all' ) { ?>
                        <option class="twbb-generate-section_type" value="<?php echo esc_attr( $type ); ?>">
                            <?php echo esc_html__( $section['title'] ); ?>
                        </option>
                    <?php }
                    }
                }?>
            </select>
        </div>

        <div class="twbb-generate-with-ai-input">
            <p class="twbb-generate-with-ai-input-title">
                <?php esc_html_e( 'Describe your section', 'tenweb-builder' ); ?>
                <span class="twbb-sg-describe-info-container">
                    <img src="<?php echo esc_url( TWBB_URL . '/Apps/SectionGeneration/assets/images/sg_info_icon.png' );?>">
                    <span class="twbb-sg-describe-info">
                        <?php esc_html_e('Provide a detailed description of your website section\'s structure and content for the best results.');?>
                    </span>
                </span>
            </p>
            <?php $gen_sections_desc = 'Ex: Core event management features highlighted with icons and brief descriptions on a clean background.' ?>
            <div class="twbb-sg-description-content">
                <textarea class="twbb-generate-section_description" placeholder="<?php echo esc_attr($gen_sections_desc);?>" maxlength="1000" ></textarea>
            </div>
            <p class="char-limit-warning" style="display:none;color:red; padding: 5px; position: absolute;">Character limit exceeded. Maximum 1000 characters allowed.</p>
        </div>
        <?php
        $business_description = '';
        if( get_option('twbb_site_description') !== null ) {
            if ( isset(get_option( 'twbb_site_description' )['description']) ) {
                $business_description = get_option('twbb_site_description')['description'];
            }
        }
        $disabled_class = '';
        if ( empty($business_description) ) {
            $disabled_class = 'disabled';
        }
        $ai_generated_section_id = '';
        if( isset( $all_sections['ai-generated-sections'] ) && isset( $all_sections['ai-generated-sections']['id'] ) ) {
            $ai_generated_section_id = $all_sections['ai-generated-sections']['id'];
        }
        ?>
        <div class="twbb-generate-with-ai-input twbb-generate-with-ai-button-input">
            <div class="twbb-sg-sidebar-navigated-content-header-button twbb-sg-generate-with-ai-button <?php echo esc_attr($disabled_class);?>" data-post_id="<?php esc_attr_e( $ai_generated_section_id ); ?>">
                <?php esc_html_e( 'Generate with AI', 'tenweb-builder' ); ?><img src="<?php echo esc_url( TWBB_URL . '/Apps/SectionGeneration/assets/images/sg_ai_logo.png' );?>">
            </div>
        </div>

        <div class="twbb-generate-not-available">
            <img src="<?php echo esc_url( TWBB_URL . '/Apps/SectionGeneration/assets/images/notif.png' );?>">
            <span class="twbb-generate-error-text">
                <?php esc_html_e('Another generation is in progress.', 'tenweb-builder');?> <br>
                <?php esc_html_e('A new generation will be available upon completion.', 'tenweb-builder');?>
            </span>
        </div>

        <div class="twbb-ready-text">
            <?php esc_html_e('Your AI section is ready:','tenweb-builder');?>
        </div>
        <div class="twbb-generate-error">
            <img src="<?php echo esc_url( TWBB_URL . '/Apps/SectionGeneration/assets/images/notif.png' );?>">
            <span class="twbb-generate-error-text">
                <?php esc_html_e('Something went wrong during generation.', 'tenweb-builder');?> <br>
                <?php esc_html_e('Please try again.', 'tenweb-builder');?>
            </span>
        </div>
        <div class="twbb-generate-with-ai-iframes">
        </div>

    </div>
<?php } ?>
