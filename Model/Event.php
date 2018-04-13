<?php
/**
 * @author Mygento
 * @package Mygento_Base
 */
namespace Mygento\Base\Model;

use Magento\Framework\Model\AbstractModel;

class Event extends AbstractModel implements \Mygento\Base\Api\Data\EventInterface
{
    protected function _construct()
    {
        $this->_init(\Mygento\Base\Model\ResourceModel\Event::class);
    }
}
