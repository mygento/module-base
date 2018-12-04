<?php

/**
 * @author Mygento Team
 * @copyright 2014-2018 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Controller\Adminhtml\Event;

class MassDelete extends \Mygento\Base\Controller\Adminhtml\Event
{
    /** @var \Magento\Ui\Component\MassAction\Filter */
    private $filter;

    /** @var \Mygento\Base\Model\ResourceModel\Event\CollectionFactory */
    private $collectionFactory;

    /**
     * @param \Mygento\Base\Model\ResourceModel\Event\CollectionFactory $collectionFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Mygento\Base\Api\EventRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\Base\Model\ResourceModel\Event\CollectionFactory $collectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Mygento\Base\Api\EventRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $entity) {
            $this->repository->delete($entity);
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $collectionSize)
        );
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
