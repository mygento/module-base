<?php
/**
 * @author Mygento
 * @package Mygento_Base
 */
namespace Mygento\Base\Model\ResourceModel\Event;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Mygento\Base\Model\Event::class,
            \Mygento\Base\Model\ResourceModel\Event::class
        );
    }
}
