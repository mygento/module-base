<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper;

class Cron extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;

    /**
     * @var \Magento\Cron\Model\ScheduleFactory
     */
    private $entity;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory
     */
    private $collection;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Cron\Model\ScheduleFactory $entity
     * @param \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $collection
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Cron\Model\ScheduleFactory $entity,
        \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $collection,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->collection = $collection;
        $this->dateTime = $dateTime;
        $this->entity = $entity;
        $this->timezone = $timezone;
    }

    /**
     * @param string $jobCode
     * @return bool
     */
    public function isRunning(string $jobCode): bool
    {
        $collection = $this->collection->create();
        $collection->addFieldToFilter('job_code', $jobCode);
        $collection->addFieldToFilter('status', ['in' => [
            \Magento\Cron\Model\Schedule::STATUS_PENDING,
            \Magento\Cron\Model\Schedule::STATUS_RUNNING,
        ]]);

        if ($collection->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param string $jobCode
     */
    public function addSchedule(string $jobCode)
    {
        $this->clearOldSchedules($jobCode);
        $schedule = $this->entity->create();
        $schedule->addData([
            'job_code' => $jobCode,
            'status' => \Magento\Cron\Model\Schedule::STATUS_PENDING,
            'created_at' => date('Y-m-d H:i:s', $this->dateTime->timestamp()),
            'scheduled_at' => date('Y-m-d H:i:s', $this->dateTime->timestamp()),
        ]);
        $schedule->save();
    }

    /**
     * @param string $jobCode
     * @return string
     */
    public function getLastUpdate(string $jobCode)
    {
        $collection = $this->collection->create();
        $collection->addFieldToFilter('job_code', $jobCode);
        $collection->addFieldToFilter(
            'status',
            ['eq' => \Magento\Cron\Model\Schedule::STATUS_SUCCESS]
        );
        $collection->setPageSize(1);
        $collection->addOrder('finished_at', $collection::SORT_ORDER_DESC);

        if ($collection->getSize() < 1) {
            return __('Never');
        }

        $date = $this->timezone->date(
            new \DateTime($collection->getFirstItem()->getFinishedAt())
        );

        return $date->format('d.m.Y H:i:s');
    }

    /**
     * @param string $jobCode
     */
    private function clearOldSchedules(string $jobCode)
    {
        $schedules = $this->collection->create();
        $schedules->addFieldToFilter('job_code', $jobCode);
        $schedules->addFieldToFilter(
            'status',
            ['in' => [\Magento\Cron\Model\Schedule::STATUS_PENDING]]
        );
        $schedules->walk('delete');
    }
}
