var tenwebCountdown = function( $countdown, endTime ) {
  var timeInterval,
    elements = {
      $monthsSpan: $countdown.find( '.tenweb-countdown-months' ),
      $daysSpan: $countdown.find( '.tenweb-countdown-days' ),
      $hoursSpan: $countdown.find( '.tenweb-countdown-hours' ),
      $minutesSpan: $countdown.find( '.tenweb-countdown-minutes' ),
      $secondsSpan: $countdown.find( '.tenweb-countdown-seconds' )
    };

  var updateClock = function() {
    var timeRemaining = tenwebCountdown.getTimeRemaining( endTime, elements[ '$monthsSpan' ].length );

    jQuery.each( timeRemaining.parts, function( timePart ) {
      var $element = elements[ '$' + timePart + 'Span' ],
        partValue = this.toString();

      if ( 1 === partValue.length ) {
        partValue = 0 + partValue;
      }

      if ( $element.length ) {
        $element.text( partValue );
      }
    } );

    if ( timeRemaining.total <= 0 ) {
      var hideAfterExpiry = $countdown.data( 'hide-after-expiry' );
      if ( 'yes' == hideAfterExpiry ) {
        $countdown.find('.tenweb-countdown-item').addClass( 'tenweb-hidden' );
        $countdown.parent().find('.tenweb-countdown-description').addClass( 'tenweb-hidden' );
        $countdown.parent().find('.tenweb-countdown-expired').removeClass( 'tenweb-hidden' );
      }
      clearInterval( timeInterval );
    }
  };

  var initializeClock = function() {
    updateClock();

    timeInterval = setInterval( updateClock, 1000 );
  };

  initializeClock();
};

tenwebCountdown.getTimeRemaining = function( endTime, showMonths ) {
  var now = new Date();
  var timeRemaining = endTime - now;
  var days = Math.floor( timeRemaining / ( 1000 * 60 * 60 * 24 ) );
  var months = showMonths && days > 31 ? (endTime.getFullYear() - now.getFullYear()) * 12 + endTime.getMonth() - now.getMonth() : 0;
  if ( showMonths && months ) {
    days = endTime.getDate() - now.getDate();
  }
  var hours = Math.floor( ( timeRemaining / ( 1000 * 60 * 60 ) ) % 24 );
  var minutes = Math.floor( ( timeRemaining / 1000 / 60 ) % 60 );
  var seconds = Math.floor( ( timeRemaining / 1000 ) % 60 );

  if ( days < 0 || hours < 0 || minutes < 0 ) {
    seconds = minutes = hours = days = 0;
  }

  return {
    total: timeRemaining,
    parts: {
      months: months,
      days: days,
      hours: hours,
      minutes: minutes,
      seconds: seconds
    }
  };
};

jQuery( window ).on( 'elementor/frontend/init', function() {
  elementorFrontend.hooks.addAction( 'frontend/element_ready/twbbcountdown.default', function ( $scope ) {
    var $element = $scope.find( '.tenweb-countdown' ),
      date = new Date( $element.data( 'date' ) * 1000 );

    new tenwebCountdown( $element, date );
  } );
});