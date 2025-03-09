jQuery(document).ready(function($) {
    var initSortable = function($element) {
        $element.find('.draggable-order-list').sortable({
            update: function(event, ui) {
                var datas = [];
                $element.find('.draggable-order-item').each(function() {
                    var data = {
                        id: $(this).data('id'),
                        label: $(this).find('.draggable-order-label').text(),
                    };
                    datas.push(data);
                });
                $element.find('input').val(JSON.stringify(datas)).trigger('input');
            }
        });
    };

    var onPanelOpen = function() {
        setTimeout(function() {
            jQuery('.draggable_order_control').each(function() {
                var $element = jQuery(this);
                if (!$element.data('sortable-initialized')) {
                    initSortable($element);
                    $element.data('sortable-initialized', true);
                }
            });

            showOrderItemsByMeta();

            jQuery(document).find('.elementor-control-classic_meta_data .elementor-select2').on('change', function() {
                showOrderItemsByMeta();
            })

        },500)
    };

    // Listen for the panel open event
    elementor.channels.editor.on('section:activated', onPanelOpen);
    elementor.channels.editor.on('panel:activated', onPanelOpen);
    // Initialize on document ready (in case the panel is already open)
    onPanelOpen();
});

/* Show/hide draggble items according to meta data select 2 value and changes */
function showOrderItemsByMeta() {
    jQuery(document).find('.draggable-order-item').each(function( index ) {
        let id = jQuery(this).find('.draggable-order-label').text();
        if( !jQuery(document).find('.select2-selection__choice[title="'+id+'"]').length ) {
            jQuery(this).hide();
        } else {
            jQuery(this).show();
        }
    });
}