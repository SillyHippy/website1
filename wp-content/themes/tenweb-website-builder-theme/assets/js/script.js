jQuery( window ).ready(function() {

    jQuery(document).on("click", "#cancel-comment-reply-link", function(){
        jQuery(this).closest("#comments").find(".comment-reply-link.disable").removeClass("disable");
        return false;
    });

    jQuery(document).on("click", ".comment-reply-link", function(){
        if (!jQuery(this).hasClass("disable")) {
            jQuery(this).addClass("disable");
        }
        return false;
    });
    jQuery("#comment_parent").after(jQuery("#reply-title"));
});