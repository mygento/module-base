<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service\PreHandlers;

use Magento\Sales\Api\Data\OrderInterface;
use Mygento\Base\Api\RecalculationPreHandlerInterface;
use Mygento\Base\Helper\Discount\Tax;
use Mygento\Base\Model\Mock\OrderMockBuilder;
use Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemsCollector;

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
        $reduceDiscountAmountInclTax = 0;
        $reduceTaxAmount = 0;

        foreach ($itemsToSkip as $item) {
            $reduceSubtotal += $item->getRowTotalInclTax();
            $reduceDiscountAmount += $item->getDiscountAmount();
            $reduceDiscountAmountInclTax += Tax::getDiscountAmountInclTax($item);
            $reduceTaxAmount += $item->getTaxAmount();
        }

        $reduceGrandTotal = bcsub($reduceSubtotal, $reduceDiscountAmountInclTax, 4);

        $newGrandTotal = bcsub($entity->getGrandTotal(), $reduceGrandTotal, 4);
        $newSubTotalInclTax = bcsub($entity->getSubtotalInclTax(), $reduceSubtotal, 4);
        //DiscountAmount has different signs in order and orderItem
        $newDiscountAmount = bcadd($entity->getDiscountAmount(), $reduceDiscountAmount, 4);
        $newTaxAmount = bcsub($entity->getTaxAmount(), $reduceTaxAmount, 4);

        //Create mock Order
        $orderSkippedLess = OrderMockBuilder::getNewOrderInstance(0, 0, 0);
        $orderSkippedLess->setData($entity->getData());
        $orderSkippedLess->setPayment($entity->getPayment());
        $orderSkippedLess->setSubtotalInclTax($newSubTotalInclTax);
        $orderSkippedLess->setGrandTotal($newGrandTotal);
        $orderSkippedLess->setDiscountAmount($newDiscountAmount);
        $orderSkippedLess->setTaxAmount($newTaxAmount);
        $orderSkippedLess->setItems([]);
        $orderSkippedLess->setAllItems([]);

        $itemIdsToSkip = $this->skippedItemsCollector->getItemIdsToSkip($entity);
        foreach ($entity->getItems() as $item) {
            if (in_array((int) $item->getId(), $itemIdsToSkip, true)) {
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
    public function shouldBeApplied(OrderInterface $entity): bool
    {
        $itemsToSkip = $this->skippedItemsCollector->getItemsToSkip($entity);

        return !empty($itemsToSkip);
    }
}
