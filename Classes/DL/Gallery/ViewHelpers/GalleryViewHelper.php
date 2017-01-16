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
use Neos\Flow\I18n\Translator;
use Neos\Flow\Persistence\QueryInterface;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractTagBasedViewHelper;
use Neos\Media\Domain\Model\AssetCollection;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Model\Tag;
use Neos\ContentRepository\Domain\Model\Node;
use Neos\Media\Domain\Repository\ImageRepository;
use Neos\Media\Domain\Repository\TagRepository;
use Neos\Utility\ObjectAccess;

class GalleryViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @Flow\Inject
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @Flow\Inject
     * @var ImageRepository
     */
    protected $imageRepository;

    /**
     * @var Translator
     * @Flow\Inject
     */
    protected $translator;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var Node
     */
    protected $galleryNode;

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
     */
    public function render(Node $galleryNode)
    {
        $this->galleryNode = $galleryNode;
        $this->templateVariableContainer->add('themeSettings', $this->getSettingsForCurrentTheme());


        $images = array_merge(
            $this->selectImagesByTag(),
            $this->selectImagesByAssetCollection(),
            $this->getSelectedImages()
        );

        if (count($images) === 0) {
            return $this->translator->translateById('gallery.noImagesSelected', [], null, null, 'Main', 'DL.Gallery');
        }

        $result = '';

        foreach ($images as $image) {
            if ($image instanceof Image) {
                /** @var Image $image */
                $this->templateVariableContainer->add('image', $image);
                $this->templateVariableContainer->add('imageMeta', $this->buildImageMetaDataArray($image));

                $result .= $this->renderChildren();

                $this->templateVariableContainer->remove('image');
                $this->templateVariableContainer->remove('imageMeta');
            }
        }

        $this->templateVariableContainer->remove('themeSettings');

        return $result;
    }


    /**
     * @return array
     */
    protected function getSelectedImages()
    {
        $images = $this->galleryNode->getProperty('assets');

        if(!is_array($images)) {
            return [];
        }

        if($this->galleryNode->getProperty('sortingField') !== 'unsorted') {
            $this->sortImageObjects($images, $this->galleryNode->getProperty('sortingField'), $this->galleryNode->getProperty('sortingDirection'));
        }

        return $images;
    }


    /**
     * @return array
     */
    protected function selectImagesByTag()
    {
        $tagIdentifier = $this->galleryNode->getProperty('tag');

        if (empty($tagIdentifier) || $tagIdentifier === '~') {
            return [];
        }

        $tag = $this->tagRepository->findByIdentifier($tagIdentifier);
        /** @var Tag $tag */

        if (!($tag instanceof Tag)) {
            return [];
        }

        $this->setImageRepositoryDefaultOrderings();
        $images = $this->imageRepository->findByTag($tag)->toArray();

        return $images;
    }

    /**
     * @return array
     */
    protected function selectImagesByAssetCollection()
    {
        $assetCollection = $this->galleryNode->getProperty('assetCollection');

        if (!($assetCollection instanceof AssetCollection)) {
            return [];
        }

        $this->setImageRepositoryDefaultOrderings();
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

    /**
     * @return array
     */
    protected function getSettingsForCurrentTheme()
    {
        return $this->settings['themes'][$this->galleryNode->getProperty('theme')]['themeSettings'];
    }

    /**
     * @param array $images
     * @param string $field
     * @param string $direction
     */
    protected function sortImageObjects(array &$images, $field = 'resource.filename', $direction = 'ASC')
    {
        usort($images, function ($imageA, $imageB) use ($field, $direction) {
            return strcmp(ObjectAccess::getPropertyPath($imageA, $field), ObjectAccess::getPropertyPath($imageB, $field));
        });

        if ($direction === 'DESC') {
            rsort($images);
        }
    }

    protected function setImageRepositoryDefaultOrderings()
    {
        if($this->galleryNode->getProperty('sortingField') !== 'unsorted') {
            return;
        }

        $sortingField = $this->galleryNode->getProperty('sortingField') ?: 'resource.filename';
        $sortingDirection = $this->galleryNode->getProperty('sortingDirection') === QueryInterface::ORDER_DESCENDING ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING;

        $this->imageRepository->setDefaultOrderings([
            $sortingField => $sortingDirection
        ]);
    }
}
