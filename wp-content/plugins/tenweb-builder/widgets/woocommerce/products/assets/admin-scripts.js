jQuery( window ).on( 'elementor:init', function() {
  elementor.hooks.addAction('panel/open_editor/widget/twbb_woocommerce-products', function (panel, model, view) {
    /*
* change is done in 1.27.X version
* this is for insuring backward compatibility with the old version of the widget,
* we remove old control and replace it's values to new one's
*/
    const controlIds = {
      'hide_products_images': 'product_images',
      'hide_products_title': 'product_title',
      'hide_products_description': 'product_description',
      'hide_products_buttons': 'product_buttons',
      'hide_product_quantity': 'product_quantity',
      'classic_skin_hide_products_titles': 'classic_skin_product_title',
      'modern_skin_hide_products_titles': 'modern_skin_product_title',
      'classic_skin_hide_products_description': 'classic_skin_product_description',
      'modern_skin_hide_products_description': 'modern_skin_product_description',
      'classic_skin_hide_product_quantity': 'classic_skin_product_quantity',
      'modern_skin_hide_product_quantity': 'modern_skin_product_quantity',
      'classic_skin_hide_products_images': 'classic_skin_product_images',
      'modern_skin_hide_products_images': 'modern_skin_product_images',
      'classic_skin_hide_products_buttons': 'classic_skin_product_buttons',
      'modern_skin_hide_products_buttons': 'modern_skin_product_buttons',
    }

    let reload = false;
    for (const oldControlId in controlIds) {
      if (controlIds.hasOwnProperty(oldControlId)) {
        const newControlId = controlIds[oldControlId];
        var oldControl = model.get('settings').get(oldControlId);
        var newControl = model.get('settings').get(newControlId);
        if (newControl === 'default') {
          reload = true;
          replaceOldControlWithNew({
            'model': model,
            'container': view.container,
            'oldControlValue': '' === oldControl ? 'yes' : 'yes' === oldControl ? '' : oldControl,
            'newControlId': newControlId
          });
        }

      }
    }

    if( reload ) {
      window.parent.$e.run('document/save/default').then(() => {
        window.parent.$e.run('document/elements/deselect-all');
        window.parent.$e.run('document/elements/toggle-selection',
          {
            container: view.container
          });
      });
    }
  });
});

function replaceOldControlWithNew(args) {
  let settings = {
    [args['newControlId']]: args['oldControlValue'],
  }
  window.parent.$e.commands.run('document/elements/settings', {
    "container": args['container'],
    settings: settings
  });
}