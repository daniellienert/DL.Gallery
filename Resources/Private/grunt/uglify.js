module.exports = {
    bootstrapLightbox: {
        src: [
            'node_modules/photoswipe/dist/photoswipe.js',
            'node_modules/photoswipe/dist/photoswipe-ui-default.js',
            'JavaScript/PhotoSwipe.js'
        ],
        dest: '../Public/JavaScript/BootstrapLightbox.min.js'
    },

    justifiedGallery: {
        src: [
            'node_modules/justifiedGallery/dist/js/jquery.justifiedGallery.js',
            'node_modules/photoswipe/dist/photoswipe.js',
            'node_modules/photoswipe/dist/photoswipe-ui-default.js',
            'JavaScript/JustifiedGallery/JustifiedGallery.js',
            'JavaScript/PhotoSwipe.js'
        ],
        dest: '../Public/JavaScript/JustifiedGallery.min.js'
    }

    inPlaceGallery: {
        src: [
            'JavaScript/InPlaceGallery.js',
        ],
        dest: '../Public/JavaScript/InPlaceGallery.min.js'
    }
}
