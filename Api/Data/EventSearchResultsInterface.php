<?php

/**
 * @author Mygento Team
 * @copyright 2014-2018 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api\Data;

interface EventSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
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
