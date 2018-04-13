<?php
/**
 * @author Mygento
 * @package Mygento_Base
 */
namespace Mygento\Base\Model\ResourceModel;

class Event extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init(\Mygento\Base\Setup\InstallSchema::LOG_TABLE, 'id');
    }
}
