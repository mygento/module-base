<?php
/**
 * @author Mygento
 * @package Mygento_Base
 */
namespace Mygento\Base\Model;

class EventRepository implements \Mygento\Base\Api\EventRepositoryInterface
{
    public function __construct(
        \Mygento\Base\Model\ResourceModel\Event $resource,
        \Mygento\Base\Model\ResourceModel\Event\CollectionFactory $collectionFactory,
        \Mygento\Base\Model\EventFactory $eventFactory,
        \Mygento\Base\Api\Data\EventSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->eventFactory = $eventFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function delete(\Mygento\Base\Api\Data\EventInterface $event)
    {
        try {
            $this->resource->delete($event);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __($exception->getMessage())
            );
        }
        return true;
    }

    public function getById($eventId)
    {
        $event = $this->eventFactory->create();
        $this->resource->load($event, $eventId);
        if (!$event->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Event log with id "%1" does not exist.', $eventId)
            );
        }
        return $event;
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        // @var \Mygento\Base\Model\ResourceModel\Event\Collection $collection

        $collection = $this->collectionFactory->create();

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    public function save(\Mygento\Base\Api\Data\EventInterface $event)
    {
        try {
            $this->resource->save($event);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __($exception->getMessage())
            );
        }
        return $event;
    }
}
