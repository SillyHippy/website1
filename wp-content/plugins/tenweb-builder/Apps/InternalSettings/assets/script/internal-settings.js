jQuery(document).ready(function() {
    jQuery('.twbb_update_sections_in_uploads').on('click', function() {
        var data = {
            action: 'twbb_update_sections_in_uploads',
            nonce: twbb_internal_admin.nonce,
        };
        twbb_internal_ajax(data);
    });
    function twbb_internal_ajax(data){
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data
        }).success(function(res){
            if( res['success'] ) {
                setTimeout(function(){
                    twbb_internal_update_sg_posts_ajax();
                    },3000);
            }
        }).error(function (res) {
            alert('Something went wrong.');
        });
    }

    function twbb_internal_update_sg_posts_ajax() {
        var data = {
            action: 'twbb_update_sg_posts',
            nonce: twbb_internal_admin.nonce,
        };
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data
        }).success(function(res) {
            if (!jQuery('.twbb_update_success_message').length) {
                jQuery('.twbb_update_sections').append('<div class="twbb_update_success_message" style="color: green;">Sections are updated</div>');
            }
        }).error(function (res) {
            alert('Something went wrong when updating SG posts.');
        });
    }
});