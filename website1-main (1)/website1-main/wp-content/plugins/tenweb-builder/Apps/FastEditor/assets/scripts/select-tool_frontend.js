jQuery(document).on('ready', function () {

    // update the display of the dropdown
    jQuery(document).on('click','div.twbb-fe-select-tool li',function(){
        //jQuery(this).parents('.twbb-fe-select-tool').find('.twbb-fe-selected-display').html(jQuery(this).html());
        jQuery(this).parent().find('li span').removeClass('twbb-fe-select-tool-active');
        jQuery(this).find('span').addClass('twbb-fe-select-tool-active');
    });

    // check if anything else other than the dropdown is clicked
    jQuery(document).on('click', 'body', function (e) {
        if (e.target.closest(".twbb-fe-select-tool") === null || jQuery(e.target).hasClass('.twbb-fe-selected-display')) {
            FE_TOOL_FRONTEND.closeAllTools(TWBB_DROPDOWN_SELECT_TOOL);
        }
    });

    jQuery(document).on('keyup','#twbb-select-input',function(){
        var input, filter, ul, li, i;
        input = jQuery(this);
        filter = input.val().toUpperCase();
        ul = jQuery(this).parent().parent().find('ul.twbb-fe-dropdown');
        li = ul.find('li');
        for (i = 0; i < li.length; i++) {
            txtValue = li[i].textContent || li[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = '';
            } else {
                li[i].style.display = 'none';
            }
        }
        let visible_li =  ul.find('li').is(':visible');
        if ( visible_li === false ) {
            ul.find('li.twbb-select-no-results').css('display', 'block');
        } else {
            ul.find('li.twbb-select-no-results').css('display','none');
        }
    });

});

function selectToolClick(tool){
    let selected = jQuery(tool);
    if (selected.hasClass("active")) {
        handleDropdown(selected,false);
    } else {
        let currentActive = jQuery(".twbb-fe-select-tool.active");

        if (currentActive.length > 0) {
            handleDropdown(currentActive,false);
        }

        handleDropdown(selected,true);
    }
}

// open all the dropdowns
function handleDropdown(dropdown, open) {
    if(open === false){
        FE_TOOL_FRONTEND.deleteAllActiveToolData();
    }

    if( dropdown && dropdown.length > 0 ) {
        if (open && !dropdown.hasClass("active")) {
            jQuery(dropdown).addClass("active");

            let $parent_div = jQuery(dropdown).find('ul.twbb-fe-dropdown')
            let $active_element = $parent_div.find('.twbb-fe-select-tool-active')
            if ($active_element.length) {
                $parent_div.scrollTop($parent_div.scrollTop() + $active_element.position().top);

                $parent_div.scrollTop($parent_div.scrollTop() + $active_element.position().top
                    - $parent_div.height() / 2 + $active_element.height() / 2);
            }


        } else {
            if ( dropdown.hasClass("active") ) {
                jQuery(dropdown).removeClass("active");
            }
        }
    }
}