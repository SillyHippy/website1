jQuery( window ).on( 'elementor/frontend/init', function() {
    var codeHighlightHandler = elementorModules.frontend.handlers.Base.extend({

        onElementChange: function onElementChange() {
            // Handle the changes for "Word Wrap" feature
            Prism.highlightAllUnder(this.$element[0], false);
        },

        onInit: function onInit() {
            elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
            Prism.highlightAllUnder(this.$element[0], false);
        }
    });

    elementorFrontend.hooks.addAction( 'frontend/element_ready/twbb_code-highlight.default', function ( $scope ) {
        new codeHighlightHandler({ $element: $scope });
    });

});
