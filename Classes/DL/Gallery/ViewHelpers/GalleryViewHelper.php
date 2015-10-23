<?php
namespace DL\Gallery\ViewHelpers;

/***************************************************************
 *  Copyright (C) 2015 punkt.de GmbH
 *  Authors: el_equipo <el_equipo@punkt.de>
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



    public function render(Node $galleryNode)
    {
        $images = $this->selectImages($galleryNode);
        $result = '';

        foreach($images as $image) {
            $this->templateVariableContainer->add('image', $image);
            $result .= $this->renderChildren();
            $this->templateVariableContainer->remove('image');
        }

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

        $images = $this->imageRepository->findByTag($tag);

        return $images;
    }

}