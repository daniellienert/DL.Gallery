<?php
namespace DL\Gallery\ViewHelpers;

/***************************************************************
 *  Copyright (C) 2015 Daniel Lienert
 *
 *  This script is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;
use Neos\Media\Domain\Model\ImageInterface;
use Neos\Media\Domain\Model\ThumbnailConfiguration;

class ImageDataViewHelper extends AbstractViewHelper
{

    
	/**
	 * NOTE: This property has been introduced via code migration to ensure backwards-compatibility.
	 * @see AbstractViewHelper::isOutputEscapingEnabled()
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

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
     * @param ImageInterface|null $image
     * @param integer $width
     * @param integer $maximumWidth
     * @param integer $height
     * @param integer $maximumHeight
     * @param bool $allowCropping
     * @param bool $allowUpScaling
     * @return array|null
     * @throws \Neos\Media\Exception\AssetServiceException
     */
    public function render(ImageInterface $image = null, $width = null, $maximumWidth = null, $height = null, $maximumHeight = null, $allowCropping = false, $allowUpScaling = false)
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


        $thumbnailConfiguration = new ThumbnailConfiguration($width, $maximumWidth, $height, $maximumHeight, $allowCropping, $allowUpScaling);
        $imageData = $this->assetService->getThumbnailUriAndSizeForAsset($image, $thumbnailConfiguration);

        $imageData['title'] = $image->getTitle();
        $imageData['caption'] = $image->getCaption();

        if(!$this->hasArgument('key')) {
            return $imageData;
        }

        if (array_key_exists($this->arguments['key'], $imageData)) {
            return $imageData[$this->arguments['key']];
        }
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