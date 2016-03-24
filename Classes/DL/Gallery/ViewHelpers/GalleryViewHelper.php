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
use TYPO3\Media\Domain\Model\Image;
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

        $images = $this->selectImages($galleryNode);

        if($images->count() === 0) {
            return 'No images are assigned to the selected tag. Please go to the media management and assign images to this tag.';
        }

        $result = '';

        foreach ($images as $image) { /** @var Image $image */
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
     * @return \TYPO3\Flow\Persistence\QueryResultInterface
     */
    protected function selectImages(Node $galleryNode)
    {
        $tagIdentifier = $galleryNode->getProperty('tag');
        $tag = $this->tagRepository->findByIdentifier($tagIdentifier); /** @var \TYPO3\Media\Domain\Model\Tag $tag */

        $sortingField = $galleryNode->getProperty('sortingField') ?: 'resource.filename';
        $sortingDirection = $galleryNode->getProperty('sortingDirection') === QueryInterface::ORDER_DESCENDING ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING;

        $this->imageRepository->setDefaultOrderings([
            $sortingField => $sortingDirection
        ]);

        $images = $this->imageRepository->findByTag($tag);

        return $images;
    }


    /**
     * @param Image $image
     * @return array
     */
    protected function buildImageMetaDataArray(Image $image) {
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