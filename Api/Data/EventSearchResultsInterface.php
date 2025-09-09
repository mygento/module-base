<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface EventSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of Event
     * @return \Mygento\Base\Api\Data\EventInterface[]
     */
    public function getItems();

    /**
     * Set list of Event
     * @param \Mygento\Base\Api\Data\EventInterface[] $items
     */
    public function setItems(array $items);
}
