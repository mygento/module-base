<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Controller\Adminhtml\Cron;

class Schedule extends \Magento\Backend\App\Action
{
    /**
     * @var \Mygento\Base\Helper\Cron
     */
    private $cronHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonResult;

    /**
     * @param \Mygento\Base\Helper\Cron $cronHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResult
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\Base\Helper\Cron $cronHelper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResult,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->jsonResult = $jsonResult;
        $this->cronHelper = $cronHelper;
    }

    /**
     * Execute action based on request and return result
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $resultJson = $this->jsonResult->create();

        $job = $this->getRequest()->getParam('job', null);
        if (!$job) {
            return $resultJson->setData([
                'errorMessage' => __('Job Code is required'),
            ]);
        }

        if ($this->cronHelper->isRunning($job)) {
            return $resultJson->setData([
                'success' => true,
                'successText' => __('Scheduled or Running'),
            ]);
        }

        $this->cronHelper->addSchedule($job);

        return $resultJson->setData([
            'success' => true,
            'successText' => __('Scheduled'),
        ]);
    }
}
