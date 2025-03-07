<!--Empty chat container template-->
<script type="text/template" id="twbb-copilot-main-icon-template">
    <span id="twbb-copilot-main-icon"><span class="twbb-copilot-tooltip">10Web AI Co-Pilot<span class="twbb-copilot-alpha">Alpha</span></span></span>
</script>

    <!--Empty chat container template-->
<script type="text/template" id="twbb-copilot-template">
    <div id="twbb-copilot-chat_container" style="display: none">
        <div id="twbb-copilot-header">
            <div class="twbb-copilot-header-logo">10Web AI Co-Pilot<span class="twbb-copilot-alpha">Alpha</span></div>
            <div class="twbb-copilot-header-settings">
                <div class="twbb-copilot-header-settings-menu-container">
                    <div class="twbb-copilot-header-settings-menu">
                        <span class="twbb-copilot-header-settings-menu-item twbb-copilot-header-settings-menu-item-inactive twbb-copilot-clear-chat">Clear Chat</span>
                    </div>
                </div>
            </div>
            <div class="twbb-copilot-header-minimize"><span class="twbb-copilot-tooltip">Minimize</span></div>
        </div>
        <div id="twbb-copilot-message_history">
            <div class="twbb-copilot-feedback-highlights-blure-container"></div>
        </div>
        <div id="twbb-copilot-info-row">
            <div class="twbb-copilot-chosen-widget" style="display: none">
                <div class="twbb-copilot-info-title">
                    <span class="twbb-copilot-info-title-default">Selected element: </span>
                    <span class="twbb-copilot-info-name-widget"></span>
                </div>
                <div class="twbb-copilot-info-action"><span>View</span></div>
            </div>
        </div>
        <div id="twbb-copilot-footer">
            <textarea id="twbb-copilot-user_input" placeholder="Type here..."></textarea>
            <div id="twbb-copilot-chat_button" class="twbb-copilot-chat_button-inactive"></div>
            <div class="twbb-copilot-inprogress-message-container">
                <p class="twbb-copilot-inprogress-message-title">Another request is in progress.</p>
                <p>Please wait until the current process finishes.</p>
            </div>
        </div>

        <div class="twbb-copilot-feedback-reason-container" style="display: none">
            <i class="twbb-copilot-feedback-reason-close"></i>
            <div class="twbb-copilot-feedback-reason-title">How can we improve?</div>
            <textarea class="twbb-copilot-feedback-reason-description" placeholder="Tell us what went wrong..."></textarea>
            <div class="twbb-copilot-feedback-reason-button-row">
                <span class="twbb-copilot-feedback-reason-button twbb-copilot-feedback-reason-button-deactive">Submit</span>
            </div>
        </div>

        <div class="twbb-copilot-feedback-highlights-container">
            <span>Your feedback means the world to us.</span>
            <span>Share your thoughts and be part of the journey!</span>
        </div>

        <div class="twbb-copilot-no-widget-container">
            <div class="twbb-copilot-no-widget-content">
                <span class="twbb-copilot-no-widget-title">Hi! Ready to edit your page?</span>
                <span class="twbb-copilot-no-widget-message">Just let me know what changes you need, and I’ll take care of it!</span>
            </div>
            <span class="twbb-copilot-no-widget-video-message">Co-Pilot interactions are recorded to improve the service quality. Opt-out anytime in settings.</span>
        </span>
    </div>
</script>

<!--Feedback form template-->
<script type="text/template" id="twbb-copilot-feedback-template">
    <div class="twbb-copilot_chat_feedback">
        <div class="twbb-copilot-feedback-good twbb-copilot-feedback-score"><span class="twbb-copilot-tooltip">Good response</span></div>
        <div class="twbb-copilot-feedback-bad-container twbb-copilot-feedback-score">
            <i class="twbb-copilot-feedback-bad"></i>
            <span class="twbb-copilot-tooltip">Bad response</span>
        </div>
        <div class="twbb-copilot-feedback-eye twbb-copilot-feedback-score"><span class="twbb-copilot-tooltip">View</span></div>
    </div>
</script>

<!--Message assistant template -->
<script type="text/template" id="twbb-copilot-message-assistant-template">
    <div class="twbb-copilot-message twbb-ai-message twbb-copilot-message-row twbb-copilot-message-row-empty">
        <div class="twbb-copilot-message-container">
            <div class="twbb-copilot-message-role">Co-pilot</div>
            <div class="twbb-copilot-message-text"></div>
        </div>
    </div>
