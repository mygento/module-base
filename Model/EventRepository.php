<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EventRepository implements \Mygento\Base\Api\EventRepositoryInterface
{
    /** @var \Mygento\Base\Model\ResourceModel\Event */
    private $resource;

    /** @var \Mygento\Base\Model\ResourceModel\Event\CollectionFactory */
    private $collectionFactory;

    /** @var \Mygento\Base\Api\Data\EventInterfaceFactory */
    private $entityFactory;

    /** @var \Mygento\Base\Api\Data\EventSearchResultsInterfaceFactory */
    private $searchResultsFactory;

    /**
     * @param \Mygento\Base\Model\ResourceModel\Event $resource
     * @param \Mygento\Base\Model\ResourceModel\Event\CollectionFactory $collectionFactory
     * @param \Mygento\Base\Api\Data\EventInterfaceFactory $entityFactory
     * @param \Mygento\Base\Api\Data\EventSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceModel\Event $resource,
        ResourceModel\Event\CollectionFactory $collectionFactory,
        \Mygento\Base\Api\Data\EventInterfaceFactory $entityFactory,
        \Mygento\Base\Api\Data\EventSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->entityFactory = $entityFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function getById($entityId)
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Base Event with id "%1" does not exist.', $entityId)
            );
        }

        return $entity;
    }

    /**
     * @param \Mygento\Base\Api\Data\EventInterface $entity
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Mygento\Base\Api\Data\EventInterface
     * @psalm-param \Mygento\Base\Api\Data\EventInterface&\Magento\Framework\Model\AbstractModel $event
     */
    public function save(\Mygento\Base\Api\Data\EventInterface $entity)
    {
        try {
            /** @psalm-param \Mygento\Base\Api\Data\EventInterface&\Magento\Framework\Model\AbstractModel $event */
            $this->resource->save($entity);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __($exception->getMessage())
            );
        }

        return $entity;
    }

    /**
     * @param \Mygento\Base\Api\Data\EventInterface $entity
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Mygento\Base\Api\Data\EventInterface $entity)
    {
        try {
            $this->resource->delete($entity);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __($exception->getMessage())
            );
        }

        return true;
    }

    /**
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->getById($entityId));
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Mygento\Base\Api\Data\EventSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Mygento\Base\Model\ResourceModel\Event\Collection $collection */
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $sortOrders = $criteria->getSortOrders();
        $sortAsc = SortOrder::SORT_ASC;
        $orderAsc = Collection::SORT_ORDER_ASC;
        $orderDesc = Collection::SORT_ORDER_DESC;
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == $sortAsc) ? $orderAsc : $orderDesc
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        /** @var \Mygento\Base\Api\Data\EventSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
