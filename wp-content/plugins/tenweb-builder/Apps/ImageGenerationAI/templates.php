<!--Empty popup container template-->
<script type="text/template" id="twbb-image-gen-template">
    <div class="twbb-image-gen-layout"></div>
    <div class="twbb-image-gen-container">
        <div class="twbb-image-gen-topbar">
            <div class="twbb-image-gen-topbar-logo">
                <img src="<?php echo esc_url( TWBB_URL . '/Apps/ImageGenerationAI/assets/images/10Web_logo.svg');?>">
                <hr>
            </div>
            <div class="twbb-image-gen-topbar-title"><?php esc_html_e('New Image','tenweb-builder') ?></div>
            <div class="twbb-image-gen-topbar-action">
                <div class="twbb-image-gen-topbar-undo-container">
                </div>
                <div class="twbb-image-gen-use_image twbb-generate_imag-button-disabled">
                    <span><?php esc_html_e('Use Image','tenweb-builder') ?></span>
                    <span class="twbb-generate_imag-button-loader"></span>
                </div>
            </div>
            <span class="twbb-close-image-gen-popup"></span>
        </div>
        <div class="twbb-image-gen-content">
            <div class="twbb-image-gen-menu">
                <span class="twbb-menu-item twbb-menu-item-add twbb-menu-item-active" data-action="new_image_view">
                    <span class="twbb-icon-tooltip"><?php esc_html_e('New image','tenweb-builder') ?></span>
                </span>
                <span class="twbb-menu-item twbb-menu-item-edit" data-action="edit_image_view">
                    <span class="twbb-icon-tooltip"><?php esc_html_e('Edit image','tenweb-builder') ?></span>
                </span>
                <span class="twbb-menu-item twbb-menu-item-multiview" data-action="multiple_view">
                    <span class="twbb-icon-tooltip"><?php esc_html_e('Multiple views','tenweb-builder') ?></span>
                </span>
            </div>
            <div class="twbb-image-gen-editor">
            </div>
            <div class="twbb-image-gen-preview">
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="twbb-image-gen-topbar-credits-template">
    <div class="twbb-image-gen-credits-container">
                    <span class="twbb-image-gen-credits">
                        <?php esc_html_e('Available Credits','tenweb-builder') ?>
                        <img src="<?php echo esc_url(TWBB_URL . '/Apps/ImageGenerationAI/assets/images/icon_diamond.svg');?>">
                        <span class="twbb-image-gen-credits-amount"><?php echo intval($this->available_credits) ?></span>
                    </span>
        <div class="twbb-image-gen-credits-layer">
            <div class="twbb-image-gen-credits-content">
                <p class="twbb-image-gen-credits-title"><?php esc_html_e('Available credits','tenweb-builder') ?></p>
                <div class="twbb-image-gen-credits-points-row">
                    <span class="twbb-image-gen-credit-amount"><?php echo intval($this->available_credits) ?></span>
                    <span class="twbb-image-gen-credits-total">/<?php echo intval($this->images_plan_limit / 1000); ?>K</span>
                    <div class="twbb-image-gen-credits-about">
                        ~
                        <span class="twbb-image-gen-credits-image_count"><?php echo intval($this->available_credits / 2); ?></span>
                        <span class="twbb-image-gen-credits-text"><?php esc_html_e('images','tenweb-builder') ?></span>
                    </div>
                </div>
                <div class="twbb-image-gen-credits-description-row">
                    <?php esc_html_e('~2 credits to generate one image and from 2 to 24 credits to expand an image','tenweb-builder') ?>
                </div>
                <div class="twbb-image-gen-credits-upgrade-row">
                    <div class="twbb-image-gen-reset-date">Reset date:<br> <?php echo esc_html($this->resetDate); ?></div>
                    <a href="#" target="_blank" class="twbb-image-gen-credits-upgrade-button"><?php esc_html_e('Upgrade','tenweb-builder') ?></a>
                </div>
            </div>
        </div>
    </div>
</script>

