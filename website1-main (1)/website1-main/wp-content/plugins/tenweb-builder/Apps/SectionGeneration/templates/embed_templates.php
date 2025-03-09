<script type="text/template" id="twbb-sg-messages">
    <div class="twbb-sg-messages">
        <div class="twbb-sg-add-section twbb-sg-not-in-process-generation__one_button">
            <span class="twbb-sg-just-add-button">
                <?php esc_html_e('To add, just click it!', 'tenweb-builder');?>
            </span>
        </div>
        <div class="twbb-sg-add-section twbb-sg-not-in-process-generation__two_buttons">
            <div class="twbb-sg-add-with-generated-content">
                <span class="twbb-sg-add-section-button twbb-sg-add-with-generated-content__button">
                    <?php esc_html_e('Add with AI Content', 'tenweb-builder');?>
                </span>
            </div>
            <div class="twbb-sg-add-with-dummy-content">
                <span class="twbb-sg-add-section-button twbb-sg-add-with-dummy-content__button">
                    <?php esc_html_e('Add as Previewed', 'tenweb-builder');?>
                </span>
            </div>
        </div>

        <div class="twbb-sg-process-generation">
            <span class="in-process-generation">
                <?php esc_html_e('Another generation in progress.', 'tenweb-builder');?> <br> <?php esc_html_e('Please wait for completion.', 'tenweb-builder');?>
            </span>
        </div>

        <div class="twbb-sg-loading">
            <div class="twbb-sg-loading-spinner"></div>
            <span><?php esc_html_e('Generating text & images', 'tenweb-builder');?></span>
        </div>
    </div>
</script>

<script type="text/template" id="twbb-sg-overlay">
    <div class="twbb-sg-overlay"></div>
</script>