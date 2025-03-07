jQuery( window ).on( 'elementor:init', function() {

    elementor.channels.editor.on('elementorPlaylistWidget:setVideoData', function (e) {
        $e.run('document/elements/settings', {
            container: e.container,
            settings: {
                thumbnail: {
                    url: e.currentItem.thumbnail ? e.currentItem.thumbnail.url : ''
                },
                title: e.currentItem.video_title ? e.currentItem.video_title : '',
                duration: e.currentItem.duration ? e.currentItem.duration : ''
            },
            options: {
                external: true
            }
        });
    });

});
