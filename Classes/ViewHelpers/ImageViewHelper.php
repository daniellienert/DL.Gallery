<?php
declare(strict_types=1);

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
use Neos\Flow\Mvc\Routing\Exception\MissingActionNameException;
use Neos\FluidAdaptor\Core\ViewHelper\Exception;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Model\ImageInterface;
use Neos\Media\Exception\AssetServiceException;
use Neos\Media\Exception\ThumbnailServiceException;

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
     * @throws Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerTagAttribute('theme', 'string', 'The name of a gallery theme', false);
        $this->registerTagAttribute('imageVariant', 'string', 'The name of a defined resolution', false);
        $this->overrideArgument('alt', 'string', 'The alt attribute', false);
    }

    /**
     * @return string an <img...> html tag
     *
     * @throws MissingActionNameException
     * @throws AssetServiceException
     * @throws ThumbnailServiceException
     */
    public function render(): string
    {
        if ($this->hasArgument('theme') && $this->hasArgument('imageVariant')) {
            $themeSettings = $this->getSettingsForCurrentTheme($this->arguments['theme']);

            $imageVariantSettings = $themeSettings['imageVariants'][$this->arguments['imageVariant']];

            $this->arguments['width'] = isset($imageVariantSettings['width']) ? $imageVariantSettings['width'] : 0;
            $this->arguments['maximumWidth'] = isset($imageVariantSettings['maximumWidth']) ? $imageVariantSettings['maximumWidth'] : 0;
            $this->arguments['height'] = isset($imageVariantSettings['height']) ? $imageVariantSettings['height'] : 0;
            $this->arguments['maximumHeight'] = isset($imageVariantSettings['maximumHeight']) ? $imageVariantSettings['maximumHeight'] : 0;

            $this->arguments['allowCropping'] = isset($imageVariantSettings['allowCropping']) ? $imageVariantSettings['allowCropping'] : false;
            $this->arguments['allowUpScaling'] = isset($imageVariantSettings['allowUpScaling']) ? $imageVariantSettings['allowUpScaling'] : false;

            $this->tag->removeAttribute('theme');
            $this->tag->removeAttribute('imageVariant');
        }

        /** @var Image $image */
        $image = $this->arguments['image'];

        $this->tag->addAttributes([
            'title' => $image->getTitle(),
            'alt' => $image->getCaption() ? $image->getCaption() : $image->getTitle()
        ]);

        $this->arguments['alt'] = $this->tag->getAttribute('alt'); // so that parent::render doesn't override the alt

        return parent::render();
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
