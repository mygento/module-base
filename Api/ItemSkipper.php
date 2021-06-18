<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api;

use \Magento\Sales\Api\Data\OrderItemInterface;
use \Magento\Sales\Api\Data\InvoiceItemInterface;
use \Magento\Sales\Api\Data\CreditmemoItemInterface;

/**
 * Interface ItemSkipper
 * Check is OrderItem (InvoiceItem, CreditmemoItem) should be skipped
 * from recalculation
 */
interface ItemSkipper
{
    /**
     * @param OrderItemInterface|InvoiceItemInterface|CreditmemoItemInterface $item
     * @return bool
     */
    public function isShouldBeSkipped($item): bool;
}
