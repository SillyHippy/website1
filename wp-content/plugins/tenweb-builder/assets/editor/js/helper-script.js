/*
* sending data to Google Analytics
 */
function analyticsDataPush ( action, eventName = '', info = '', params = {} ) {
    //TODO in future we can change all functions and add all keys to params
    if ( typeof dataLayer != "undefined" ) {
        let dataLayerObject = {
            event: '10web-event',
            'eventName': eventName,
            'eventAction': action,
            'info': info,
            'domain_id': twbb_helper.domain_id
        };
        Object.keys(params).forEach(key => {
            dataLayerObject[key] = params[key];
        });
        dataLayer.push(dataLayerObject);
    }
}