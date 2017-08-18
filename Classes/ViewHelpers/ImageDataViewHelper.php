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

use DL\Gallery\Exceptions\InvalidConfigurationException;
use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;
use Neos\Media\Domain\Model\ImageInterface;
use Neos\Media\Domain\Model\ThumbnailConfiguration;

class ImageDataViewHelper extends AbstractViewHelper
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @Flow\Inject
     * @var \Neos\Media\Domain\Service\ThumbnailService
     */
    protected $thumbnailService;

    /**
     * @Flow\Inject
     * @var \Neos\Media\Domain\Service\AssetService
     */
    protected $assetService;

    /**
     * name of the tag to be created by this view helper
     *
     * @var string
     */
    protected $tagName = 'img';

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
        $this->registerArgument('theme', 'string', 'The name of a gallery theme', false);
        $this->registerArgument('imageVariant', 'string', 'The name of a defined resolution', false);
        $this->registerArgument('key', 'string', 'The key of the meta data array', false);
    }


    /**
     * @param ImageInterface $image
     * @param integer $width
     * @param integer $maximumWidth
     * @param integer $height
     * @param integer $maximumHeight
     * @param bool $allowCropping
     * @param bool $allowUpScaling
     * @return array|null
     * @throws InvalidConfigurationException
     */
    public function render(ImageInterface $image, $width = null, $maximumWidth = null, $height = null, $maximumHeight = null, $allowCropping = false, $allowUpScaling = false)
    {
        if ($this->hasArgument('theme') && $this->hasArgument('imageVariant')) {
            $themeSettings = $this->getSettingsForCurrentTheme($this->arguments['theme']);

            if (!isset($themeSettings['imageVariants'][$this->arguments['imageVariant']])) {
                throw new InvalidConfigurationException(sprintf('The theme "%s" has no imageVariant with name "%s" defined.', $this->arguments['theme'], $this->arguments['imageVariant']), 1503035707);
            }

            $imageVariantSettings = $themeSettings['imageVariants'][$this->arguments['imageVariant']];

            $width = isset($imageVariantSettings['width']) ? $imageVariantSettings['width'] : 0;
            $maximumWidth = isset($imageVariantSettings['maximumWidth']) ? $imageVariantSettings['maximumWidth'] : 0;
            $height = isset($imageVariantSettings['height']) ? $imageVariantSettings['height'] : 0;
            $maximumHeight = isset($imageVariantSettings['maximumHeight']) ? $imageVariantSettings['maximumHeight'] : 0;

            $allowCropping = isset($imageVariantSettings['allowCropping']) ? $imageVariantSettings['allowCropping'] : false;
            $allowUpScaling = isset($imageVariantSettings['allowUpScaling']) ? $imageVariantSettings['allowUpScaling'] : false;
        }


        $thumbnailConfiguration = new ThumbnailConfiguration($width, $maximumWidth, $height, $maximumHeight, $allowCropping, $allowUpScaling);
        $imageData = $this->assetService->getThumbnailUriAndSizeForAsset($image, $thumbnailConfiguration);

        $imageData['title'] = $image->getTitle();
        $imageData['caption'] = $image->getCaption();

        if (!$this->hasArgument('key')) {
            return $imageData;
        }

        if (array_key_exists($this->arguments['key'], $imageData)) {
            return $imageData[$this->arguments['key']];
        }
    }


    /**
     * @param string $theme
     * @return mixed
     * @throws InvalidConfigurationException
     */
    protected function getSettingsForCurrentTheme($theme)
    {
        if (!isset($this->settings['themes'][$theme])) {
            throw new InvalidConfigurationException(sprintf('No theme with name %s was found in settings.', $theme), 1503035486);
        }

        if (!isset($this->settings['themes'][$theme]['themeSettings'])) {
            throw new InvalidConfigurationException(sprintf('The theme %s has no themeSettings defined ', $theme), 1503035487);
        }

        return $this->settings['themes'][$theme]['themeSettings'];
    }
}
