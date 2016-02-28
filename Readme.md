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

### 1. Use tags to group images
The images to show in a gallery need to be grouped by a tag. To group images, go to the media module and add a new tag. Drag and drop images onto this tag or select the tag in the image detail view.

### 2. Chose a tag in the plugins inspector
Add a new gallery plugin to your page. You can now choose a tag as the gallery source in the inspector. The images will then be rendered equally within the gallery.

## Gallery Presentation
Currently the thumbnails of the gallery are displayed squared using a bootstrap grid. The lightbox [photoswipe](http://photoswipe.com/) is used to open a large representation of the image.