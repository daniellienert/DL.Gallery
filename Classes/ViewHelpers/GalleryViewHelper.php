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

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('galleryNode', Node::class, 'The gallery node', true);
    }

    /**
     * @param Node $galleryNode
     * @return string
     */
    public function render()
    {
        $this->galleryNode = $this->arguments['galleryNode'];
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
        $i = 0;

        foreach ($images as $image) {
            if ($image instanceof Image) {
                /** @var Image $image */
                $this->templateVariableContainer->add('image', $image);
                $this->templateVariableContainer->add('imageMeta', $this->buildImageMetaDataArray($image));

                if ($i === 0) {
                    $this->templateVariableContainer->add('isFirst', true);
                } elseif ($this->templateVariableContainer->exists('isFirst')) {
                    $this->templateVariableContainer->remove('isFirst');
                }

                $result .= $this->renderChildren();

                $this->templateVariableContainer->remove('image');
                $this->templateVariableContainer->remove('imageMeta');

                $i++;
            }
        }

        $this->templateVariableContainer->remove('themeSettings');
        return $result;
    }


    /**
     * @return array
     */
    protected function getSelectedImages(): array
    {
        $images = $this->galleryNode->getProperty('assets');

        if (!is_array($images)) {
            return [];
        }

        if ($this->galleryNode->getProperty('sortingField') !== 'unsorted') {
            $this->sortImageObjects($images, $this->galleryNode->getProperty('sortingField'), $this->galleryNode->getProperty('sortingDirection'));
        }

        return $images;
    }


    /**
     * @return array
     */
    protected function selectImagesByTag(): array
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
    protected function selectImagesByAssetCollection(): array
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
    protected function buildImageMetaDataArray(Image $image): array
    {
        return [
            'title' => $image->getTitle(),
            'caption' => $image->getCaption()
        ];
    }

    /**
     * @return array
     */
    protected function getSettingsForCurrentTheme(): array
    {
        return $this->settings['themes'][$this->galleryNode->getProperty('theme')]['themeSettings'];
    }

    /**
     * @param array $images
     * @param string $field
     * @param string $direction
     */
    protected function sortImageObjects(array &$images, string $field = 'resource.filename', string $direction = 'ASC')
    {
        usort($images, function ($imageA, $imageB) use ($field) {
            $valueA = ObjectAccess::getPropertyPath($imageA, $field);
            $valueB = ObjectAccess::getPropertyPath($imageB, $field);

            if ($valueA instanceof \DateTime) {
                $valueA =  (string) $valueA->getTimestamp();
            }

            if ($valueB instanceof \DateTime) {
                $valueB =  (string) $valueB->getTimestamp();
            }

            return strcmp($valueA, $valueB);
        });

        if ($direction === 'DESC') {
            rsort($images);
        }
    }

    protected function setImageRepositoryDefaultOrderings()
    {
        if ($this->galleryNode->getProperty('sortingField') === 'unsorted') {
            return;
        }

        $sortingField = $this->galleryNode->getProperty('sortingField') ?: 'resource.filename';
        $sortingDirection = $this->galleryNode->getProperty('sortingDirection') === QueryInterface::ORDER_DESCENDING ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING;

        $this->imageRepository->setDefaultOrderings([
            $sortingField => $sortingDirection
        ]);
    }
}
