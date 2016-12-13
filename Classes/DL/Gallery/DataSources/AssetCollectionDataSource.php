<?php

namespace DL\Gallery\DataSources;

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

use TYPO3\Flow\Utility\TypeHandling;
use TYPO3\Neos\Service\DataSource\AbstractDataSource;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\Flow\Annotations as Flow;

class AssetCollectionDataSource extends AbstractDataSource
{

    /**
     * @var string
     */
    static protected $identifier = 'dl-gallery-assetcollections';


    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
     */
    protected $persistenceManager;


    /**
     * @Flow\Inject
     * @var \TYPO3\Media\Domain\Repository\AssetCollectionRepository
     */
    protected $assetCollectionRepository;


    /**
     * @param NodeInterface|null $node
     * @param array $arguments
     * @return \TYPO3\Flow\Persistence\QueryResultInterface
     */
    public function getData(NodeInterface $node = null, array $arguments)
    {
        $options = [];
        // Empty value
        $options[] = ['label' => '', 'value' => '~'];
        $assetCollections = $this->assetCollectionRepository->findAll();
        foreach ($assetCollections as $assetCollection) {
            /** @var \TYPO3\Media\Domain\Model\AssetCollection $assetCollection */
            $options[] = [
                'label' => $assetCollection->getTitle(),
                'value' => json_encode([
                    '__identity' => $this->persistenceManager->getIdentifierByObject($assetCollection),
                    '__type' => TypeHandling::getTypeForValue($assetCollection)
                ])
            ];
        }
       return $options;
    }

}