<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface EventRepositoryInterface
{
    /**
     * Save Event
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\EventInterface $entity): Data\EventInterface;

    /**
     * Retrieve Event
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $entityId): Data\EventInterface;

    /**
     * Retrieve Event entities matching the specified criteria
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): Data\EventSearchResultsInterface;

    /**
     * Delete Event
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\EventInterface $entity): bool;

    /**
     * Delete Event
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $entityId): bool;
}
