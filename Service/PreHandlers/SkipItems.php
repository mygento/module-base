<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service\PreHandlers;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Mygento\Base\Api\RecalculationPreHandlerInterface;
use Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemsCollector;
use Mygento\Base\Test\OrderMockBuilder;

class SkipItems implements RecalculationPreHandlerInterface
{
    /**
     * @var \Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemsCollector
     */
    private $skippedItemsCollector;

    public function __construct(SkippedItemsCollector $skippedItemsCollector)
    {
        $this->skippedItemsCollector = $skippedItemsCollector;
    }

    /**
     * Create mock order without skipped items
     *
     * @param OrderInterface $entity
     * @throws \Exception
     * @return OrderInterface
     */
    public function handle(OrderInterface $entity): OrderInterface
    {
        $itemsToSkip = $this->skippedItemsCollector->getItemsToSkip($entity);

        if (empty($itemsToSkip)) {
            return $entity;
        }

        $reduceSubtotal = 0;
        $reduceDiscountAmount = 0;
        $reduceTaxAmount = 0;
        /** @var OrderItemInterface $item */
        foreach ($itemsToSkip as $item) {
            $reduceSubtotal += $item->getRowTotalInclTax();
            $reduceDiscountAmount += $item->getDiscountAmount();
            $reduceTaxAmount += $item->getTaxAmount();
        }
        //Случай, когда DA применяется до вычисления налога
        $reduceDiscountAmountWithTax = $reduceDiscountAmount * 6 / 5; //20%

        //TODO: to calculate GT It's necessary to figure out
        //is discount was applied after or before tax?
        $reduceGrandTotal = bcsub($reduceSubtotal, $reduceDiscountAmountWithTax, 4);

        $newGrandTotal = bcsub($entity->getGrandTotal(), $reduceGrandTotal, 4);
        $newSubTotalInclTax = bcsub($entity->getSubtotalInclTax(), $reduceSubtotal, 4);
        //DiscountAmount в order и orderItem имеют разные знаки
        $newDiscountAmount = bcadd($entity->getDiscountAmount(), $reduceDiscountAmount, 4);

        //Create mock Order
        $orderSkippedLess = OrderMockBuilder::getNewOrderInstance(0, 0, 0);
        $orderSkippedLess->setData($entity->getData());
        $orderSkippedLess->setSubtotalInclTax($newSubTotalInclTax);
        $orderSkippedLess->setGrandTotal($newGrandTotal);
        $orderSkippedLess->setGrandTotal($newGrandTotal);
        $orderSkippedLess->setDiscountAmount($newDiscountAmount);
        //TODO
        //$orderSkippedLess->setTaxAmount();
        $orderSkippedLess->setItems([]);

        $itemIdsToSkip = $this->skippedItemsCollector->getItemIdsToSkip($entity);
        foreach ($entity->getItems() as $item) {
            if ($item->isDummy()) {
                continue;
            }

            if (in_array((int)$item->getId(), $itemIdsToSkip, true)) {
                continue;
            }

            OrderMockBuilder::addItem($orderSkippedLess, $item);
        }

        return $orderSkippedLess;
    }

    /**
     * @param OrderInterface $entity
     * @return bool
     */
    public function isShouldBeApplied(OrderInterface $entity): bool
    {
        $itemsToSkip = $this->skippedItemsCollector->getItemsToSkip($entity);

        return !empty($itemsToSkip);
    }
}
