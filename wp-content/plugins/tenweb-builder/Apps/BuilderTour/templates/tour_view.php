<?php ?>
<!--Editor Tour View template-->
<script type="text/template" id="twbb-editor-tour-template">
    <div class="twbb-tour-main-overlay twbb-tour-guide">
        <div class="twbb-tour-guide__container">
            <p class="twbb-tour-guide__steps"></p>
            <div class="twbb-tour-guide__video">
                <video width="310" height="170" autoplay muted loop preload="auto"></video>
                <span class="twbb-tour-zoom_in"></span>
            </div>
            <p class="twbb-tour-guide__title"></p>
            <p class="twbb-tour-guide__description"></p>
            <div class="twbb-tour-guide__buttons">
                <div class="twbb-tour-guide__button twbb-tour-guide__left_button"></div>
                <div class="twbb-tour-guide__button twbb-tour-guide__right_button"></div>
            </div>
        </div>
    </div>
</script>

<!--Editor Tour small popup-->
<script type="text/template" id="twbb-editor-tour-small-notif-template">
    <div class="twbb-editor-tour-small-notif-main-container">
        <div class="twbb-editor-tour-small-notif-container">
            <span class="twbb-editor-tour-notif-title__close" onclick="deleteNotification()"></span>
            <div class="twbb-editor-tour-notif-title">
                <?php esc_html_e('10Web Builder Editor tour', 'tenweb-builder'); ?>
            </div>
            <div class="twbb-editor-tour-notif-description">
                <?php echo wp_kses_post(__('Explore our drag-and-drop editor’s powerful<br> features that simplify website creation.', 'tenweb-builder')); ?>
            </div>
            <div class="twbb-editor-tour-notif-button-container">
                <div class="twbb-editor-tour-notif-button" onclick="twbbStartTour()">
                    <?php esc_html_e('Let’s Get Started', 'tenweb-builder'); ?>
                </div>
            </div>
        </div>
    </div>
</script>
<?php