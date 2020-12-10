<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model;

use Magento\Sales\Api\Data\OrderInterface;

class OrderRepository extends \Magento\Sales\Model\OrderRepository
{
    /**
     * Clear repository cache
     */
    public function clearCache()
    {
        $this->registry = [];
    }

    public function reloadOrder($orderId): OrderInterface
    {
        $this->clearCache();

        return $this->get($orderId);
    }
}
