<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service\PreHandlers;

use Magento\Bundle\Model\Product\Type;
use Magento\Framework\Data\DataArray;
use Magento\Framework\DataObject;
use Magento\Framework\EntityManager\EventManager;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Mygento\Base\Api\Data\RecalculateResultInterface;
use Mygento\Base\Api\DiscountHelperInterface;
use Mygento\Base\Api\DiscountHelperInterfaceFactory;
use Mygento\Base\Api\RecalculationPreHandlerInterface;
use Mygento\Base\Model\Recalculator\ResultFactory;
use Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemsCollector;
use Mygento\Base\Test\OrderItemMock;
use Mygento\Base\Test\OrderMock;


use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;

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
     * @param OrderInterface|InvoiceInterface|CreditmemoInterface $entity
     * @throws \Exception
     * @return OrderInterface|InvoiceInterface|CreditmemoInterface
     */
    public function handle($entity)
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
        //TODO: to calculate GT It's necessary to figure out
        //is discount was applied after or before tax?
        $reduceGrandTotal = bcsub($reduceSubtotal, $reduceDiscountAmount, 4);


    }

    /**
     * @param OrderInterface|InvoiceInterface|CreditmemoInterface $entity
     * @return bool
     */
    public function isShouldBeApplied($entity): bool
    {
        $itemsToSkip = $this->skippedItemsCollector->getItemsToSkip($entity);

        return !empty($itemsToSkip);
    }
}
