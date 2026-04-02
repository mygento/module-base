<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Event extends AbstractDb
{
    public const TABLE_NAME = 'mygento_base_event';
    public const TABLE_PRIMARY_KEY = 'id';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::TABLE_PRIMARY_KEY);
    }
}