<!--New image Editor template-->
<script type="text/template" id="twbb-image-new_image-editor-template">
    <div class="twbb-image-editor-row twbb-new_image-editor-image_description">
        <label>
            <?php echo esc_html__( 'Describe the image', 'tenweb-builder' ); ?>
            <div class="twbb-help-tooltip"></div>
            <p class="twbb-help-tooltip-content" style="display:none">
                <?php echo esc_html__( 'The image prompt guides the generation process to include specific elements based on the provided text.', 'tenweb-builder' ); ?>
            </p>
        </label>
        <textarea class="twbb-image-description" placeholder="<?php echo esc_attr__( 'Ex: Chocolate cake with rich, fudgy frosting and perfectly layered cake, garnished with fresh berries and drizzled with melted chocolate.', 'tenweb-builder' ); ?>"></textarea>
        <p class="twbb-image-error-text"></p>
    </div>
    <div class="twbb-image-editor-row twbb-new_image-editor-image_style">
        <label>
            <?php echo esc_html__( 'Choose image style', 'tenweb-builder' ); ?>
            <div class="twbb-help-tooltip"></div>
            <p class="twbb-help-tooltip-content" style="display:none">
                <?php echo esc_html__( 'Image styles allow users to generate images using a specific style.', 'tenweb-builder' ); ?>
            </p>
        </label>
        <div class="twbb-select twbb-select-right_open">
            <span class="twbb-select-value">
                <span><?php echo esc_html__( 'None', 'tenweb-builder' ); ?></span>
                <i class="twbb-select-arrow"></i>
            </span>
            <div class="twbb-select-dropdown twbb-select-dropdown-image_style">
                <div  class="twbb-image_style-title-row">
                    <p class="twbb-image_style-title"><?php echo esc_html__( 'Image Style', 'tenweb-builder' ); ?></p>
                    <span class="twbb-image_style-close"></span>
                </div>
                <ul class="twbb-select-dropdown-image_style-content">
                    <?php
                    foreach ( $this->image_styles as $img_style ) {
                        ?>
                        <li data-value="<?php echo esc_attr($img_style['value']);?>">
                        <span class="twbb-image-menu-preview">
                            <?php if($img_style['img'] !== "") { ?>
                                <img src="<?php echo esc_url(TWBB_URL  .  '/Apps/ImageGenerationAI/assets/images/image_styles/' . $img_style['img']); ?>">
                            <?php } else {
                                echo esc_html__('No style');
                            } ?>
                        </span>
                            <p class="twbb-image-menu-preview-title"><?php echo esc_html($img_style['title']); ?></p>
                        </li>
                        <?php
                    } ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="twbb-image-editor-row twbb-new_image-editor-image_ratio">
        <label>
            <?php echo esc_html__( 'Select ratio', 'tenweb-builder' ); ?>
            <div class="twbb-help-tooltip"></div>
            <p class="twbb-help-tooltip-content" style="display:none">
                <?php echo esc_html__( 'Aspect ratios determine the dimensions of the generated result, with the square (1:1) ratio being selected by default.', 'tenweb-builder' ); ?>
            </p>
        </label>
        <div class="twbb-select">
            <span class="twbb-select-value" data-value="Square (1:1)">
                <span><?php echo esc_html__( 'Square (1:1)', 'tenweb-builder' ); ?></span>
                <i class="twbb-select-arrow"></i>
            </span>
            <ul class="twbb-select-dropdown">
                <?php
                foreach ( $this->image_ratio as $img_ratio ) {
                    $class = '';
                    if( $img_ratio === 'Square (1:1)' ) {
                        $class = 'twbb-select-active';
                    }
                    ?>
                    <li data-value="<?php echo esc_attr($img_ratio); ?>" class="<?php echo esc_attr($class); ?>"><?php echo esc_html($img_ratio); ?></li>
                <?php
                } ?>
            </ul>
        </div>
    </div>
    <div class="twbb-image-editor-row twbb-new_image-editor-image_count">
        <label>
            <?php echo esc_html__( 'Set number of images', 'tenweb-builder' ); ?>
            <div class="twbb-help-tooltip"></div>
            <p class="twbb-help-tooltip-content" style="display:none">
                <?php echo esc_html__( 'Number of images determines how many images will be generated. Four is selected by default. ', 'tenweb-builder' ); ?>
            </p>
        </label>
        <div class="twbb-select">
            <span class="twbb-select-value" data-value="4">
                <span><?php echo esc_html__( '4 images', 'tenweb-builder' ); ?></span>
                <i class="twbb-select-arrow"></i>
            </span>
            <ul class="twbb-select-dropdown">
                <?php
                for ( $i = 1; $i <= 4; $i++ ) { ?>
                    <li data-value="<?php esc_attr_e( intval($i) ); ?>" <?php if($i === 4 ) echo 'class="twbb-select-active"'; ?>><?php ( $i === 1 ) ? esc_html_e($i . ' image') : esc_html_e($i . ' images'); ?></li>
                <?php
                } ?>
            </ul>
        </div>
    </div>
    <div class="twbb-image-editor-row twbb-generate_image">
        <div class="twbb-generate_imag-button twbb-generate_imag-description-button twbb-request-button twbb-generate_imag-button-disabled" data-action="image_generate">
            <span><?php echo esc_html__( 'Generate with AI', 'tenweb-builder' ); ?></span>
            <img src="<?php echo esc_url(TWBB_URL  .  '/Apps/ImageGenerationAI/assets/images/10web_icon.svg'); ?>" class="twbb-generate_imag-button-icon">
            <span class="twbb-generate_imag-button-loader"></span>
            <?php if ( !$this->available_credits ) { ?>
                <div class="twbb-image-gen-credits-layer twbb-image-gen-credits-exceed">
                    <div class="twbb-image-gen-credits-content">
                        <p class="twbb-image-gen-credits-title"><?php esc_html_e('Your credits limit exceeded','tenweb-builder') ?></p>
                        <p class="twbb-image-gen-credits-description"><?php esc_html_e('To add more credits upgrade your plan.','tenweb-builder') ?></p>
                        <div class="twbb-image-gen-credits-points-row">
                            <span class="twbb-image-gen-credit-amount">0</span>
                            <span class="twbb-image-gen-credits-total">/<?php echo intval($this->images_plan_limit/1000); ?>K</span>
                            <div class="twbb-image-gen-credits-about">
                                <span class="twbb-image-gen-credits-image_count">0</span>
                                <span class="twbb-image-gen-credits-text"><?php esc_html_e('images','tenweb-builder') ?></span>
                            </div>
                        </div>
                        <div class="twbb-image-gen-credits-description-row">
                            <?php esc_html_e('~2 credits to generate one image and from 2 to 24 credits to expand an image','tenweb-builder') ?>
                        </div>
                        <div class="twbb-image-gen-credits-upgrade-row">
                            <div class="twbb-image-gen-reset-date">Expiration date:<br> <?php echo esc_html($this->resetDate); ?></div>
                            <a href="#" target="_blank" class="twbb-image-gen-credits-upgrade-button"><?php esc_html_e('Upgrade','tenweb-builder') ?></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</script>

<!--New image Preview template-->
<script type="text/template" id="twbb-image-four_image-empty-template">
    <div class="twbb-image-preview-container">
        <div class="twbb-image-preview-item">
            <div class="twbb-image-preview"></div>
            <span class="twbb-generate_imag-button-loader"></span>
        </div>
        <div class="twbb-image-preview-item">
            <div class="twbb-image-preview"></div>
            <span class="twbb-generate_imag-button-loader"></span>
        </div>
        <div class="twbb-image-preview-item">
            <div class="twbb-image-preview"></div>
            <span class="twbb-generate_imag-button-loader"></span>
        </div>
        <div class="twbb-image-preview-item">
            <div class="twbb-image-preview"></div>
            <span class="twbb-generate_imag-button-loader"></span>
        </div>
        <div class="twbb-edit_image-thumb-container">
        </div>
    </div>
</script>

<!--Edit image editor template-->
<script type="text/template" id="twbb-image-edit_image-editor-template">
        <div class="twbb-image-editor-row twbb-edit_image-editor-image_description">
            <label>
                <?php echo esc_html__( 'Describe changes', 'tenweb-builder' ); ?>
                <div class="twbb-help-tooltip"></div>
                <p class="twbb-help-tooltip-content" style="display:none">
                    <?php echo esc_html__( 'Image prompt tells the Al model what you want to see in the generated images. When specified, it guides the generation process to include things in the image according to the given text.', 'tenweb-builder' ); ?>
                </p>
            </label>
            <textarea class="twbb-image-description" placeholder="<?php echo esc_attr__( 'Ex: Chocolate cake with rich, fudgy frosting and perfectly layered cake, garnished with fresh berries and drizzled with melted chocolate.', 'tenweb-builder' ); ?>"></textarea>
            <p class="twbb-image-error-text"></p>
        </div>
        <div class="twbb-image-editor-row twbb-generate_image twbb-edit_generate_image">
            <div class="twbb-generate_imag-button twbb-generate_imag-description-button twbb-request-button twbb-generate_imag-button-disabled" data-action="image_edit">
                <span><?php echo esc_html__( 'Generate with AI', 'tenweb-builder' ); ?></span>
                <img src="<?php echo esc_url(TWBB_URL  .  '/Apps/ImageGenerationAI/assets/images/10web_icon.svg'); ?>" class="twbb-generate_imag-button-icon">
                <span class="twbb-generate_imag-button-loader"></span>
            </div>
        </div>
        <div class="twbb-image-editor-row twbb-edit_image-editor-image_ratio">
            <label class="twbb-image-editor-title">
                <?php echo esc_html__( 'Select ratio for expansion', 'tenweb-builder' ); ?>
                <div class="twbb-help-tooltip"></div>
                <p class="twbb-help-tooltip-content" style="display:none">
                    <?php echo esc_html__( 'Aspect ratio\'s determine the dimensions of the generated result. 
                        Square (1:1) ratio is selected by default.', 'tenweb-builder' ); ?>
                    <br><br>
                    <?php echo esc_html__( 'The image expansion can require between 2 to 24 credits.', 'tenweb-builder' ); ?>
                </p>
            </label>
            <div class="twbb-generate_imag-action-row">
                <div class="twbb-select">
                    <span class="twbb-select-value" data-value="Square (1:1)">
                        <span><?php echo esc_html__( 'Square (1:1)', 'tenweb-builder' ); ?></span>
                        <i class="twbb-select-arrow"></i>
                    </span>
                    <ul class="twbb-select-dropdown">
                        <?php
                        foreach ( $this->image_ratio as $img_ratio ) {
                            $class = '';
                            if( $img_ratio === 'Square (1:1)' ) {
                            $class = 'twbb-select-active';
                            }
                            ?>
                            <li data-value="<?php echo esc_attr($img_ratio); ?>" class="<?php echo esc_attr($class); ?>"><?php echo esc_html($img_ratio); ?></li>
                            <?php
                        } ?>
                    </ul>
                </div>
                <div class="twbb-generate_imag-button twbb-request-button" data-action="image_expand">
                    <span><?php echo esc_html__( 'Apply', 'tenweb-builder' ); ?></span>
                    <img src="<?php echo esc_url(TWBB_URL  .  '/Apps/ImageGenerationAI/assets/images/10web_icon.svg'); ?>" class="twbb-generate_imag-button-icon">
                    <span class="twbb-generate_imag-button-loader"></span>
                </div>
            </div>
        </div>
        <div class="twbb-image-editor-row twbb-edit_image-editor-image_resolution">
            <label class="twbb-image-editor-title">
                <?php echo esc_html__( 'Adjust image resolution', 'tenweb-builder' ); ?>
                <div class="twbb-help-tooltip"></div>
                <p class="twbb-help-tooltip-content" style="display:none">
                    <?php echo esc_html__( 'Adjust the image\'s resolution to achieve your desired quality and size.', 'tenweb-builder' ); ?>
                </p>
            </label>
            <div class="twbb-generate_imag-action-row">
                <div class="twbb-select">
                    <span class="twbb-select-value" data-value="2">
                        <span><?php echo esc_html__( '2x', 'tenweb-builder' ); ?></span>
                        <i class="twbb-select-arrow"></i>
                    </span>
                    <ul class="twbb-select-dropdown">
                        <?php
                        foreach ( $this->image_resolution as $img_res ) { ?>
                            <li data-value="<?php esc_attr_e( intval(str_replace('x','', $img_res))); ?>" <?php if($img_res === '2x' ) echo 'class="twbb-select-active"'; ?>"><?php echo esc_html($img_res); ?></li>
                            <?php
                        } ?>
                    </ul>
                </div>
                <div class="twbb-generate_imag-button twbb-request-button" data-action="image_upscale">
                    <span><?php echo esc_html__( 'Enhance', 'tenweb-builder' ); ?></span>
                    <img src="<?php echo esc_url(TWBB_URL  .  '/Apps/ImageGenerationAI/assets/images/10web_icon.svg'); ?>" class="twbb-generate_imag-button-icon">
                    <span class="twbb-generate_imag-button-loader"></span>
                </div>

            </div>
        </div>
        <div class="twbb-image-editor-row twbb-edit_image-editor-remove_background">
            <label class="twbb-image-editor-title">
                <?php echo esc_html__( 'Remove background', 'tenweb-builder' ); ?>
                <div class="twbb-help-tooltip"></div>
                <p class="twbb-help-tooltip-content" style="display:none">
                    <?php echo esc_html__( 'Quickly eliminate the background from your image for a clean and focused result.', 'tenweb-builder' ); ?>
                </p>
            </label>
            <div class="twbb-generate_imag-button twbb-request-button" data-action="image_remove_bg">
                <span><?php echo esc_html__( 'Remove with AI', 'tenweb-builder' ); ?></span>
                <img src="<?php echo esc_url(TWBB_URL  .  '/Apps/ImageGenerationAI/assets/images/10web_icon.svg'); ?>" class="twbb-generate_imag-button-icon">
                <span class="twbb-generate_imag-button-loader"></span>
            </div>
        </div>
</script>

<!--Edit image Preview template-->
<script type="text/template" id="twbb-image-edit_image-preview-template">
    <div class="twbb-edit_image-preview-container">
        <div class="twbb-edit_image-preview">
            <div class="twbb-edit_image">
                    <div  class="twbb-edit_image-container">
                        <img src="#">
                    </div>
            </div>
        </div>
        <div class="twbb-edit_image-thumb-container">
        </div>
    </div>
</script>

<!--Edit image Preview thumb template-->
<script type="text/template" id="twbb-image-edit_image-preview-thumb-template">
    <div class="twbb-edit_image-thumb twbb-edit_image-thumb-new"><img src=""></div>
</script>

<!--Multiview Editor template-->
<script type="text/template" id="twbb-image-multi_image-editor-template">
    <div class="twbb-image-editor-row twbb-multi_image-editor-image_description">
        <label>
            <?php echo esc_html__( 'Describe the image', 'tenweb-builder' ); ?>
            <div class="twbb-help-tooltip"></div>
            <p class="twbb-help-tooltip-content" style="display:none">
                <?php echo esc_html__( 'The image prompt guides the generation process to include specific elements based on the provided text.', 'tenweb-builder' ); ?>
            </p>
        </label>
        <textarea class="twbb-image-description" placeholder="<?php echo esc_attr__( 'Ex: Chocolate cake with rich, fudgy frosting and perfectly layered cake, garnished with fresh berries and drizzled with melted chocolate.', 'tenweb-builder' ); ?>"></textarea>
        <p class="twbb-image-error-text"></p>
    </div>
    <div class="twbb-image-editor-row twbb-multi_image-editor-image_count">
        <label>
            <?php echo esc_html__( 'Set number of images', 'tenweb-builder' ); ?>
            <div class="twbb-help-tooltip"></div>
            <p class="twbb-help-tooltip-content" style="display:none">
                <?php echo esc_html__( 'Number of images determines how many images will be generated. Four is selected by default.', 'tenweb-builder' ); ?>
            </p>
        </label>
        <div class="twbb-select">
            <span class="twbb-select-value">
                <span><?php echo esc_html__( '3 images', 'tenweb-builder' ); ?></span>
                <i class="twbb-select-arrow"></i>
            </span>
            <ul class="twbb-select-dropdown">
                <?php
                for ( $i = 1; $i < 4; $i++ ) { ?>
                    <li data-value="<?php esc_attr_e( intval($i)); ?>" <?php if($i === 3 ) echo 'class="twbb-select-active"'; ?>><?php ($i === 1) ? esc_html_e($i . ' image') : esc_html_e($i . ' images'); ?></li>
                    <?php
                } ?>
            </ul>
        </div>
    </div>
    <div class="twbb-image-editor-row twbb-generate_image twbb-generate_multi_image">
        <div class="twbb-generate_imag-button twbb-request-button" data-action="image_variations">
            <span><?php echo esc_html__( 'Generate with AI', 'tenweb-builder' ); ?></span>
            <img src="<?php echo esc_url(TWBB_URL  .  '/Apps/ImageGenerationAI/assets/images/10web_icon.svg'); ?>" class="twbb-generate_imag-button-icon">
            <span class="twbb-generate_imag-button-loader"></span>
        </div>
    </div>
</script>

<!--Edit image Tools template-->
<script type="text/template" id="twbb-image-edit_image-tools-template">
    <div class="twbb-image-edit_image-tools-container">
        <div class="twbb-image-edit-tool twbb-image-edit" data-action="edit">
            <span class="twbb-icon-tooltip"><?php esc_html_e('Edit image','tenweb-builder') ?></span>
        </div>
        <a href="" target="_blank" class="twbb-image-edit-tool twbb-image-download" data-action="download" download>
            <span class="twbb-icon-tooltip"><?php esc_html_e('Download image','tenweb-builder') ?></span>
        </a>
    </div>
</script>

<!--Exceed credit limits popup template -->
<script type="text/template" id="twbb-image-gen-credits-exceed-template">
    <div class="twbb-image-gen-credits-layer twbb-image-gen-credits-exceed">
        <div class="twbb-image-gen-credits-content">
            <p class="twbb-image-gen-credits-title"><?php esc_html_e('Your credits limit exceeded','tenweb-builder') ?></p>
            <p class="twbb-image-gen-credits-description"><?php esc_html_e('To add more credits upgrade your plan.','tenweb-builder') ?></p>
            <div class="twbb-image-gen-credits-points-row">
                <span class="twbb-image-gen-credit-amount">0</span>
                <span class="twbb-image-gen-credits-total">/<?php echo intval($this->images_plan_limit/1000); ?>K</span>
                <div class="twbb-image-gen-credits-about">
                    <span class="twbb-image-gen-credits-image_count">0</span>
                    <span class="twbb-image-gen-credits-text"><?php esc_html_e('images','tenweb-builder') ?></span>
                </div>
            </div>
            <div class="twbb-image-gen-credits-description-row">
                <?php esc_html_e('~2 credits to generate one image and from 2 to 24 credits to expand an image','tenweb-builder') ?>
            </div>
            <div class="twbb-image-gen-credits-upgrade-row">
                <div class="twbb-image-gen-reset-date">Expiration date:<br> <?php echo esc_html($this->resetDate); ?></div>
                <a href="#" target="_blank" class="twbb-image-gen-credits-upgrade-button"><?php esc_html_e('Upgrade','tenweb-builder') ?></a>
            </div>
        </div>
    </div>
</script>

<!--Save image before close alert template-->
<script type="text/template" id="twbb-image-gen-alert-template">
    <div class="twbb-image-gen-alert-container">
        <p class="twbb-image-gen-alert-title"><?php esc_html_e('Save liked images before closing!','tenweb-builder'); ?></p>
        <p class="twbb-image-gen-alert-description"><?php esc_html_e('Download your favorite images before closing the window. Once it\'s shut, they\'ll be gone and  there\'s no way to recover them.','tenweb-builder'); ?></p>
        <div class="twbb-image-gen-alert-button-row">
            <span class="twbb-image-gen-alert-button twbb-image-gen-alert-button-cancel"><?php esc_html_e('Cancel','tenweb-builder'); ?></span>
            <span class="twbb-image-gen-alert-button twbb-image-gen-alert-button-close"><?php esc_html_e('Close','tenweb-builder'); ?></span>
        </div>
        <span class="twbb-image-gen-alert-close"></span>
    </div>
</script>

<!--Save image before close Use image action alert template-->
<script type="text/template" id="twbb-image-gen-alert-useImage-template">
    <div class="twbb-image-gen-alert-container">
        <p class="twbb-image-gen-alert-title"><?php esc_html_e('Save your images!','tenweb-builder'); ?></p>
        <p class="twbb-image-gen-alert-description">
            <?php esc_html_e('Before proceeding with your selected image, make sure to save the others.','tenweb-builder'); ?>
            <br>
            <?php esc_html_e('If you close the window, you won\'t be able to retrieve your favorite images later.','tenweb-builder'); ?>
            <br>
            <?php esc_html_e('Take a moment to download your generated images.','tenweb-builder'); ?></p>
        <div class="twbb-image-gen-alert-button-row">
            <span class="twbb-image-gen-alert-button twbb-image-gen-alert-button-cancel"><?php esc_html_e('Cancel','tenweb-builder'); ?></span>
            <span class="twbb-image-gen-alert-button twbb-image-gen-alert-button-use_image"><?php esc_html_e('Proceed','tenweb-builder'); ?></span>
        </div>
        <span class="twbb-image-gen-alert-close"></span>
    </div>
</script>


<!--Image generation error message template-->
<script type="text/template" id="twbb-image-gen_error-template">
    <p class="twbb-image-gen-message twbb-image-gen_error">
        <?php esc_html_e('The server had an error while processing your request.','tenweb-builder'); ?>
    </p>
</script>

<!--Image generation success message template-->
<script type="text/template" id="twbb-image-gen_success-template">
    <p class="twbb-image-gen-message twbb-image-gen_success">
        <?php esc_html_e('Successfully accomplished!','tenweb-builder'); ?>
    </p>
</script>

<!--Image generation undo/redo template-->
<script type="text/template" id="twbb-image-undo-redo-template">
        <span class="twbb-image-gen-undo"></span>
        <span class="twbb-image-gen-redo"></span>
</script>


