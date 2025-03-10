var tenwebShareButtons = (function() {
  return {
    networksClassDictionary: {
      pocket: 'fab fa-get-pocket',
      email: 'fa fa-envelope',
      print: 'fa fa-print'
    },

    networks: {
      'facebook': 'Facebook',
      'twitter': 'Twitter',
      'linkedin': 'LinkedIn',
      'pinterest': 'Pinterest',
      'reddit': 'Reddit',
      'vk': 'VK',
      'odnoklassniki': 'OK',
      'tumblr': 'Tumblr',
      'delicious': 'Delicious',
      'digg': 'Digg',
      'skype': 'Skype',
      'stumbleupon': 'StumbleUpon',
      'telegram': 'Telegram',
      'pocket': 'Pocket',
      'xing': 'XING',
      'whatsapp': 'WhatsApp',
      'email': 'Email',
      'print': 'Print',
    },

    getNetworkClass: function( networkName ) {
      return this.networksClassDictionary[ networkName ] || 'fab fa-' + networkName;
    },

    getNetworkTitle: function( buttonSettings ) {
      return buttonSettings.text || this.networks[ buttonSettings.button ];
    }
  };
})();