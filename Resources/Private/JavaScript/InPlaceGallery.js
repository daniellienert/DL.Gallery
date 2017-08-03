// in place gallery
(function($) {
  $(function() {

    var setPicActive = function(indicator, indicators, main) {

      var $href = indicator.attr('href');
      var $size = indicator.data('size').split('x');
      var $width = $size[0];
      var $height = $size[1];

      main.attr('src', $href);
      main.attr('width', $width);
      main.attr('height', $height);

      main.addClass('loading');

      main.one('load', function() {
        // image loaded here
        main.removeClass('loading');
      })

      indicators.removeClass('active');
      indicator.addClass('active');
    }


    var moveActive = function(move, gallery, indicators, main) {
      var showIndex = indicators.index(gallery.find('a.active'));
      if (showIndex === -1) {
        showIndex = 0;
      }
      showIndex = (showIndex + move) % indicators.length;
      setPicActive($(indicators.eq(showIndex)), indicators, main);
    }


    $('.dl-gallery-gallery').each(function() {
      var $gallery = $(this);
      var $allGalleryIndicators = $gallery.find('a');
      var $mainImg = $($gallery.find('.dl-gallery__prefix__img img')[0]);

      var $pre = $($gallery.find('.dl-gallery__prefix__pre')[0]);
      var $next = $($gallery.find('.dl-gallery__prefix__next')[0]);

      $($pre).on('click', function(event) {
        moveActive(-1, $gallery, $allGalleryIndicators, $mainImg);
      })

      $($next).on('click', function(event) {
        moveActive(+1, $gallery, $allGalleryIndicators, $mainImg);
      })

      $allGalleryIndicators.each(function(index, elementIndicator) {
        $(elementIndicator).on('mouseover', function(event) {
          setPicActive($(elementIndicator), $allGalleryIndicators, $mainImg);
        })
        $(elementIndicator).on('click', function(event) {
          setPicActive($(elementIndicator), $allGalleryIndicators, $mainImg);
          event.stopPropagation();
          return false;
        })
      })

    });
  });

})(jQuery);
