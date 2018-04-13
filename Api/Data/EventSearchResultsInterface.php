<?php
/**
 * @author Mygento
 * @package Mygento_Base
 */
namespace Mygento\Base\Api\Data;

interface EventSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get events list.
     *
     * @return \Mygento\Base\Api\Data\EventInterface $event[]
     */
    public function getItems();

    /**
     * Set events list.
     *
     * @param \Mygento\Base\Api\Data\EventInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
