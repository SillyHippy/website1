jQuery( window ).on( 'elementor:init', function() {

  elementor.channels.editor.on( 'section:activated', function( sectionName, editor ) {
    var editedElement = editor.getOption( 'editedElementView' );
    var model = editedElement.getEditModel();
    var currentElementType = model.get( 'elType' );

    if ( 'widget' === currentElementType ) {
      currentElementType = model.get( 'widgetType' );
    }

    if ( 'twbb-flip-box' === currentElementType ) {
      var isSideBSection = -1 !== [ 'section_side_b_content', 'section_style_b' ].indexOf( sectionName );

      editedElement.$el.toggleClass( 'tenweb-flip-box--flipped', isSideBSection );

      var $backLayer = editedElement.$el.find( '.tenweb-flip-box__back' );

      if ( isSideBSection ) {
        $backLayer.css( 'transition', 'none' );
      }
      else {
        setTimeout( function() {
          $backLayer.css( 'transition', '' );
        }, 10 );
      }
    }
    else {
      editedElement.$el.parent().find('.elementor-widget-twbb-flip-box').each(function() {
        var container = jQuery(this);
        container.removeClass( 'tenweb-flip-box--flipped' );

        var $backLayer = container.find( '.tenweb-flip-box__back' );

        setTimeout( function() {
          $backLayer.css( 'transition', '' );
        }, 10 );
      });
    }
  } );

});