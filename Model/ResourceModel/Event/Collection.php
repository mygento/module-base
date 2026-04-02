<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\ResourceModel\Event;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mygento\Base\Model\Event;
use Mygento\Base\Model\ResourceModel\Event as EventResource;

class Collection extends AbstractCollection
{
    /** @var string */
    protected $_idFieldName = EventResource::TABLE_PRIMARY_KEY;

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(
            Event::class,
            EventResource::class,
        );
    }
}
