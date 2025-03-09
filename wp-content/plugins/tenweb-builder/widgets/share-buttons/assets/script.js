(function( $ ) {

  var ShareLink = function( element, userSettings ) {
    var $element,
      settings = {};

    var getNetworkLink = function( networkName ) {
      var link = ShareLink.networkTemplates[ networkName ].replace( /{([^}]+)}/g, function( fullMatch, pureMatch ) {
        if ( networkName == 'twitter' && pureMatch == 'text' ) {
          var text = jQuery(jQuery.parseHTML(settings[pureMatch])).text().replace(/\s\s+/g, ' ');
          var href = window.location.href;
          settings[pureMatch] = text.substr( 0, 345 - href.length ) + ' ...';
        }

        return settings[ pureMatch ];
      });

      return encodeURI( link );
    };

    var getNetworkNameFromClass = function( className ) {
      var classNamePrefix = className.substr( 0, settings.classPrefixLength );

      return classNamePrefix === settings.classPrefix ? className.substr( settings.classPrefixLength ) : null;
    };

    var bindShareClick = function( networkName ) {
      $element.on( 'click', function() {
        openShareLink( networkName );
      } );
    };

    var openShareLink = function( networkName ) {
      var shareWindowParams = '';

      if ( settings.width && settings.height ) {
        var shareWindowLeft = screen.width / 2 - settings.width / 2,
          shareWindowTop = screen.height / 2 - settings.height / 2;

        shareWindowParams = 'toolbar=0,status=0,width=' + settings.width + ',height=' + settings.height + ',top=' + shareWindowTop + ',left=' + shareWindowLeft;
      }

      var link = getNetworkLink( networkName ),
        isPlainLink = /^https?:\/\//.test( link ),
        windowName = isPlainLink ? '' : '_self';

      open( link, windowName, shareWindowParams );
    };

    var run = function() {
      $.each( element.classList, function() {
        var networkName = getNetworkNameFromClass( this );

        if ( networkName ) {
          bindShareClick( networkName );

          return false;
        }
      } );
    };

    var initSettings = function() {
      $.extend( settings, ShareLink.defaultSettings, userSettings );

      [ 'title', 'text' ].forEach( function( propertyName ) {
        settings[ propertyName ] = settings[ propertyName ].replace( '#', '' );
      } );

      settings.classPrefixLength = settings.classPrefix.length;
    };

    var initElements = function() {
      $element = $( element );
    };

    var init = function() {
      initSettings();

      initElements();

      run();
    };

    init();
  };

  ShareLink.networkTemplates = {
    twitter: 'https://twitter.com/intent/tweet?url={url}&text={text}',
    pinterest: 'https://www.pinterest.com/pin/find/?url={url}',
    facebook: 'https://www.facebook.com/sharer.php?u={url}',
    vk: 'https://vkontakte.ru/share.php?url={url}&title={title}&description={text}&image={image}',
    linkedin: 'https://www.linkedin.com/shareArticle?mini=true&url={url}&title={title}&summary={text}&source={url}',
    odnoklassniki: 'http://odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st._surl={url}',
    tumblr: 'https://tumblr.com/share/link?url={url}',
    delicious: 'https://del.icio.us/save?url={url}&title={title}',
    digg: 'https://digg.com/submit?url={url}',
    reddit: 'https://reddit.com/submit?url={url}&title={title}',
    /*mix: 'https://www.mix.com/submit?url={url}',*/
    pocket: 'https://getpocket.com/edit?url={url}',
    whatsapp: 'whatsapp://send?text=*{title}*\n{text}\n{url}',
    xing: 'https://www.xing.com/app/user?op=share&url={url}',
    print: 'javascript:print()',
    email: 'mailto:?subject={title}&body={url}',
    telegram: 'https://telegram.me/share/url?url={url}&text={text}',
    skype: 'https://web.skype.com/share?url={url}'
  };

  ShareLink.defaultSettings = {
    title: '',
    text: '',
    image: '',
    url: location.href,
    classPrefix: 's_',
    width: 640,
    height: 480
  };

  $.each( { shareLink: ShareLink }, function( pluginName ) {
    var PluginConstructor = this;

    $.fn[ pluginName ] = function( settings ) {
      return this.each( function() {
        $( this ).data( pluginName, new PluginConstructor( this, settings ) );
      } );
    };
  } );
})( jQuery );

jQuery( window ).on( 'elementor/frontend/init', function() {
  var HandlerModule = elementorModules.frontend.handlers.Base,
    tenwebShareButtonsHandler;

  tenwebShareButtonsHandler = HandlerModule.extend( {
    onInit: function() {
      HandlerModule.prototype.onInit.apply( this, arguments );

      var elementSettings = this.getElementSettings(),
        classes = this.getSettings( 'classes' ),
        isCustomURL = elementSettings.share_url && elementSettings.share_url.url,
        shareLinkSettings = {
          classPrefix: classes.shareLinkPrefix
        };

      if ( isCustomURL ) {
        shareLinkSettings.url = elementSettings.share_url.url;
      } else {
        shareLinkSettings.url = location.href;
        shareLinkSettings.title = elementorFrontend.config.post.title;
        shareLinkSettings.text = elementorFrontend.config.post.excerpt;
      }

      this.elements.$shareButton.shareLink( shareLinkSettings );
    },
    getDefaultSettings: function() {
      return {
        selectors: {
          shareButton: '.elementor-share-btn'
        },
        classes: {
          shareLinkPrefix: 'elementor-share-btn_'
        }
      };
    },
    getDefaultElements: function() {
      var selectors = this.getSettings( 'selectors' );

      return {
        $shareButton: this.$element.find( selectors.shareButton )
      };
    }
  } );

  if ( ! elementorFrontend.isEditMode() ) {
    elementorFrontend.hooks.addAction('frontend/element_ready/twbbshare-buttons.default', function ($scope) {
      new tenwebShareButtonsHandler({$element: $scope});
    });
  }
});