</script>

<!--Message user template -->
<script type="text/template" id="twbb-copilot-message-user-template">
    <div class="twbb-copilot-message twbb-user-message twbb-copilot-message-row twbb-copilot-message-row-empty">
        <div class="twbb-copilot-message-container">
            <div class="twbb-copilot-message-text"></div>
        </div>
    </div>
</script>

<!--Copilot request loadind template -->
<script type="text/template" id="twbb-copilot-request-loading-template">
    <div class="twbb-copilot-request-loading">
        <b>Co-Pilot</b> is working <span class="twbb-copilot-on">on</span> <span class="twbb-copilot-request-widgetName"></span>
    </div>
</script>
<?php
$tour_status = get_option('twbb-coPilot-tour-status');
$show_tour = false;

if( $tour_status === false ) {
    update_option('twbb-coPilot-tour-status',1);
    $show_tour = true;
}

if( $show_tour ) {
?>
<!--Copilot tour template -->
<script type="text/template" id="twbb-copilot-tour-template">
    <div class="twbb-copilot-tour-layer"></div>
    <div class="twbb-copilot-tour-container">
        <div class="twbb-copilot-tour-descr-cont">
            <div class="twbb-copilot-tour-welcome">Welcome to</div>
            <div class="twbb-copilot-tour-title">10Web AI Co-Pilot<span class="twbb-copilot-alpha">Alpha</span></div>
            <div class="twbb-copilot-tour-description">You're one of the first to try our new Co-Pilot, built to simplify website editing and page creation.</div>

            <div class="twbb-copilot-images-row">
                <div class="twbb-copilot-image-item" data-name="select">Select
                    <div class="twbb-copilot-circle-loader" style="display: none">
                        <!-- Embed the SVG you uploaded -->
                        <svg width="14" height="12" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" stroke="#3498db" stroke-width="10" fill="none" />
                            <!-- This is the blue progress circle -->
                            <circle cx="50" cy="50" r="45" stroke="#ffffff" stroke-width="10" fill="none" class="twbb-copilot-progress-circle" />
                        </svg>
                    </div>
                </div>
                <div class="twbb-copilot-image-item" data-name="describe">Describe
                    <div class="twbb-copilot-circle-loader" style="display: none">
                        <!-- Embed the SVG you uploaded -->
                        <svg width="14" height="12" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" stroke="#3498db" stroke-width="10" fill="none" />
                            <!-- This is the blue progress circle -->
                            <circle cx="50" cy="50" r="45" stroke="#ffffff" stroke-width="10" fill="none" class="twbb-copilot-progress-circle" />
                        </svg>
                    </div>
                </div>
                <div class="twbb-copilot-image-item" data-name="evaluate">Evaluate the result
                    <div class="twbb-copilot-circle-loader" style="display: none">
                        <!-- Embed the SVG you uploaded -->
                        <svg width="14" height="12" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" stroke="#3498db" stroke-width="10" fill="none" />
                            <!-- This is the blue progress circle -->
                            <circle cx="50" cy="50" r="45" stroke="#ffffff" stroke-width="10" fill="none" class="twbb-copilot-progress-circle" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="twbb-copilot-tour-subtitle">Edit visual elements</div>
            <div class="twbb-copilot-tour-subdescr">Select an element, describe the changes you want (text,images, or styles), and the Co-Pilot will take care of it for you.</div>
            <div class="twbb-copilot-tour-subtitle">Add sections or visual elements</div>
            <div class="twbb-copilot-tour-subdescr">Describe the section or element you want to add, and 10Web Co-Pilot will seamlessly create it for you.</div>
            <div class="twbb-copilot-tour-button-descr">Your feedback is vital in this Alpha phase, and we truly appreciate your support!</div>
            <div class="twbb-copilot-tour-button">Let’s Try</div>
        </div>
        <div class="twbb-copilot-tour-video-cont">
            <img src="<?php echo esc_url(TWBB_URL.'/Apps/CoPilot/assets/images/select.jpg'); ?>" id="twbb-copilot-img-select" style="display: none">
            <img src="<?php echo esc_url(TWBB_URL.'/Apps/CoPilot/assets/images/describe.jpg'); ?>" id="twbb-copilot-img-describe" style="display: none">
            <img src="<?php echo esc_url(TWBB_URL.'/Apps/CoPilot/assets/images/evaluate.jpg'); ?>" id="twbb-copilot-img-evaluate" style="display: none">
        </div>
    </div>
</script>
<?php } ?>
