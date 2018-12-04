<?php

/**
 * @author Mygento Team
 * @copyright 2014-2018 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api;

interface EventRepositoryInterface
{
    /**
     * Save event
     * @param \Mygento\Base\Api\Data\EventInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function save(Data\EventInterface $entity);

    /**
     * Retrieve event
     * @param int $entityId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function getById($entityId);

    /**
     * Retrieve event entities matching the specified criteria
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Base\Api\Data\EventSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete event
     * @param \Mygento\Base\Api\Data\EventInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function delete(Data\EventInterface $entity);

    /**
     * Delete event
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function deleteById($entityId);
}
