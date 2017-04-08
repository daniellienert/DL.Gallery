[![Latest Stable Version](https://poser.pugx.org/dl/gallery/v/stable)](https://packagist.org/packages/dl/gallery) [![Total Downloads](https://poser.pugx.org/dl/gallery/downloads)](https://packagist.org/packages/dl/gallery) [![Latest Unstable Version](https://poser.pugx.org/dl/gallery/v/unstable)](https://packagist.org/packages/dl/gallery) [![License](https://poser.pugx.org/dl/gallery/license)](https://packagist.org/packages/dl/gallery)

# Neos Gallery
This package provides a node type to easily render image galleries. 

# Installation and integration

The installation is done with composer: 

	composer require dl/gallery

### (De)activate CSS / Javascript autoloading

By default, the galleries CSS and JavaScript files are added to the header and footer includes of your page automatically on pages, where an instance of the gallery plugin is added. If you want to compile the JS / CSS into your pages main files you can deactivate this behavior via settings:

	DL:
	  Gallery:
	    loadGalleryCSS: false
	    loadGalleryJS: false

# Show a set of images as a gallery

## Select imaged to be displayed in the gallery

Add a new gallery node to your page and use the inspector add images from the media module or upload them directly.


## Use Tags or collections

**1. Use tags or collections to group images**

The images to show in a gallery need to be grouped by a tag or collection. To group images, go to the media module and add a new tag or collection. Drag and drop images onto this or select it in the image detail view.

**2. Chose a tag in the plugins inspector**

Add a new gallery plugin to your page. You can now choose a tag or a collection as the gallery source in the inspector. The images will then be rendered equally within the gallery.



## Gallery Presentation

### Theme Bootstrap Lightbox

The thumbnails of the gallery are displayed squared using a bootstrap grid. The lightbox [photoswipe](http://photoswipe.com/) is used to open a large representation of the image.

### Theme Justified
Uses [justified.js](http://nitinhayaran.github.io/Justified.js/demo/) to display the thumbnails and also [photoswipe](http://photoswipe.com/) as lightbox.