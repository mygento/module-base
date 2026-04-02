<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mygento\Base\Api\Data\EventInterface;
use Mygento\Base\Api\Data\EventInterfaceFactory;
use Mygento\Base\Api\Data\EventSearchResultsInterface;
use Mygento\Base\Api\Data\EventSearchResultsInterfaceFactory;
use Mygento\Base\Api\EventRepositoryInterface;
use Mygento\Base\Model\ResourceModel\Event\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EventRepository implements EventRepositoryInterface
{
    public function __construct(
        private readonly ResourceModel\Event $resource,
        private readonly CollectionFactory $collectionFactory,
        private readonly EventInterfaceFactory $entityFactory,
        private readonly EventSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
    ) {}

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): EventInterface
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(
                __('A Base Event with id "%1" does not exist', $entityId),
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(EventInterface $entity): EventInterface
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the Base Event'),
                $exception,
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(EventInterface $entity): bool
    {
        try {
            $this->resource->delete($entity);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __($exception->getMessage()),
            );
        }

        return true;
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $entityId): bool
    {
        return $this->delete($this->getById($entityId));
    }

    public function getList(SearchCriteriaInterface $criteria): EventSearchResultsInterface
    {
        /** @var \Mygento\Base\Model\ResourceModel\Event\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var EventSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
