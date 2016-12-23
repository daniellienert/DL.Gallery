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
use Neos\Flow\Persistence\QueryInterface;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractTagBasedViewHelper;
use Neos\Media\Domain\Model\AssetInterface;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Model\Tag;
use Neos\ContentRepository\Domain\Model\Node;

class GalleryViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @Flow\Inject
     * @var \Neos\Media\Domain\Repository\TagRepository
     */
    protected $tagRepository;

    /**
     * @Flow\Inject
     * @var \Neos\Media\Domain\Repository\ImageRepository
     */
    protected $imageRepository;

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
     */
    public function render(Node $galleryNode)
    {

        $this->templateVariableContainer->add('themeSettings', $this->getSettingsForCurrentTheme());

        $images = array_merge(
            $this->selectImagesByTag($galleryNode), 
            $this->getSelectedImages($galleryNode)
        );

        if (count($images) === 0) {
            return 'No images are assigned to the selected tag. Please go to the media management and assign images to this tag.';
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
        return $this->settings['themes']['bootstrapLightbox']['themeSettings'];
    }

}