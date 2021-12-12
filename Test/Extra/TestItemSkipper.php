<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Extra;

use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Class TestItemSkipper is for testing purposes.
 * It specifies which items should be skipped from recalculation.
 */
class TestItemSkipper implements \Mygento\Base\Api\ItemSkipper
{
    /**
     * @inheritDoc
     */
    public function shouldBeSkipped(OrderItemInterface $item): bool
    {
        return (bool) $item->getData('is_skipped');
    }
}
