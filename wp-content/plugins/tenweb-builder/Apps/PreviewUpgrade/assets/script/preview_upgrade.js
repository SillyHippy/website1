jQuery(document).ready(function () {
    jQuery(".twbb-pu-upgrade-close, .twbb-pu-upgrade-layout").on("click", function() {
        jQuery(".twbb-pu-upgrade-layout, .twbb-pu-upgrade-container").addClass("twbb-pu-hidden");
        jQuery(".twbb-pu-video-active").removeClass("twbb-pu-video-active");
        jQuery(".twbb-pu-video-item").first().addClass("twbb-pu-video-active");
    })

    jQuery(".twbb-pu-bottom-bar .twbb-pu-button").on("click", function() {
        jQuery(".twbb-pu-upgrade-layout, .twbb-pu-upgrade-container").removeAttr("style").removeClass("twbb-pu-hidden");
        twbb_pu_run_video( jQuery(".twbb-pu-video-active") );
    })

    jQuery(".twbb-pu-upgrade-videos li.twbb-pu-video-item").on("click", function () {
        twbb_pu_run_video(this);
    })

    jQuery(document).on("click", ".twbb-pu-upgrade-button",  function (e) {
        e.preventDefault();
        twbb_remove_sidebar();
    });

    jQuery(".twbb-pu-bottom-bar").show();

    /* Add border bottom ecommerce upgrade popup for mobile */
    jQuery(document).find(".twbb-pu-upgrade-right").on("scroll", function() {
        if( jQuery(this).scrollTop() > 20 ) {
            jQuery(document).find(".twbb-transparent-row").addClass("twbb-transparent-row-active");
        } else {
            jQuery(document).find(".twbb-transparent-row-active").removeClass("twbb-transparent-row-active");
        }
    });
})

function twbb_remove_sidebar() {
    let color = tbb_getCookie("twbb_theme_color");
    let font_family = tbb_getCookie("twbb_theme_font");
    let url = twbb_sidebar_vars.upgrade_url;
    if(color){
        url += "&color_pallet=" + color;
    }

    if(font_family){
        url += "&font_family=" + font_family;
    }

    jQuery.ajax({
        type: "POST",
        url: twbb_sidebar_vars.remove_sidebar_url,
        data: {
            nonce: twbb_sidebar_vars.nonce,
        }
    }).success(function () {
        window.location.href = url;
    }).error(function () {
        window.location.href = url;
    });
}

function tbb_getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function twbb_pu_run_video(that) {
    if( jQuery(window).width() < 769 ) {
        return;
    }
    twbb_pu_reset_video();
    jQuery(".twbb-pu-video-active").removeClass("twbb-pu-video-active");
    jQuery(".twbb-pu-video-item > span").removeClass("twbb-pu-hidden");

    jQuery(that).addClass("twbb-pu-video-active");
    let video_url = jQuery(that).data("video_url");
    let duration = parseInt(jQuery(that).data("video_duration"))+1;
    let index = jQuery(that).data("index");
    jQuery(".twbb-pu-upgrade-right source").attr("src", video_url);
    let video = jQuery(document).find(".twbb-pu-upgrade-right video");

    video[0].load();
    video.get(0).play();
    jQuery(that).find("svg circle").css('animation','countdown '+duration+'s linear infinite forwards');
    video.on("ended", function() {
        let next_video = jQuery(".twbb-pu-video-item[data-index="+(++index)+"]");
        if( !next_video.length ) {
            next_video = jQuery(".twbb-pu-video-item[data-index=0]");
        }
        twbb_pu_run_video(next_video)
    });
}

function twbb_pu_reset_video() {
    let video = jQuery(".twbb-pu-upgrade-right video");
    video[0].pause();
    video.unbind('ended');
}