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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\QueryInterface;
use TYPO3\Media\Domain\Model\AssetCollection;
use TYPO3\Media\Domain\Model\Image;
use TYPO3\Media\Domain\Model\Tag;
use TYPO3\TYPO3CR\Domain\Model\Node;

class GalleryViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{

    /**
     * @Flow\Inject
     * @var \TYPO3\Media\Domain\Repository\TagRepository
     */
    protected $tagRepository;

    /**
     * @Flow\Inject
     * @var \TYPO3\Media\Domain\Repository\ImageRepository
     */
    protected $imageRepository;

    /**
     * @var \TYPO3\Flow\I18n\Translator
     * @Flow\Inject
     */
    protected $translator;

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
     * @param Node $galleryNode
     * @return string
     * @throws \TYPO3\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     */
    public function render(Node $galleryNode)
    {

        $this->templateVariableContainer->add('themeSettings', $this->getSettingsForCurrentTheme());

        $images = array_merge(
            $this->selectImagesByTag($galleryNode), 
            $this->selectImagesByAssetCollection($galleryNode),
            $this->getSelectedImages($galleryNode)
        );


        if (count($images) === 0) {
            return $this->translator->translateById('gallery.noImagesSelected', [], null, null, 'Main', 'DL.Gallery');
        }

        $result = '';

        foreach ($images as $image) {
            /** @var Image $image */
            $this->templateVariableContainer->add('image', $image);
            $this->templateVariableContainer->add('imageMeta', $this->buildImageMetaDataArray($image));

            $result .= $this->renderChildren();

            $this->templateVariableContainer->remove('image');
            $this->templateVariableContainer->remove('imageMeta');
        }

        $this->templateVariableContainer->remove('themeSettings');

        return $result;
    }


    /**
     * @param Node $galleryNode
     * @return array
     */
    protected function getSelectedImages(Node $galleryNode)
    {
        $assets = $galleryNode->getProperty('assets');
        return is_array($assets) ? $assets : [];
    }


    /**
     * @param Node $galleryNode
     * @return array
     */
    protected function selectImagesByTag(Node $galleryNode)
    {
        $tagIdentifier = $galleryNode->getProperty('tag');

        if(empty($tagIdentifier) || $tagIdentifier === '~') {
            return [];
        }

        $tag = $this->tagRepository->findByIdentifier($tagIdentifier);
        /** @var Tag $tag */
        
        if(!($tag instanceof Tag)) {
            return [];
        }

        $sortingField = $galleryNode->getProperty('sortingField') ?: 'resource.filename';
        $sortingDirection = $galleryNode->getProperty('sortingDirection') === QueryInterface::ORDER_DESCENDING ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING;

        $this->imageRepository->setDefaultOrderings([
            $sortingField => $sortingDirection
        ]);

        $images = $this->imageRepository->findByTag($tag)->toArray();

        return $images;
    }

    /**
     * @param Node $galleryNode
     * @return array
     */
    protected function selectImagesByAssetCollection(Node $galleryNode)
    {
        $assetCollection = $galleryNode->getProperty('assetCollection');

        if(!($assetCollection instanceof AssetCollection)) {
            return [];
        }

        $sortingField = $galleryNode->getProperty('sortingField') ?: 'resource.filename';
        $sortingDirection = $galleryNode->getProperty('sortingDirection') === QueryInterface::ORDER_DESCENDING ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING;

        $this->imageRepository->setDefaultOrderings([
            $sortingField => $sortingDirection
        ]);

        $images = $this->imageRepository->findByAssetCollection($assetCollection)->toArray();

        return $images;
    }


    /**
     * @param Image $image
     * @return array
     */
    protected function buildImageMetaDataArray(Image $image)
    {
        return [
            'title' => $image->getTitle(),
            'caption' => $image->getCaption()
        ];
    }


    protected function getSettingsForCurrentTheme()
    {
        return $this->settings['themes']['bootstrapLightbox']['themeSettings'];
    }

}