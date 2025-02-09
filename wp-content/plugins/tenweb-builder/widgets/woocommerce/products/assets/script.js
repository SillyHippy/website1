jQuery( window ).on( 'elementor/frontend/init', function () {

    image_gallery_run();
    image_gallery_second_image_run();

    jQuery(document).on('mouseenter', '.elementor-widget-twbb_woocommerce-products[data-widget_type="twbb_woocommerce-products.modern"].product_buttons_visibility__show_on_hover .product', function() {
        jQuery(this).find('.twbb-add_to_cart_container_open').css({'display':'flex'});
        let buttonsContainerHeight = parseInt(jQuery(this).find('.twbb-add_to_cart_container_open').outerHeight());
        jQuery(this).find('.product_info_div').css('bottom',buttonsContainerHeight + 'px');
    })
    jQuery(document).on('mouseleave ', '.elementor-widget-twbb_woocommerce-products[data-widget_type="twbb_woocommerce-products.modern"].product_buttons_visibility__show_on_hover .product', function() {
        jQuery(this).find('.twbb-add_to_cart_container_open').css({'display':'none'});
        jQuery(this).find('.product_info_div').css('bottom',0);
    })


    jQuery('.twbb_woocommerce-products-ajax-paginate .page-numbers li').on('click', function(e) {
        e.preventDefault();
        productsAjaxPagination(jQuery(this));
    });
    jQuery('.elementor-widget-twbb_woocommerce-products .add_to_cart_button').on('click',function() {
        changeProductQuantity(this);
    });
    /*
    10web customization
    */
    jQuery(document).on('click', '.elementor-widget-twbb_woocommerce-products .twbb-product-quantity-change', function() {
        var $input = jQuery(this).parent().find('input');
        if ( jQuery(this).hasClass( 'twbb-minus-quantity' ) ) {
            if( (parseInt($input.val()) - 1) > 0 ) {
                $input.val(parseInt($input.val()) - 1);
            }
        } else {
            $input.val(parseInt($input.val()) + 1);
        }
        $input.change();
        jQuery('button[name=update_cart]').trigger('click');
        return false;
    });

    jQuery('.twbb-woocommerce-products-variations .variation-image').on('mouseenter', function() {
        var src = jQuery(this).attr('src');
        var product = jQuery(this).closest('.twbb-woocommerce-products-variations').parent();
        var product_link = product.attr('href');
        var attrs = {};
        jQuery.each( jQuery(this).attr('data-attrs_as_params').split('&'), function(dataKey, dataValue){
            if( dataValue ) {
                let key, val;
                key = dataValue.split('=')[0];
                val = dataValue.split('=')[1];
                attrs[key] = val;
            }
        })
        var new_link = add_params_to_product_link(product_link, attrs);
        product.attr('href', new_link);
        jQuery(this).closest('.twbb-woocommerce-products-variations').parent().find('>.variation-image').css('background-image', 'url(' + src + ')');
    });

    jQuery(document).on('click', '.add_to_cart_button', function(){
        jQuery(this).addClass('twbb-change-button-text').text(twbb.woocommerce.added);
        setTimeout(function(){
            jQuery('.add_to_cart_button.twbb-change-button-text').text(twbb.woocommerce.add_to_cart);
        },1000);})
    /*
      end customization
    */
})

function image_gallery_run() {
    var swiperInstance = null; // Store Swiper instance
    jQuery(document).on('mouseenter', ".woocommerce-loop-product__link .twbb-image-container", function() {
        const $slider = jQuery(this).closest(".woocommerce-loop-product__link").find('.product-gallery-slider');
        if( !$slider.length ) {
            return true;
        }
        let $firstImage = jQuery(this).find("img[data-image='main']");

        let imgWidth = $firstImage.innerWidth() || 270; // Default width
        let imgHeight = $firstImage.innerHeight() || 270; // Default height

        $slider.css({
            'width': imgWidth + 'px',
            'height': imgHeight + 'px'
        });

        let loop = false;
        if( jQuery(this).find(".product-gallery-slider .swiper-wrapper .swiper-slide").length > 1 ) {
            loop = true;
            jQuery(this).removeClass("hover-active hover-zoom-in");
        }
        let swiperOptions = {
            loop: loop,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            watchOverflow: true,
        };
        // Initialize Swiper only if not already initialized
        const Swiper = elementorFrontend.utils.swiper;
        if (typeof Swiper === 'undefined') {
            return;
        }
        if (!swiperInstance)
        {
            new Swiper($slider[0], swiperOptions);
        }

        // Show the slider
        $slider.css('display', 'block');
    });

    jQuery(document).on('mouseleave', ".woocommerce-loop-product__link", function() {
        const $slider = jQuery(this).closest(".woocommerce-loop-product__link").find('.product-gallery-slider');
        // Hide the slider
        $slider.css('display', 'none');
    });
}

function image_gallery_second_image_run() {
    jQuery(document).on('mouseenter', ".woocommerce-loop-product__link .twbb-image-container", function() {
        const $secondImage = jQuery(this).closest(".woocommerce-loop-product__link").find('.product-gallery-second-image');
        if( !$secondImage.length ) {
            return true;
        }
        let $firstImage = jQuery(this).find("img[data-image='main']");

        let imgWidth = $firstImage.innerWidth() || 270; // Default width
        let imgHeight = $firstImage.innerHeight() || 270; // Default height

        $secondImage.css({
            'width': imgWidth + 'px',
            'height': imgHeight + 'px'
        });

        // Show the slider
        $secondImage.css('display', 'block');
    });

    jQuery(document).on('mouseleave', ".woocommerce-loop-product__link", function() {
        const $secondImage = jQuery(this).closest(".woocommerce-loop-product__link").find('.product-gallery-second-image');
        // Hide the slider
        $secondImage.css('display', 'none');
    });
}

function add_params_to_product_link( link, attrs) {
    let url, new_params, new_link;
    url = new URL(link).searchParams;
    Object.keys(attrs).forEach(function(key) {
        url.set(key, attrs[key]);
    });
    new_params = url.toString();
    new_link = link.split('?')[0] + '?' + new_params;
    return new_link;
}

function productsAjaxPagination(element) {
    const url = element.find('a').attr('href');
    const container = element.closest('.elementor-widget-twbb_woocommerce-products');
    const container_id = element.closest('.elementor-widget-twbb_woocommerce-products').data('id');
    jQuery.ajax({
        url: url,
        type:'GET',
        dataType: 'html',
        success: function(data){
            let parser = new DOMParser();
            const doc = parser.parseFromString(data, 'text/html');
            const new_page = jQuery(doc).find('.elementor-widget-twbb_woocommerce-products[data-id="' + container_id + '"]').html();
            container.html(new_page);
            jQuery('.twbb_woocommerce-products-ajax-paginate .page-numbers li').on('click', function(e) {
                e.preventDefault();
                productsAjaxPagination(jQuery(this));
            })
        }
    })
}

function changeProductQuantity(that) {
    let input_value = jQuery(that).closest('li.product').find('input.twbb-product-quantity-input').val();
    jQuery(that).attr('data-quantity',input_value);
}
