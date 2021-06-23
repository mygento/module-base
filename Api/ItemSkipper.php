<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api;

use \Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Interface ItemSkipper
 * Check is OrderItem should be skipped
 * from recalculation
 */
interface ItemSkipper
{
    /**
     * @param OrderItemInterface $item
     * @return bool
     */
    public function isShouldBeSkipped(OrderItemInterface $item): bool;
}
