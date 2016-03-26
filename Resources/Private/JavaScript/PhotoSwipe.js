(function($) {
    $('.lightbox-images').each( function() {
        console.log('found');
        // Get the items.
        var $gallery     = $(this),
            getItems = function() {
                var items = [];
                $gallery.find('figure').each(function() {
                    var $href    = $(this).find('a').attr('href'),
                        $size    = $(this).find('a').data('size').split('x'),
                        $width   = $size[0],
                        $height  = $size[1],
                        $caption = $(this).find('figcaption').html();

                    var item = {
                        src   : $href,
                        w     : $width,
                        h     : $height,
                        title : $caption
                    }

                    items.push(item);
                });
                return items;
            }

        var items = getItems();

        // Preload image.
        var image = [];
        $.each(items, function(index, value) {
            image[index]     = new Image();
            image[index].src = value['src'];
        });

        // Binding click event.
        var $pswp = $('.pswp')[0];
        $gallery.on('click', 'figure', function(event) {
            event.preventDefault();

            var $index = $(this).index();
            var options = {
                index: $index,
                bgOpacity: 0.7,
                showHideOpacity: true,
                fullscreenEl: false,
                shareButtons: [
                    {id:'download', label:'Download image', url:'{{raw_image_url}}', download:true}
                ]
            }

            var lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
            lightBox.init();

            if($.isFunction($pswp.listen)) {
                $pswp.listen('close', function() {
                    $gallery.off('click');
                });
            }
        });
    });
})(jQuery);