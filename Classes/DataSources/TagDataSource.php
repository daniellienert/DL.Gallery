<?php
declare(strict_types=1);

namespace DL\Gallery\DataSources;

/*
 * This file is part of the DL.Gallery package.
 *
 * (c) Daniel Lienert 2016
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Media\Domain\Model\Tag;
use Neos\Media\Domain\Repository\TagRepository;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;

class TagDataSource extends AbstractDataSource
{

    /**
     * @var string
     */
    static protected $identifier = 'dl-gallery-tags';

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @Flow\inject
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * @param NodeInterface|null $node
     * @param array $arguments
     * @return QueryResultInterface
     */
    public function getData(NodeInterface $node = null, array $arguments)
    {
        $tagCollection = $this->tagRepository->findAll();
        $tags['']['label'] = '-';

        foreach ($tagCollection as $tag) {
            /** @var Tag $tag */
            $tags[$this->persistenceManager->getIdentifierByObject($tag)] = $tag;
        }

        return $tags;
    }
}
