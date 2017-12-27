<?php
namespace DL\Gallery\ViewHelpers;

/*
 * This file is part of the DL.Gallery package.
 *
 * (c) Daniel Lienert 2016
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Media\Domain\Model\ImageInterface;

class ImageViewHelper extends \Neos\Media\ViewHelpers\ImageViewHelper
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerTagAttribute('theme', 'string', 'The name of a gallery theme', false);
        $this->registerTagAttribute('imageVariant', 'string', 'The name of a defined resolution', false);
        $this->overrideArgument('alt', 'string', 'The alt attribute', false);
    }

    /**
     * @param ImageInterface $image The image to be rendered as an image
     * @param integer $width Desired width of the image
     * @param integer $maximumWidth Desired maximum width of the image
     * @param integer $height Desired height of the image
     * @param integer $maximumHeight Desired maximum height of the image
     * @param boolean $allowCropping Whether the image should be cropped if the given sizes would hurt the aspect ratio
     * @param boolean $allowUpScaling Whether the resulting image size might exceed the size of the original image
     * @param boolean $async Return asynchronous image URI in case the requested image does not exist already
     * @param string $preset Preset used to determine image configuration
     * @param integer $quality Quality of the image
     * @return string an <img...> html tag
     */
    public function render(ImageInterface $image = null, $width = null, $maximumWidth = null, $height = null, $maximumHeight = null, $allowCropping = false, $allowUpScaling = false, $async = false, $preset = null, $quality = null)
    {

        if ($this->hasArgument('theme') && $this->hasArgument('imageVariant')) {
            $themeSettings = $this->getSettingsForCurrentTheme($this->arguments['theme']);

            $imageVariantSettings = $themeSettings['imageVariants'][$this->arguments['imageVariant']];

            $width = isset($imageVariantSettings['width']) ? $imageVariantSettings['width'] : 0;
            $maximumWidth = isset($imageVariantSettings['maximumWidth']) ? $imageVariantSettings['maximumWidth'] : 0;
            $height = isset($imageVariantSettings['height']) ? $imageVariantSettings['height'] : 0;
            $maximumHeight = isset($imageVariantSettings['maximumHeight']) ? $imageVariantSettings['maximumHeight'] : 0;

            $allowCropping = isset($imageVariantSettings['allowCropping']) ? $imageVariantSettings['allowCropping'] : false;
            $allowUpScaling = isset($imageVariantSettings['allowUpScaling']) ? $imageVariantSettings['allowUpScaling'] : false;

            $this->tag->removeAttribute('theme');
            $this->tag->removeAttribute('imageVariant');
        }

        $this->tag->addAttributes([
            'title' => $image->getTitle(),
            'alt' => $image->getCaption() ? $image->getCaption() : $image->getTitle()
        ]);

        $this->arguments['alt'] = $this->tag->getAttribute('alt'); // so that parent::render doesn't override the alt

        return parent::render($image, $width, $maximumWidth, $height, $maximumHeight, $allowCropping, $allowUpScaling, $async, $preset);
    }

    /**
     * @param $theme
     * @return mixed
     */
    protected function getSettingsForCurrentTheme($theme)
    {
        return $this->settings['themes'][$theme]['themeSettings'];
    }
}
