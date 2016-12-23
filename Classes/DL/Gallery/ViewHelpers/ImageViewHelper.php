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

    public function render(ImageInterface $image = null, $width = null, $maximumWidth = null, $height = null, $maximumHeight = null, $allowCropping = false, $allowUpScaling = false, $async = false, $preset = null)
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
        }

        $this->tag->addAttributes([
            'title' => $image->getTitle(),
            'alt' => $image->getCaption()
        ]);

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