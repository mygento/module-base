<?php
/**
 * @author Mygento
 * @package Mygento_Base
 */
namespace Mygento\Base\Api;

interface EventRepositoryInterface
{
    /**
     * Save event.
     *
     * @param \Mygento\Base\Api\Data\EventInterface $event
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function save(\Mygento\Base\Api\Data\EventInterface $event);

    /**
     * Retrieve event.
     *
     * @param int $eventId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function getById($eventId);

    /**
     * Retrieve events matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Base\Api\Data\EventSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete block.
     *
     * @param \Mygento\Base\Api\Data\EventInterface $event
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function delete(\Mygento\Base\Api\Data\EventInterface $event);
}
