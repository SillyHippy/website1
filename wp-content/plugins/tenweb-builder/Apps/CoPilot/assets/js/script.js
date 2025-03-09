class TWBBCoPilot {
    init() {
        this.setDefaults();
        this.initFirebase();
        this.addChatToPage();
        this.getAuthToken();

        this.registerEvents();
        this.runCoPilotTour();
        this.runForTest();
    }

    setDefaults() {
        this.firebaseConfig = twbb_chat.firebaseConfig;

        /* Keeping value if coPilot action in progress */
        this.inProgress = 0;
        /* Keeping current active widget object */
        this.activeWidget = {};
        /* Keeping in progress action widget object */
        this.inProgressWidget = {};
        /* Keeping response to use it printing message when action will be finished */
        this.coPilotResponse = {};
        /*Need to keep status as response print is working from all actions methods to avoid multiple action duplication*/
        this.coPilotResponsePrinted = 0;
        /*Keeping count to open feedback highlight popup when count is 3*/
        this.coPilotResponseCount = 0;
        /*Keeping count to open feedback highlight popup when count is 3*/
        this.newAddedWidgetModelId = 0;
    }

    runForTest() {
        if(!twbb_chat.testMode) return;
        let multiButtonGroup = document.querySelector('.MuiButtonGroup-root');
        let exportElementorTreeBtn = document.createElement('button');
        exportElementorTreeBtn.setAttribute('id', 'twb_export_elementor_tree');
        exportElementorTreeBtn.innerText = "Export elementor tree";

        let importElementorTreeBtn = document.createElement('button');
        importElementorTreeBtn.setAttribute('id', 'twb_import_elementor_tree');
        importElementorTreeBtn.innerText = "Import elementor tree";

        multiButtonGroup.parentElement.insertBefore(exportElementorTreeBtn, multiButtonGroup);
        multiButtonGroup.parentElement.insertBefore(importElementorTreeBtn, multiButtonGroup);

        let self = this;
        document.querySelector('#twb_export_elementor_tree').addEventListener('click', function () {
            window.parent.$e.data.get('globals/index').then(function (_ref) {
                if (self.activeWidget) {
                    if (document.querySelector('#twb_elementor_tree_content_export')){
                        document.querySelector('#twb_elementor_tree_content_export').remove();
                    }
                    const elementorTree = new TWBCElementorTree(self.activeWidget).getTree();
                    const elementorKit = TWBCUtils.getElementorKit(_ref.data);
                    let elementorTreeContent = document.createElement('div');
                    elementorTreeContent.setAttribute('id', 'twb_elementor_tree_content_export');
                    elementorTreeContent.setAttribute('style', 'position: absolute; top: 10%; left: 0; right: 0; z-index: 99999999; background: #fff; padding: 20px; border: 1px solid #000; max-height: 1000px; overflow: auto; width: 90%; color:#000; margin: 0 auto; text-align:center;');
                    elementorTreeContent.innerHTML =
                        '<div style="text-align:right; font-weight: bold; cursor: pointer; padding: 3px;" onclick="document.querySelector(\'#twb_elementor_tree_content_export\').remove();">X</div>' +
                        '<h2>Elementor Tree</h2>' +
                        '<textarea style="padding: 20px; height:300px" id="twb_elementor_json">' + JSON.stringify(elementorTree) + '</textarea>' +
                        '<h2>Elementor Kit</h2>' +
                        '<textarea style="padding: 20px;" id="twb_elementor_kit">' +  JSON.stringify(elementorKit) + '</textarea>'
                    ;
                    document.body.insertBefore(elementorTreeContent, document.body.firstChild);
                }
            });
        });

        document.querySelector('#twb_import_elementor_tree').addEventListener('click', function () {
            window.parent.$e.data.get('globals/index').then(function (_ref) {
                if (document.querySelector('#twb_elementor_tree_content_import')){
                    return;
                }
                let elementorTreeContent = document.createElement('div');
                elementorTreeContent.setAttribute('id', 'twb_elementor_tree_content_import');
                elementorTreeContent.setAttribute('style', 'position: absolute; top: 10%; left: 0; right: 0; z-index: 99999999; background: #fff; padding: 20px; border: 1px solid #000; max-height: 500px; overflow: auto; width: 90%; color:#000; margin: 0 auto; text-align:center;');
                elementorTreeContent.innerHTML =
                    '<div style="text-align:right; font-weight: bold; cursor: pointer; padding: 3px;" onclick="document.querySelector(\'#twb_elementor_tree_content_import\').remove();">X</div>' +
                    '<h2>Import Elementor Tree</h2>' +
                    '<div style="text-align:right"><button id="twbb_elementor_tree_import_submit" style="padding:5px;">Import</button></div>' +
                    '<textarea style="padding: 20px; height:300px;" id="twb_elementor_json_import">[]</textarea>';
                document.body.insertBefore(elementorTreeContent, document.body.firstChild);
            });
        });

        jQuery(document).on("click","#twbb_elementor_tree_import_submit", function() {
            let elementorTreeData = document.querySelector('#twb_elementor_json_import').value;
            elementorTreeData = JSON.parse(elementorTreeData);
            for (let node of elementorTreeData) {
                node["is_new_widget"] = true;
            }
            new TWBCUpdateElementorTree({
                tree: elementorTreeData,
                deleted_repeated_widgets: [],
                deleted_widgets_ids: [],
            }, {}, '').run();
            document.querySelector('#twb_elementor_tree_content_import').remove();
        });
    }
    runCoPilotTour() {
        let self = this;
        let templateLayer = jQuery(document).find("#twbb-copilot-tour-template").html();
        jQuery("body").append(templateLayer);

        this.changeTourImage(jQuery(document).find(".twbb-copilot-images-row .twbb-copilot-image-item").eq(0));
        this.runAutoChange();
    }

    runAutoChange() {
        let self = this;
        self.changeInterval = setInterval(function() {
            let nextItem = '';
            let index = jQuery(document).find(".twbb-copilot-images-row .twbb-copilot-image-item.twbb-copilot-image-item-active").index();
            if( index == 2 ) {
                nextItem = jQuery(document).find(".twbb-copilot-images-row .twbb-copilot-image-item").eq(0);
            } else {
                nextItem = jQuery(document).find(".twbb-copilot-images-row .twbb-copilot-image-item").eq(index+1);
            }
            self.changeTourImage(nextItem);
        }, 3000);
    }

    changeTourImage(that) {
        jQuery(that).find('.twbb-copilot-progress-circle').css('stroke-dashoffset', '283');
        jQuery(document).find(".twbb-copilot-images-row .twbb-copilot-image-item").removeClass("twbb-copilot-image-item-active");
        jQuery(that).addClass("twbb-copilot-image-item-active");
        let name = jQuery(that).attr("data-name");
        jQuery(document).find(".twbb-copilot-tour-video-cont img").hide();
        jQuery(document).find(".twbb-copilot-tour-video-cont #twbb-copilot-img-"+name).show();


        jQuery(document).find(".twbb-copilot-circle-loader").hide();
        jQuery(that).find(".twbb-copilot-circle-loader").show();
        const $progressCircle = jQuery(that).find('.twbb-copilot-progress-circle');
        // Set strokeDashoffset to 0 to animate the fill using jQuery's css method
        $progressCircle.css('stroke-dashoffset', '0');
    }

    getAuthToken() {
        if (twbb_chat.testModeApi) return;
        let self = this;
        jQuery.ajax({
            url: twbb_chat.customTokenApi,
            headers: {
                Accept: "application/x.10webaiassistantapi.v1+json",
                Authorization: "Bearer " + twbb_chat.accessToken
            },
            type: 'GET',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            success: function (response) {
                const customToken = response.data.token;
                self.initFirestore(customToken);

            },
            error: function (error) {
                console.log('Error Token:', error);
            }
        });

    }

    initFirebase() {
        if (twbb_chat.testModeApi) return;
        firebase.initializeApp(this.firebaseConfig);
        this.db = firebase.firestore();
        this.existingDocs = [];
        this.converter = new showdown.Converter();

    }

    addChatToPage() {
        let templateForm = jQuery(document).find("#twbb-copilot-template").html();
        jQuery('body').append(templateForm);
        let template = jQuery(document).find("#twbb-copilot-main-icon-template").html();
        jQuery('body').append(template);
    }

    openChat() {
        jQuery(document).find("#twbb-copilot-chat_container").show();
        jQuery(document).find("#twbb-copilot-main-icon").remove();
        jQuery('.twbb-fast-editor-tools-container').addClass('twbb_chat_opened');
        this.chatScrollBottom();
    }

    closeChat() {
        let template = jQuery(document).find("#twbb-copilot-main-icon-template").html();
        jQuery('body').append(template);
        jQuery(document).find("#twbb-copilot-chat_container").hide();
        if( this.inProgress == 1 ) {
            jQuery(document).find("#twbb-copilot-main-icon").addClass("twbb-copilot-main-icon-loading");
        }
        jQuery('.twbb-fast-editor-tools-container').removeClass('twbb_chat_opened');
    }

    initFirestore(customToken) {
        if (twbb_chat.testModeApi) return;
        let self = this;
        firebase.auth().signInWithCustomToken(customToken).then((userCredential) => {
            self.db.collection("domains").doc(twbb_chat.domainId).collection('messages')
                .where("page_id", "==", twbb_chat.pageId)
                .orderBy("timestamp")
                .get().then((snapshot) => {
                self.previousDate = null;
                snapshot.forEach((doc) => {
                    self.existingDocs.push(doc.id);
                    self.addChatDate(doc.data().timestamp);
                    if (doc.data().bot == 0) {
                        let messageTemplate = jQuery(document).find("#twbb-copilot-message-user-template").html();
                        document.getElementById('twbb-copilot-message_history').innerHTML += messageTemplate;
                        jQuery(document).find(".twbb-user-message.twbb-copilot-message-row-empty .twbb-copilot-message-text").html(doc.data().user_input);
                        jQuery(document).find(".twbb-user-message.twbb-copilot-message-row-empty").removeClass("twbb-copilot-message-row-empty");
                    } else {
                        jQuery(document).find(".twbb-copilot-header-settings-menu-item.twbb-copilot-clear-chat").removeClass("twbb-copilot-header-settings-menu-item-inactive");
                        let agentResponse = self.convertPlaceholder( doc.data().agent_response );
                        agentResponse = self.converter.makeHtml(agentResponse);
                        let messageTemplate = jQuery(document).find("#twbb-copilot-message-assistant-template").html();
                        document.getElementById('twbb-copilot-message_history').innerHTML += messageTemplate;
                        jQuery(document).find(".twbb-ai-message.twbb-copilot-message-row-empty .twbb-copilot-message-text").html(agentResponse).attr("data-id", doc.id);
                        jQuery(document).find(".twbb-ai-message.twbb-copilot-message-row-empty").removeClass("twbb-copilot-message-row-empty");
                    }
                });
                if( !jQuery(document).find("#twbb-copilot-message_history .twbb-copilot-message-row").length ) {
                    jQuery(document).find(".twbb-copilot-no-widget-container").css("display", "flex");
                }

                let last_ai_message = jQuery(document).find("#twbb-copilot-message_history .twbb-ai-message:last-child");
                let last_ai_message_id = last_ai_message.find(".twbb-copilot-message-text").attr("data-id");
                self.run_id = last_ai_message_id;
               /* Check if the message already has feedback and ignore */
                let feedback_id = self.getCookie('twbb_coPilotFeedback-' + twbb_chat.pageId)
                if( feedback_id != last_ai_message_id ) {
                    let feedbackTemplate = jQuery(document).find("#twbb-copilot-feedback-template").html();
                    last_ai_message.after(feedbackTemplate);
                }

                /* Remove eye if there is no widgetId in the cookie or widget absent */
                let coPilotLastWidgetId = self.getCookie("twbb_coPilotLastWidgetId-"+twbb_chat.pageId);
                let coPilotLastWidgetContainer = window.$e.components.get('document').utils.findContainerById(coPilotLastWidgetId);
                if( !coPilotLastWidgetId || typeof coPilotLastWidgetContainer === 'undefined' ) {
                    jQuery(document).find(".twbb-copilot-feedback-eye").remove();
                }
            });

            self.db.collection("domains").doc(twbb_chat.domainId).collection('messages')
                .where("page_id", "==", twbb_chat.pageId)
                .orderBy("timestamp")
                .onSnapshot((snapshot) => {
                    snapshot.docChanges().forEach((change) => {
                        if (change.type === 'added' && !self.existingDocs.includes(change.doc.id)) {
                            self.addChatDate(change.doc.data().timestamp);
                            self.log("New message: ", change.doc.data());
                            if (change.doc.data().bot == 0) {
                                self.addChatMsg(0, change.doc.data().user_input);
                                if(self.inProgress) {
                                    let loadingTemplate = jQuery(document).find("#twbb-copilot-request-loading-template").html();
                                    jQuery("#twbb-copilot-message_history").append(loadingTemplate);
                                    let widgetTitle = '';
                                    if ( Object.keys(self.activeWidget).length ) {
                                        widgetTitle = self.activeWidget.model.getTitle();
                                    } else {
                                        jQuery(document).find(".twbb-copilot-request-loading .twbb-copilot-on").hide();
                                    }
                                    jQuery(document).find(".twbb-copilot-request-loading .twbb-copilot-request-widgetName").text(widgetTitle);

                                    jQuery(document).find("#twbb-copilot-chat_container").addClass("twbb-copilot-inprogress");
                                    jQuery("#twbb-copilot-chat_button").addClass("twbb-copilot-chat_button-inactive");
                                    self.chatScrollBottom();
                                }
                            } else {
                                self.coPilotResponse = change.doc.data();
                                if( change.doc.data().response_data["actions"].length ) {
                                    self.doActions(change.doc.data());
                                } else {
                                    self.finishCoPilotJob();
                                    self.addChatMsg(1, self.coPilotResponse.agent_response, self.coPilotResponse.run_id);
                                }
                            }
                        }
                    });
                });
        });
    }

    addChatDate(timestamp) {
        let self = this;
        let date = new Date(timestamp * 1000);

        // Format the date as DD.MM.YYYY
        let day = String(date.getDate()).padStart(2, '0');
        let month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        let year = date.getFullYear();

        let formattedDate = day + '.' + month + '.' + year;

        // Print the date only once per day
        if (formattedDate !== self.previousDate) {
            jQuery("#twbb-copilot-message_history").append("<span class='twbb-copilot-date'>"+formattedDate+"</span>");
            self.previousDate = formattedDate; // Update previous date to current
        }
    }

    finishCoPilotJob() {
        let self = this;
        self.inProgress = 0;
        let preview_iframe = window.parent.jQuery('#elementor-preview-iframe').contents();
        preview_iframe.find('.twbb_ask_to_ai_submit_button').removeClass('twbb_ai_loading');
        preview_iframe.find('.twbb-fast-editor-tools-container').removeClass('twbb_disabled_loading');

        jQuery(document).find("#twbb-copilot-chat_button").removeClass("twbb-copilot-chat_button-loading");
        jQuery(document).find("#twbb-copilot-chat_container").removeClass("twbb-copilot-inprogress");
        jQuery(document).find("#twbb-copilot-main-icon").removeClass("twbb-copilot-main-icon-loading").addClass("twbb-copilot-main-icon-notif");
        jQuery(document).find(".twbb-copilot-request-loading").remove();

        /* Scroll to new added element in case of no widget selected */
        if ( Object.keys(self.activeWidget).length && self.newAddedWidgetModelId != 0 ) {
            setTimeout(function() {
               self.scrollToWidget(self.newAddedWidgetModelId);
            }, 500);

        }
    }

    chatScrollBottom() {
        jQuery(document).find('#twbb-copilot-message_history').animate({
            scrollTop: jQuery('#twbb-copilot-message_history')[0].scrollHeight
        }, 1000);
    }

    showActiveWidgetInfo() {
        let widgetTitle = this.activeWidget.model.getTitle();
        let text = '';
        if( this.activeWidget.model.attributes.elType === 'widget' ) {
            text = widgetTitle;
        } else {
            if( widgetTitle === 'container' ) {
                text = widgetTitle;
            } else {
                text = 'container';
            }

        }
        jQuery(document).find("#twbb-copilot-info-row .twbb-copilot-info-name-widget").text(text);
        jQuery(document).find("#twbb-copilot-info-row .twbb-copilot-chosen-widget").show();

        if( jQuery("#twbb-copilot-user_input").val() !== '' && !this.inProgress ) {
            jQuery("#twbb-copilot-chat_button").removeClass("twbb-copilot-chat_button-inactive");
        } else {
            jQuery("#twbb-copilot-chat_button").addClass("twbb-copilot-chat_button-inactive");
        }
        jQuery(document).find(".twbb-copilot-no-widget-container").hide();
    }

    showNoChosenWidgetInfo() {
        jQuery(document).find("#twbb-copilot-info-row .twbb-copilot-chosen-widget").hide();
    }

    scrollToWidget(widgetID) {
        // Access the iframe that contains the Elementor preview
        let iframe = jQuery('#elementor-preview-iframe');

        if (iframe.length) {
            // Access the iframe's document
            let iframeContent = iframe.contents();

            // Find the widget element inside the iframe
            let $widgetElement = iframeContent.find('.elementor-element-' + widgetID);
            // Scroll the iframe's content to the widget's position if found
            if ($widgetElement.length) {
                let elementOffset = $widgetElement.offset().top;
                let scrollPosition = elementOffset - (jQuery(window).height() / 2) + ($widgetElement.outerHeight() / 2);

                iframeContent.find('html, body').animate({
                    scrollTop: scrollPosition
                }, 800); // 800ms for smooth scroll
            }
        }
    }

    registerEvents() {
        let self = this;

        elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
            self.activeWidget = window.$e.components.get('document').utils.findContainerById(model['id']);
            self.showActiveWidgetInfo();
        });

        elementor.hooks.addAction( 'panel/open_editor/container', function( panel, model, view ) {
            self.activeWidget = window.$e.components.get('document').utils.findContainerById(model['id']);
            self.showActiveWidgetInfo();
        });

        /* Listen every click on the body and update active widget data */
        jQuery("body").on("click", function(e) {
            jQuery(document).find("#twbb-copilot-chat_container").removeClass("twbb-copilot-feedback-hightlights-active");
            if ( !jQuery(e.target).hasClass("twbb-copilot-header-settings") ) {
                jQuery(document).find("#twbb-copilot-header .twbb-copilot-header-settings-menu-container").hide();
            }
            self.checkActiveWidget();
        })
        let preview_iframe = window.parent.jQuery('#elementor-preview-iframe').contents()
        preview_iframe.find("body").on("click", function() {
            jQuery(document).find("#twbb-copilot-chat_container").removeClass("twbb-copilot-feedback-hightlights-active");
            jQuery(document).find("#twbb-copilot-header .twbb-copilot-header-settings-menu-container").hide();
            self.checkActiveWidget();
        })


        jQuery(document).on("click",".twbb-copilot-tour-button", function() {
            jQuery(document).find(".twbb-copilot-tour-layer, .twbb-copilot-tour-container").remove();
            jQuery(document).find("#twbb-copilot-main-icon").trigger("click");
        });

        jQuery(document).on("click","#twbb-copilot-chat_button", function() {
            if(jQuery(this).hasClass("twbb-copilot-chat_button-inactive") || self.inProgress ) {
                return;
            }
            self.coPilotResponsePrinted = 0;

            self.getDataToSend();
        });

        /* Catch Ctrl+C and copy text from chat to buffer */
        jQuery(document).on('keydown', function(e) {
            if (e.ctrlKey && e.key === 'c') {
                var selection = window.getSelection();
                var selectedText = selection.toString();  // Get selected text

                if (selectedText.length > 0) {
                    // Check if the selected text is within a specific div
                    var selectedElement = selection.anchorNode.parentElement;
                    if (jQuery(selectedElement).closest('#twbb-copilot-chat_container').length > 0) {
                        e.preventDefault();
                        navigator.clipboard.writeText(selectedText).then(function () {
                        }).catch(function (err) {
                        });
                    }
                }
            }
        });

        jQuery(document).find("#twbb-copilot-user_input").keydown(function(event) {
            if( event.key === "Enter" ) {
                if (event.shiftKey) {
                    event.preventDefault();

                    let textarea = this;
                    let start = textarea.selectionStart;
                    let end = textarea.selectionEnd;

                    // Insert the new line character (\n)
                    let value = jQuery(textarea).val();
                    jQuery(textarea).val(value.substring(0, start) + "\n" + value.substring(end));

                    // Move the cursor to the next line
                    textarea.selectionStart = textarea.selectionEnd = start + 1;
                    jQuery('#twbb-copilot-user_input').trigger("input");
                } else {
                    event.preventDefault();
                    jQuery(document).find("#twbb-copilot-chat_button").trigger("click");

                }
            }
        });

        jQuery(document).find(".twbb-copilot-feedback-reason-description").keydown(function(event) {
            if( event.key === "Enter" ) {
                if (event.shiftKey) {
                    event.preventDefault();

                    let textarea = this;
                    let start = textarea.selectionStart;
                    let end = textarea.selectionEnd;

                    // Insert the new line character (\n)
                    let value = jQuery(textarea).val();
                    jQuery(textarea).val(value.substring(0, start) + "\n" + value.substring(end));

                    // Move the cursor to the next line
                    textarea.selectionStart = textarea.selectionEnd = start + 1;
                    jQuery('.twbb-copilot-feedback-reason-description').trigger("input");
                    // Scroll to make the cursor visible
                    setTimeout(() => {
                        textarea.scrollTop = textarea.scrollHeight;
                    }, 0);
                } else {
                    event.preventDefault();
                    jQuery(document).find(".twbb-copilot-feedback-reason-button").trigger("click");

                }
            }
        });

        jQuery(document).on("click",".twbb-copilot-send-feedback-button", function() {
            self.send_feedback(self.run_id);
        });

        jQuery(document).on("input","#twbb-copilot-user_input", function() {
            self.autoResize(this);
        });

        jQuery(document).on("click","#twbb-copilot-main-icon", function() {
            self.openChat();
        });

        jQuery(document).on("click",".twbb-copilot-header-minimize", function() {
            self.closeChat();
        });

        jQuery(document).on("click",".twbb-copilot-feedback-bad", function(e) {
            e.preventDefault();
            jQuery(this).closest(".twbb-copilot-feedback-bad-container").addClass("twbb-copilot-feedback-active");
            let height = jQuery("#twbb-copilot-footer").outerHeight();
            jQuery(document).find(".twbb-copilot-feedback-reason-container").css({
                'bottom': height + 64 + 'px',
            });

            jQuery(document).find(".twbb-copilot-feedback-reason-container").show();
        });

        jQuery(document).find(".twbb-copilot-feedback-reason-description").on("input", function (){
            if( jQuery(this).val() === '' ) {
                jQuery(document).find(".twbb-copilot-feedback-reason-button").addClass("twbb-copilot-feedback-reason-button-deactive");
            } else {
                jQuery(document).find(".twbb-copilot-feedback-reason-button").removeClass("twbb-copilot-feedback-reason-button-deactive");
            }
        });

        jQuery(document).on("click",".twbb-copilot-feedback-reason-item", function() {
            let reason = jQuery(this).text();
            let val = jQuery(document).find(".twbb-copilot-feedback-reason-description").val();
            jQuery(document).find(".twbb-copilot-feedback-reason-description").val(val + ' ' +reason);
            jQuery(this).closest(".twbb-copilot-feedback-reason-container").find(".twbb-copilot-feedback-reason-button-deactive").removeClass("twbb-copilot-feedback-reason-button-deactive");
        });

        jQuery(document).on("click",".twbb-copilot-feedback-reason-button", function() {
            if( jQuery(this).hasClass("twbb-copilot-feedback-reason-button-deactive") ) return;

            let reason = jQuery(document).find(".twbb-copilot-feedback-reason-description").val();
            jQuery(document).find(".twbb-copilot-feedback-bad-container").addClass("twbb-copilot-feedback-inprogress");
            jQuery(document).find(".twbb-copilot-feedback-reason-description").val('');
            jQuery(document).find(".twbb-copilot-feedback-reason-container").hide();
            self.send_feedback(self.run_id, 0, reason);
        });

        jQuery(document).on("click",".twbb-copilot-feedback-good", function() {
            let reason = 'Good clicked';
            jQuery(document).find(".twbb-copilot-feedback-good").addClass("twbb-copilot-feedback-inprogress");
            self.send_feedback(self.run_id, 1, '');
        });

        jQuery(document).on("click",".twbb-copilot-feedback-reason-close", function(e) {
            e.preventDefault();
            jQuery(document).find(".twbb-copilot-feedback-reason-description").val('');
            jQuery(this).closest(".twbb-copilot-feedback-reason-container").hide();
            jQuery(document).find(".twbb-copilot-feedback-active").removeClass("twbb-copilot-feedback-active");
        });


        jQuery(document).find("#twbb-copilot-message_history").on("scroll", function (){
            jQuery(document).find(".twbb-copilot-feedback-reason-description").val('');
            jQuery(document).find(".twbb-copilot-feedback-reason-container").hide();
        });


        jQuery(document).on("click",".twbb-copilot-info-action", function(e) {
            e.preventDefault();
            // Get the widget ID from the model
            self.scrollToWidget(self.activeWidget['id']);
        });

        jQuery(document).on("click",".twbb-copilot-clear-chat", function(e) {
            e.preventDefault();
            if( !jQuery(this).hasClass("twbb-copilot-header-settings-menu-item-inactive") ) {
                self.clearChat(jQuery(this));
            }
        });

        jQuery(document).on("click",".twbb-copilot-feedback-eye, .twbb-copilot-request-widgetName", function(e) {
            e.preventDefault();
            let scrollToWidgetId;
            if ( Object.keys(self.inProgressWidget).length ) {
                scrollToWidgetId = self.inProgressWidget['id'];
            } else if( self.newAddedWidgetModelId !== 0) {
                scrollToWidgetId = self.newAddedWidgetModelId;
            } else {
                scrollToWidgetId = self.getCookie("twbb_coPilotLastWidgetId-"+twbb_chat.pageId);
            }
            self.scrollToWidget(scrollToWidgetId);
        });

        jQuery('#twbb-copilot-chat_button').mouseover(function() {
            if( jQuery("#twbb-copilot-user_input").val() != '' ) {
                jQuery(".twbb-copilot-inprogress #twbb-copilot-footer .twbb-copilot-inprogress-message-container").show();
            }
        });

        // Mouseout event
        jQuery('#twbb-copilot-chat_button').mouseout(function() {
            jQuery("#twbb-copilot-footer .twbb-copilot-inprogress-message-container").hide();
        });

        jQuery(document).on("click",".twbb-copilot-respons-link", function(e) {
            e.preventDefault();
            self.openWidgetSetting(this);
        });

        jQuery(document).on("click",".twbb-copilot-image-item", function(e) {
            e.preventDefault();
            clearInterval(self.changeInterval);
            self.changeTourImage(this);
            self.runAutoChange();
        });

        jQuery(document).on("click","#twbb-copilot-header .twbb-copilot-header-settings", function(e) {
            e.preventDefault();
            if( jQuery(this).find(".twbb-copilot-header-settings-menu-container").is(":visible") ) {
                jQuery(this).find(".twbb-copilot-header-settings-menu-container").hide();
            } else {
                jQuery(this).find(".twbb-copilot-header-settings-menu-container").show();
            }

        });
        let twbb_elementor_preview_iframe = jQuery('#elementor-preview-iframe').contents();
        twbb_elementor_preview_iframe.on('click', function(evt) {
            var target = jQuery( evt.target );
            if( !target.is('.twbb-more-tool') && jQuery('.dialog-widget.dialog-simple-widget.elementor-context-menu').length > 0
                &&  jQuery('.dialog-widget.dialog-simple-widget.elementor-context-menu').hasClass('twbb-contextmenu-opened') ) {
                twbb_elementor_preview_iframe.find('.twbb-more-tool').removeClass('twbb-more-tool-active');
                jQuery('.dialog-widget.dialog-simple-widget.elementor-context-menu').removeClass('twbb-contextmenu-opened');
            }
        });
    }

    openWidgetSetting(that) {
        let widget_id = jQuery(that).attr("data-widget_id");
        if( !widget_id ) {
            return false;
        }
        let control = jQuery(that).attr("data-control");
        let preview_iframe = window.parent.jQuery('#elementor-preview-iframe').contents()
        let widget = preview_iframe.find('.elementor-element-' + widget_id);

        let container = window.$e.components.get('document').utils.findContainerById(widget_id);
        if( typeof container.controls === 'undefined' || typeof container.controls[control] === 'undefined') {
            return false;
        }

        let tab = container.controls[control]['tab'];
        let section = container.controls[control]['section'];

        if (widget.length) {
            widget.trigger('click'); // Simulate a click on the widget to activate it
        }

        setTimeout(function() {
            /* Open tab */
            jQuery(document).find(".elementor-tab-control-"+tab).trigger("click");
        }, 500);

        setTimeout(function() {
            let sectionEl = jQuery(document).find(".elementor-control-"+section);
            if( !sectionEl.hasClass("e-open") ) {
                /* Open section */
                jQuery(document).find(".elementor-control-" + section).trigger("click");
            }

            jQuery(document).find('.elementor-control-' + control)[0].scrollIntoView({
                block: 'center',    // Align to the center of the viewport
            });
        }, 500);

    }

    /**
     *  Function is firing on body click and check if there is active widget
     *  timeout need as some actions take time while widget become deactivate like Settings
     */
    checkActiveWidget() {
        let self = this;
        setTimeout(function(){
            var panelView = elementor.getPanelView();
            var pageView = panelView.getCurrentPageView();

            if (pageView && pageView.model && pageView.model.get('elType') === 'widget' ||
                pageView && pageView.model && pageView.model.get('elType') === 'container') {
                self.activeWidget = window.$e.components.get('document').utils.findContainerById(window.$e.components.get("panel/editor").activeModelId);
                self.showActiveWidgetInfo();
            } else {
                self.activeWidget = {};
                self.showNoChosenWidgetInfo();
            }

        },1000);
    }

    /**
     *  Function is firing on clear chat menu item click and send ajax request to clear history
     *
     *  @params that is menu item object
     */
    clearChat(that) {
        jQuery(that).addClass("twbb-copilot-header-settings-menu-item-inactive");
        jQuery(document).find("#twbb-copilot-message_history").empty();
        jQuery.ajax({
            url: twbb_chat.clearChatUrl,
            headers: {
                Accept: "application/x.10webaiassistantapi.v1+json",
                Authorization: "Bearer " + twbb_chat.accessToken
            },
            method: 'DELETE',
            contentType: 'application/json; charset=utf-8',
            success: function (response) {
                jQuery(document).find("#twbb-copilot-message_history").empty();
                /* Remove cookies */
                document.cookie = "twbb_coPilotFeedback-" + twbb_chat.pageId + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "twbb_coPilotLastWidgetId-" + twbb_chat.pageId + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            },
            error: function (error) {
            }
        });

    }

    /**
     * The function is resizing user chat area according to the inputted text height
     */
    autoResize(that) {
        if( jQuery(that).val() !== '' && !this.inProgress ) {
            jQuery("#twbb-copilot-chat_button").removeClass("twbb-copilot-chat_button-inactive");
        } else {
            jQuery("#twbb-copilot-chat_button").addClass("twbb-copilot-chat_button-inactive");
        }

        jQuery("#twbb-copilot-user_input").removeAttr('style');
        let scrollHeight = parseInt(that.scrollHeight);
        jQuery("#twbb-copilot-user_input").css('height', scrollHeight + 'px');

        if( scrollHeight < 226 ) {
            let chatContainer = jQuery("#twbb-copilot-footer");
            chatContainer.css('height', 'auto');
            chatContainer.css('height', scrollHeight + 30 + 'px');

            let messageContainer = jQuery("#twbb-copilot-message_history");
            messageContainer.css('height', 'auto');
            messageContainer.css('height', 274 - scrollHeight + 30 + 'px');

            let emptyMessageContainer = jQuery(".twbb-copilot-no-widget-container");
            emptyMessageContainer.css('height', 'auto');
            emptyMessageContainer.css('height', 274 - scrollHeight + 18 + 'px');
        }
    }

    getDataToSend(user_input = null) {
        let self = this;
        self.coPilotResponsePrinted = 0;
        window.parent.$e.data.get('globals/index').then(function (_ref) {
            let widget_name = '';
            let elementor_tree = {};
            if ( Object.keys(self.activeWidget).length ) {
                widget_name = self.activeWidget.label;
                self.inProgressWidget = self.activeWidget;
                elementor_tree = new TWBCElementorTree(self.inProgressWidget).getTree();

            } else {
                self.activeWidget = {};
                self.inProgressWidget = {};
            }

            const now = new Date();
            let data = {
                "user_input": document.querySelector('#twbb-copilot-user_input').value,
                "elementor_tree": elementor_tree,
                "device_mode": TWBCUtils.getDeviceMode(),
                "kit": TWBCUtils.getElementorKit(_ref.data),
                "bot": 0,
                "domain_id": twbb_chat.domainId,
                "page_id": twbb_chat.pageId,
                "wp_user_id": twbb_chat.wpUserId,
                "workspace_id": twbb_chat.workspaceId,
                "timestamp":  Math.floor(Date.UTC(
                    now.getUTCFullYear(),
                    now.getUTCMonth(),
                    now.getUTCDate(),
                    now.getUTCHours(),
                    now.getUTCMinutes(),
                    now.getUTCSeconds(),
                    now.getUTCMilliseconds()
                ) / 1000),
                "thread_id": twbb_chat.workspaceId.toString() + twbb_chat.domainId.toString() + twbb_chat.pageId.toString(),
                "widget_name": widget_name,
                "theme": twbb_chat.theme,
                "rest_url": twbb_chat.restURL,
            }
            if(user_input === null){
                data.user_input = document.querySelector('#twbb-copilot-user_input').value;
            }else{
                data.user_input = user_input;
            }
            self.inProgress = 1;
            self.newAddedWidgetModelId = 0;
            jQuery(document).find(".twbb-copilot-no-widget-container").hide();
            jQuery(document).find("#twbb-copilot-chat_button").addClass("twbb-copilot-chat_button-inactive");
            jQuery(document).find("#twbb-copilot-chat_button").addClass("twbb-copilot-chat_button-loading");
            jQuery(document).find(".twbb-copilot_chat_feedback").remove();
            document.querySelector('#twbb-copilot-user_input').value = "";
            jQuery('#twbb-copilot-user_input').trigger("input");

            try {
                if(twbb_chat.testModeApi) {
                    self.dataSendToAPi(data);
                } else {
                    data["elementor_tree"] = JSON.stringify(data["elementor_tree"]);
                    data["kit"] = JSON.stringify(data["kit"]);
                    const docRef = self.db.collection("domains").doc(twbb_chat.domainId).collection('messages').add(data);
                }

            } catch (e) {
                self.inProgress = 0;
                jQuery(document).find("#twbb-copilot-chat_button").removeClass("twbb-copilot-chat_button-loading");
            }

        }).catch(function (e) {
            jQuery(document).find("#twbb-copilot-chat_container").removeClass("twbb-copilot-inprogress");
            self.inProgress = 0;
            jQuery(document).find("#twbb-copilot-chat_button").removeClass("twbb-copilot-chat_button-loading");
        });
    }

    dataSendToAPi(data) {
        this.converter = new showdown.Converter();
        let self = this;
        data["want_response"] = 1;
        jQuery.ajax({
            url: 'http://127.0.0.1:5071/api/copilot/run',  // Replace with your URL,
            headers: {
                Accept: "application/x.10webaiassistantapi.v1+json",
            },
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            success: function (response) {
                self.log('Success API Call:', response);
                if( response["response"]["actions"]) {
                    self.coPilotResponse = {
                        "response_data": {"actions": response["response"]["actions"]},
                        "agent_response": response["response"]["agent_response"],
                        "run_id": ""
                    };
                    self.doActions(self.coPilotResponse);
                }
            },
            error: function (error) {
                console.log('Error:', error);
            }
        });
    }

    /**
     *  The function fire actions got from coPilot as response
     *
     *  @params data is object which is coming from coPilot response
     */
    doActions(data) {
        let self = this;
        let actions = data.response_data["actions"];

        for(let i = 0; i < actions.length; i++) {
            if (typeof actions[i] == "string") {
                actions[i] = JSON.parse(actions[i]);
            }
        }

        let upload_image_actions = [];
        for (let action of actions) {
            if (action["name"] === "upload_image") {
                upload_image_actions.push(action["args"]);
            }
        }

        Promise.all(upload_image_actions.map(self.uploadImage))
            .then(results => {
                let image_placeholders_data = {};
                results.forEach(({generated_image_data, response}) => {
                    if(response["success"] !== true){
                        return;
                    }

                    image_placeholders_data[generated_image_data["uid"]] = {
                        "media_id": response["data"]["id"],
                        "media_url": response["data"]["url"],
                        "generated_image_url": generated_image_data["image_url"]
                    }
                });

                if(image_placeholders_data) {
                    self.replaceImagePlaceholders(image_placeholders_data, actions);
                }

                this.doMainActions(data, actions);
            })
            .catch(error => {
                self.coPilotError();
                self.finishCoPilotJob();
            });
    }


    doMainActions( data, actions ) {
        let self = this;
        let actionExists = false;
        let actions_with_callbacks = {
            "update_elementor_tree": TWBCUpdateElementorTree,
            "image_generation": self.imageGeneration,
            "image_editing": self.imageGenerationEdit,
            "image_upscaling": self.imageGenerationEdit,
            "image_expansion": self.imageGenerationEdit,
            "image_variation_generation": self.imageGenerationEdit,
            "background_removal": self.imageGenerationEdit,
            "image_actions": self.imageAction,
            "text_modifications": self.textModifications,
            "advanced_text_modifications": self.textModifications,
            "section_generation": self.sectionGeneration,
            "open_globals": self.openGlobals,
            "open_settings": self.openSettings,
            "change_visibility": self.changeWpPostVisibility,
            "clone_post": self.cloneWpPost,
            "create_post": self.createWpPost,
        }

        for (let action of actions) {
            if(typeof action == "string") {
                action = JSON.parse(action);
            }
            let callback = actions_with_callbacks[action['name']];

            if (!callback) {
                continue;
            }

            let response;
            if (TWBCUtils.isClass(callback)) {
                actionExists = true;
                try {
                    response = (new callback(action['args'], self.inProgressWidget, '')).run();
                } catch (e) {
                    self.coPilotError();
                    self.finishCoPilotJob();
                }


                self.addChatMsg(1, data.agent_response, data.run_id);
                self.finishCoPilotJob();
            } else {
                actionExists = true;
                try {
                    response = callback(action, self);
                } catch (e) {
                    self.coPilotError();
                    self.finishCoPilotJob();
                }
            }
        }

        if( !actionExists ) {
            self.addChatMsg(1, data.agent_response, data.run_id);
            self.finishCoPilotJob();
        }
    }


    convertPlaceholder( text ) {
        const self = this;
        text = self.replaceMenuPlaceholder(text)

        const regex = /\{twbb_open_[^}]*\}/g; // Matches the entire pattern inside curly braces
        const matches = text.match(regex);

        if( !matches || !matches.length ) {
            return text;
        }
        let response = text;
        let html;
        matches.forEach((value, index) => {
            html = '';
            const result = value.replace(/\{twbb_open_|}/g, '');

            const parts = result.split('_');

            // Rejoin everything after the first split
            const part1 = parts[0]; // "cc4eca5"
            const part2 = parts.slice(1).join('_'); // "title_color"

            let widget_id = part1;
            let control = part2;

            let controlTitle = "Adjust manually";
            let container = window.$e.components.get('document').utils.findContainerById(widget_id);
            if( typeof container !== 'undefined' && typeof container.controls !== 'undefined' && typeof container.controls[control] !== 'undefined' ) {
                if(!container.controls[control]['label']){
                    controlTitle = self.controlTitleFormControlName(control);
                }else{
                    controlTitle = container.controls[control]['label'];
                }
            }

            if( index === 0 ) {
                html = "<span class='twbb-copilot-adjust-manually-title'>Adjust manually:</span>";
                html += "<div class='twbb-copilot-respons-link-container'>";
            }
            html += "<span class='twbb-copilot-respons-link' data-widget_id='"+widget_id+"' data-control='"+control+"'>"+controlTitle+"</span>";
            if( index === (matches.length-1) ) {
                html += "</div>";
            }
            response = response.replace(value,html);
        });

        return response;
    }

    openGlobals(data, that) {
        let customize_button = jQuery(document).find(".twbb-customize-button");
        if( !customize_button.hasClass("selected") ) {
            customize_button.trigger("click");
        }

        self.addChatMsg(1, data.agent_response, data.run_id);
        that.finishCoPilotJob();
    }

    openSettings(data, that) {
        let settings_button = jQuery(document).find("button[value='Site Settings']");
        if( !settings_button.hasClass("Mui-selected") ) {
            settings_button.trigger("click");
        }
        self.addChatMsg(1, data.agent_response, data.run_id);
        that.finishCoPilotJob();
    }


    changeWpPostVisibility(data, that) {
        let args = data['args'];
        let visibility = args['visibility'] ?? null;
        let post_id = args['post_id'] ?? null;
        let title = args['title'] ?? null;
        let post_type = args['post_type'] ?? null;
        let dataToSend = {
            "action": "twbb_copilot_change_wp_post_visibility",
            "visibility": visibility,
            "id": post_id,
            "title": title,
            "post_type": post_type,
            "twbb_cop_nonce": twbb_chat.twbb_cop_nonce
        };

        console.log('that', that)
        this.sendCopilotAjaxRequest(dataToSend, that);
    }

    cloneWpPost(data, that) {
        let args = data['args'];
        let post_id = args['post_id'] ?? null;
        let title = args['title'] ?? null;
        let post_type = args['post_type'] ?? null;
        let dataToSend = {
            "action": "twbb_copilot_clone_wp_post",
            "id": post_id,
            "title": title,
            "post_type": post_type,
            "twbb_cop_nonce": twbb_chat.twbb_cop_nonce
        };

        this.sendCopilotAjaxRequest(dataToSend, that);
    }

    createWpPost(data, that) {
        let args = data['args'];
        let post_data = args['data'];
        let post_type = args['post_type'] ?? null;
        let dataToSend = {
            "action": "twbb_copilot_create_wp_post",
            "data": post_data,
            "post_type": post_type,
            "twbb_cop_nonce": twbb_chat.twbb_cop_nonce
        };

        this.sendCopilotAjaxRequest(dataToSend, that);
    }
    /**
     *  The function generating section
     *
     *  @params args is object which is comming in the response
     */
    sectionGeneration(data, that) {
        let args = data['args'];
        let sectionResponse = args['section_data'];
        let add_section_at = -1;
        if( sectionResponse.status === 'success' ) {
            jQuery.ajax({
                type: 'POST',
                url: twbb_sg_editor.ajaxurl,
                dataType: 'json',
                data: {
                    "nonce": twbb_chat.twbb_sg_nonce,
                    "action": "twbb_process_generation_response",
                    "data": sectionResponse
                }
            }).success(function(res){
                if( res.data['status'] === 'success' ) {
                    window.parent.import_twbb_generated_template(add_section_at, res.data['params']);
                    that.finishCoPilotJob();
                    that.addChatMsg(1, that.coPilotResponse.agent_response, that.coPilotResponse.run_id);
                }

            }).error(function () {
                that.coPilotError();
                that.finishCoPilotJob();
            });
        } else {
            that.coPilotError();
            that.finishCoPilotJob();
        }
    }

    /**
     * The function is sending request for text modification
     *
     * @params args object which includes action type, ex change_tone
     * @params that object class
     */
    textModifications( data, that ) {
        let args = data['args'];
        let self = that;
        let setting_name = args['setting_name'];
        let settings = self.inProgressWidget.settings.attributes;
        let setting_value = settings[setting_name];
        let params = {};
        let action = args['action_name'];
        switch (args['action_name']) {
            case 'change_tone':
                params = {"text": setting_value, "tone": args['tone']};
                break;
            case 'translate_to':
                params = {"text": setting_value, "tone": args['language']};
                break;
            case 'new_prompt':
                params = {"text": args["action_param"]}
                break;
            default:
                params = {"text": setting_value}
        }

        let ob;
        if( typeof window.parent.restRequestInstance == 'function' ) {
            ob = window.parent.restRequestInstance("builder/" + action, params, "POST", function (success) {
                let output = success['data']['output'];
                if( output !== '' ) {
                    let args = {
                        "tree": [
                            {
                                "id": self.inProgressWidget['id'],
                                "type": self.inProgressWidget['label'],
                                "settings": {
                                    [setting_name]: output,
                                }
                            }
                        ]
                    }
                    that.finishCoPilotJob();
                    that.addChatMsg(1, that.coPilotResponse.agent_response, that.coPilotResponse.run_id);

                    let result = (new TWBCUpdateElementorTree(args, that.inProgressWidget, 'image_generation')).run();
                }
            }, function (err) {
                that.coPilotError();
                that.finishCoPilotJob();
            }, function (err) {
                that.coPilotError();
                that.finishCoPilotJob();
            });
            ob.twbb_send_rest_request(false);
        }
    }

    imageAction( data, that ){
        let args = data['args'];
        let image_url = args['generated_image'];
        let setting_name = args['setting_name'];
        let node = args['node'];
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data:  {
                'action': 'twbb_use_image',
                'task': 'twbb_use_image',
                'nonce': twbb_img.ajaxnonce,
                'image': image_url
            },
            success: function (response){
                if( response.success ) {
                    if(setting_name == "background_image_url"){
                        node["settings"]["background_image"]["url"] = response.data.url;
                    } else if (setting_name == "_background_image_url") {
                        node["settings"]["_background_image"]["url"] = response.data.url;
                    } else if(setting_name == "image_url"){
                        node["settings"]["image"]["url"] = response.data.url;
                    } else if(setting_name == "testimonial_image_url"){
                        node["settings"]["testimonial_image"]["url"] = response.data.url;
                    }
                    that.log("Node", {"node": node, "image_url": image_url});
                    (new TWBCUpdateElementorTree({
                        "tree": [node],
                        "deleted_repeated_widgets": [],
                        "deleted_widgets_ids": [],
                    }, that.inProgressWidget, 'image_generation')).run();

                    that.finishCoPilotJob();
                    that.addChatMsg(1, that.coPilotResponse.agent_response, that.coPilotResponse.run_id);

                }
            },
            error: function (jqXHR, exception) {
                self.coPilotError();
                self.finishCoPilotJob();
                console.log(jqXHR);
            },

        });
    }


    uploadImage(generated_image_data) {
        return jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'twbb_use_image',
                'task': 'twbb_use_image',
                'nonce': twbb_img["ajaxnonce"],
                'image': generated_image_data["image_url"]
            }
        }).then(response => ({ generated_image_data, response }));
    }
    /**
     * The function is sending request for image generation
     *
     * @params args object which includes image description, style, ratio,..
     * @params that object class
     */
    imageGeneration( data, that ) {
        let args = data['args'];
        let image_description = args['description'];
        let image_style = args['image_style'];
        let aspect_ratio = args['aspect_ratio'];
        let is_background = args["is_widget_background"];
        let control_name = that.getMediaControl(is_background);
        let settings = that.inProgressWidget.model.getSetting(control_name);

        if( typeof settings.url === 'undefined' && control_name !== 'gallery' && control_name !== 'carousel' ) {
            self.coPilotError();
            self.finishCoPilotJob();
            return;
        }

        let emptyRatio = [null, '', 'null', 'none'];
        if( emptyRatio.includes(String(aspect_ratio).toLowerCase()) ) {
            let imageWidth, imageHeight;
            if( is_background ) {
                let iframeContent = jQuery('#elementor-preview-iframe').contents();
                // Find the widget element inside the iframe
                let widget = iframeContent.find('.elementor-element-' + that.inProgressWidget['id']);
                imageWidth = widget.width();
                imageHeight = widget.height();
                if( imageWidth != 0 && imageHeight != 0 ) {
                    let aspectRatio = parseFloat(imageWidth / imageHeight);
                    let imageRatioOb = new ImageRatio(aspectRatio);
                    aspect_ratio = imageRatioOb.process();
                }
                that.sendImageGenerationRequest( is_background, image_description, image_style, aspect_ratio, that );
            } else {
                const getMeta = (url, callback) => {
                    const img = new Image();
                    img.onload = () => callback(null, img);
                    img.onerror = (err) => callback(err);
                    img.src = url;
                };

                getMeta(settings.url, (err, img) => {
                    if( err ) {
                        aspect_ratio = 'Square (1:1)';
                    } else {
                        imageWidth = img.naturalWidth;
                        imageHeight = img.naturalHeight;
                        if (imageWidth != 0 && imageHeight != 0) {
                            let aspectRatio = parseFloat(imageWidth / imageHeight);
                            let imageRatioOb = new ImageRatio(aspectRatio);
                            aspect_ratio = imageRatioOb.process();
                        }
                    }
                    that.sendImageGenerationRequest( is_background, image_description, image_style, aspect_ratio, that );
                });
            }
        }
        else {
            that.sendImageGenerationRequest( is_background, image_description, image_style, aspect_ratio, that );
        }
    }

    imageGenerationEdit( data, that ) {
        let args = data['args'];
        let action_name = data['name'];
        let params = {}
        let description = '';
        let image;
        let current_action;
        let is_background = false;

        let control_name = that.getMediaControl( false );
        if( control_name === '' ) {
            control_name = that.getMediaControl( true );
            is_background = true;
        }
        let settings = that.inProgressWidget.model.getSetting(control_name);
        if( typeof settings['url'] == 'undefined' || settings['url'] === '' ) {
            that.coPilotError();
            that.finishCoPilotJob();
        }

        let current_image_url = settings['url'];

        switch (action_name) {
            case 'image_editing':
                current_action = 'image_edit';
                description = args['description'];
                image = current_image_url;
                params = {
                    'description': description,
                    'image': image,
                };

                break;
            case 'background_removal':
                current_action = 'image_remove_bg';
                image = current_image_url;
                params = {
                    'image': image,
                };
                break;
            case 'image_upscaling':
                current_action = 'image_upscale';
                image = current_image_url;
                let factor = args['factor'];
                params = {
                    'image' : image,
                    'factor' : factor
                };
                break;
            case 'image_variation_generation':
                current_action = 'image_variations';
                image = current_image_url;
                description = args['description'];
                let n_images = args['n_images'];
                params = {
                    'image' : image,
                    'description' : description,
                    'n_images': n_images,
                };
                break;
            case 'image_expansion':
                current_action = 'image_expand';
                let aspect_ratio = args['new_aspect_ratio'];
                image = current_image_url;
                params = {
                    'image' : image,
                    'aspect_ratio' : aspect_ratio,
                };
                break;
        }

        let session_id = (+new Date).toString(36);

        let staticParams = {
            'session_id': session_id,
            'action_type': 'builder_image',
            'existing_image_edit': 1,
        }
        Object.assign(params, staticParams);


        let ob = new RestRequest("builder_image/" + current_action, params, "POST", function (success) {
            let output = success['data']['output'];
            that.addImageToMediaLibrary(output, is_background, control_name);
        }, function (err) {
            that.coPilotError();
            that.finishCoPilotJob();
        }, function (err) {
            that.coPilotError();
            that.finishCoPilotJob();
        });
        ob.twbb_send_rest_request(false, 'builder_image');

    }

    sendImageGenerationRequest( is_background, image_description, image_style, aspect_ratio, that ) {
        let session_id = (+new Date).toString(36);
        let params = {
            'description': image_description,
            'image_style': image_style,
            'aspect_ratio' : aspect_ratio,
            'n_images': 1,
            'session_id': session_id,
            'action_type': 'builder_image',
            'existing_image_edit': 0,
        };

        let ob = new RestRequest("builder_image/image_generate", params, "POST", function (success) {
            let output = success['data']['output'];
            that.addImageToMediaLibrary(output, is_background, '');

        }, function (err) {
            that.coPilotError();
            that.finishCoPilotJob();
        }, function (err) {
            that.coPilotError();
            that.finishCoPilotJob();
        });
        ob.twbb_send_rest_request(false, 'builder_image');

    }

    /**
     * Add response image url to media library
     *
     * @params output object which includes version of image and types
     * @params is_background bool which got from coPilot
     */
    addImageToMediaLibrary( output, is_background, image_control_name ) {
        let self = this;
        if( typeof output != 'object' || Object.keys(output).length === 0 ) {
            self.coPilotError();
            self.finishCoPilotJob();
            return '';
        }
        let lastimage = Object.keys(output)[0];
        let lastVersion = Object.keys(output[lastimage]).pop();
        let image_url = output[lastimage][lastVersion]['original_image'];
        let data = {
            'action': 'twbb_use_image',
            'task': 'twbb_use_image',
            'nonce': twbb_img.ajaxnonce,
            'image': image_url
        }
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data:  data,
            success: function (response){
                if( response.success ) {
                    let post_id = response.data.id;
                    let url = response.data.url;
                    let control_name;
                    if( image_control_name === '' ) {
                        control_name = self.getMediaControl(is_background);
                    } else {
                        control_name = image_control_name;
                    }
                    self.setMediaToControl( url, post_id, control_name, is_background );
                }
            },
            error: function (jqXHR, exception) {
                self.coPilotError();
                self.finishCoPilotJob();
                console.log(jqXHR);
            },

        });
    }

    /**
     * The function update settings
     *
     * @params url string which is url the image which is already in media library
     * @params control_name string
     * @params is_background bool which got from coPilot
     * @params post_id integer media post id
     */
    setMediaToControl( url, post_id, control_name, is_background ) {
        let self = this;
        let settings = {};
        if( is_background ) {
            settings = {
                id: post_id,
                size: "",
                url: url
            }

        } else {
            let widget_with_galleries = ['twbb_gallery', 'image-carousel'];
            if( widget_with_galleries.includes(self.inProgressWidget.model.attributes.widgetType) ) {
                let currentSettings = self.inProgressWidget.getSetting(control_name);
                currentSettings.push({
                    id: post_id,
                    url: url
                });
                settings = currentSettings;
            } else {
                settings = {
                    alt: "",
                    id: post_id,
                    size: "",
                    source: "library",
                    url: url
                }
            }
        }

        self.finishCoPilotJob();
        self.addChatMsg(1, self.coPilotResponse.agent_response, self.coPilotResponse.run_id);

        if( control_name !== '' ) {
            let args = {
                "tree": [
                    {
                        "id": self.inProgressWidget['id'],
                        "type": self.inProgressWidget['label'],
                        "settings": {
                            [control_name]: settings,
                        }
                    }
                ]
            }
            let result = (new TWBCUpdateElementorTree(args, self.inProgressWidget, 'image_generation')).run();
        }

    }

    /**
     * The function find control name of media and update settings
     *
     * @params is_background bool which got from coPilot
     *
     * @return string media control name
     */
    getMediaControl( is_background ) {

        let self = this;

        let controls = self.inProgressWidget['controls'];
        let control_name = '';
        if( is_background ) {
            if( typeof self.inProgressWidget['controls']['_background_image'] !== 'undefined' ) {
                control_name = '_background_image';
            } else {
                control_name = 'background_image';
            }

        } else {
            for (const key in controls) {
                if ((controls[key].type === "media" || controls[key].type === "gallery") && controls[key].tab !== "advanced" ) {
                    control_name = key;
                    break;
                }
            }
        }
        return control_name;
    }

    /**
     * The function add message to chat area
     *
     * @params is_bot integer 0 is user 1 is coPilot
     * @params text string response text
     * @params run_id integer coPilot run id which is coming in the coPilot response
     */
    addChatMsg(is_bot, text, run_id){
        this.run_id = run_id;
        if ( is_bot ) {
            if ( !this.coPilotResponsePrinted ) {
                this.coPilotResponsePrinted = 1;
                text = text.replace(/\\n/g, '\n');
                text = this.convertPlaceholder( text );
                text = this.converter.makeHtml(text);
                if (text !== '') {
                    let messageTemplate = jQuery(document).find("#twbb-copilot-message-assistant-template").html();
                    document.getElementById('twbb-copilot-message_history').innerHTML += messageTemplate;
                    jQuery(document).find(".twbb-ai-message.twbb-copilot-message-row-empty .twbb-copilot-message-text").html(text);
                    let feedbackTemplate = jQuery(document).find("#twbb-copilot-feedback-template").html();
                    document.getElementById('twbb-copilot-message_history').innerHTML += feedbackTemplate;

                    jQuery(document).find(".twbb-copilot-message-row:last-child .twbb-ai-message .twbb-copilot-message-text").attr("data-id", run_id);

                    let lastWidgetId;
                    if ( Object.keys(this.inProgressWidget).length ) {
                        lastWidgetId = this.inProgressWidget['id'];
                    } else {
                        lastWidgetId = this.newAddedWidgetModelId;
                    }
                    this.setCookieAdvanced("twbb_coPilotLastWidgetId-"+twbb_chat.pageId, lastWidgetId, 365, '/' );
                    this.coPilotResponseCount++;
                    if( this.coPilotResponseCount === 3 && !this.checkCookieExists('coPilotFeedbackHighlights') ) {
                        this.setCookieAdvanced("coPilotFeedbackHighlights", "1", 365, "/");
                        let height = jQuery("#twbb-copilot-footer").outerHeight();
                        jQuery(document).find(".twbb-copilot-feedback-highlights-container").css({
                            'bottom': height + 64 + 'px',
                        });
                        jQuery(document).find("#twbb-copilot-chat_container").addClass("twbb-copilot-feedback-hightlights-active");
                    }
                }
            }
        } else{
            let messageTemplate = jQuery(document).find("#twbb-copilot-message-user-template").html();
            document.getElementById('twbb-copilot-message_history').innerHTML += messageTemplate;
            jQuery(document).find(".twbb-user-message.twbb-copilot-message-row-empty .twbb-copilot-message-text").html(text);
        }
        jQuery(document).find(".twbb-copilot-message-row-empty").removeClass("twbb-copilot-message-row-empty");


        document.querySelectorAll('#twbb-copilot-message_history')[0].lastElementChild.scrollIntoView()
        jQuery(document).find(".twbb-copilot-header-settings-menu-item.twbb-copilot-clear-chat").removeClass("twbb-copilot-header-settings-menu-item-inactive");
    }

    checkCookieExists(cname) {
        // Construct the search pattern
        const nameEQ = cname + "=";

        // Split the cookie string into an array and search for the cookie
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            if (cookies[i].trim().indexOf(nameEQ) === 0) {
                return true;
            }
        }

        return false;
    }

    setCookieAdvanced(name, value, days, path = "/", domain = "", secure = false) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }

        let cookie = name + "=" + encodeURIComponent(value) + expires + "; path=" + path;

        if (domain) {
            cookie += "; domain=" + domain;
        }

        if (secure) {
            cookie += "; secure"; // Sends the cookie over HTTPS only
        }

        document.cookie = cookie;
    }

    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null; // Return null if the cookie is not found
    }

    /**
     * The function fire ajax request and send user feedback
     *
     * @params run_id integer coPilot run id which is coming in the coPilot response
     * @params score integer 0 is bad feedback, 1 is good feedback
     * @params reason string user input in the description textarea
     */
    send_feedback(run_id, score, reason){
        /* set 4 means the value never been 3 and highlight popup will not open */
        this.coPilotResponseCount = 4;
        let data = {};
        data['user_feedback_score'] = score;
        data['user_feedback'] = reason;

        if(!data){
            return;
        }

        this.setCookieAdvanced('twbb_coPilotFeedback-' + twbb_chat.pageId, run_id, 365, "/");

        data['run_id'] = run_id;
        setTimeout( function() {
            jQuery(document).find(".twbb-copilot-feedback-good, .twbb-copilot-feedback-bad-container").remove();
        }, 2000);

        jQuery.ajax({
            url: twbb_chat.feedBackApi,
            headers: {
                Accept: "application/x.10webaiassistantapi.v1+json",
                Authorization: "Bearer " + twbb_chat.accessToken
            },
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function (response) {
            },
            error: function (error) {
            }
        });

    }

    replaceImagePlaceholders(image_placeholders_data, actions){
        let update_elementor_tree_action = null;
        for(let action of actions){
            if(action["name"] === "update_elementor_tree"){
                update_elementor_tree_action = action;
            }

        }

        if(update_elementor_tree_action === null){
            return;
        }

        let elementor_tree = JSON.stringify(update_elementor_tree_action["args"]["tree"]);

        const post_id_regex = /\{TAC_POST_ID_([A-Za-z0-9]+)\}/g;
        const media_url_regex = /\{TAC_MEDIA_URL_([A-Za-z0-9]+)\}/g;

        elementor_tree = elementor_tree.replace(post_id_regex, (match, uid) => {
            let media_id = "";

            if(image_placeholders_data[uid] && image_placeholders_data[uid]["media_id"]) {
                media_id = image_placeholders_data[uid]["media_id"];
            }

            return media_id;
        });

        elementor_tree = elementor_tree.replace(media_url_regex, (match, uid) => {
            let media_url = "";

            if(image_placeholders_data[uid] && image_placeholders_data[uid]["media_url"]) {
                media_url = image_placeholders_data[uid]["media_url"];
            }
            return media_url;
        });

        update_elementor_tree_action["args"]["tree"] = JSON.parse(elementor_tree);
    }

    coPilotError() {
        let self = this;
        self.addChatMsg(1, self.coPilotResponse.agent_response, self.coPilotResponse.run_id);
    }
    log(message, data) {
        if(twbb_chat.testMode) {
            console.log(message, data);
        }
    }

    sendCopilotAjaxRequest(dataToSend, _that) {
        console.log("_that", _that)
        jQuery.ajax({
            type: 'POST',
            url: twbb_chat.copilotAjaxUrl,
            dataType: 'json',
            data: dataToSend
        }).success(function(res){
            console.log(res)
            console.log("res that", _that)
            if( res.data['status'] === 'success' ) {
                _that.finishCoPilotJob();
                _that.addChatMsg(1, _that.coPilotResponse.agent_response, _that.coPilotResponse.run_id);
            }
        }).error(function () {
            _that.coPilotError();
            _that.finishCoPilotJob();
        });
    }

    controlTitleFormControlName(controlName){
        try {
            const sentence = controlName.replace(/[-_]/g, ' ').toLowerCase(); // Replace `-` and `_` with spaces and lowercase all
            return sentence.charAt(0).toUpperCase() + sentence.slice(1); // Capitalize the first letter
        }catch (e){
            return controlName;
        }
    }


    replaceMenuPlaceholder(text){
        const matches = Array.from(text.matchAll(/{twbb_menu_structure_modification_(.*?)}/gm));

        if(!matches || !matches.length ){
            return text;
        }

        let full_match = matches[0][0];
        let menu_name = matches[0][1];

        let menu_id = null;
        if(menu_name && !!twbb_chat.menu_name_id_mapping[menu_name]){
            menu_id = twbb_chat.menu_name_id_mapping[menu_name];
        }

        let path = "websites/"+twbb_chat.domainId+"/navigation";
        if(menu_id){
            path += "/edit-menu/" + menu_id;
        }
        let link = (new URL(path, twbb_chat.dashboardURL)).toString();

        let link_html = "<div class='twbb-copilot-respons-link-container'>" +
            "<a class='twbb-copilot-menu-editing-link' target='_blank' href='"+link+"'>Edit menu</a>" +
            "</div>";

        text = text.replace(full_match, link_html)

        return text;
    }
}

let coPilot;
jQuery(window).on('elementor:init', function() {
    var $iframe = jQuery('#elementor-preview-iframe');
    $iframe.on('load', function() {
        setTimeout(function() {
            coPilot = new TWBBCoPilot();
            coPilot.init();
            window.TWBBCoPilotInstance = coPilot;
        }, 3000)
    });
});
