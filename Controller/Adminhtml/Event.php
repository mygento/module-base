<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Controller\Adminhtml;

abstract class Event extends \Magento\Backend\App\Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mygento_Base::base_event';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Event repository
     *
     * @var \Mygento\Base\Api\EventRepositoryInterface
     */
    protected $repository;

    /**
     * @param \Mygento\Base\Api\EventRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\Base\Api\EventRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mygento_Base::event');
        //->addBreadcrumb(__('Event'), __('Event'));
        return $resultPage;
    }
}
