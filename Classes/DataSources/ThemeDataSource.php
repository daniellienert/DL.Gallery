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

use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;

class ThemeDataSource extends AbstractDataSource
{

    /**
     * @var string
     */
    static protected $identifier = 'dl-gallery-themes';

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
     * @param NodeInterface|null $node
     * @param array $arguments
     * @return array
     */
    public function getData(NodeInterface $node = null, array $arguments)
    {

        $themes = [];

        foreach ($this->settings['themes'] as $key => $theme) {

            $label = isset($theme['label']) ? $theme['label'] : $key;
            $themes[$key]['label'] = $label;
        }

        return $themes;
    }

}
