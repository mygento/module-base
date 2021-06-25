<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service\PreHandlers\SkipItems;

use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface as OrderItem;
use Mygento\Base\Api\Data\RecalculateResultItemInterface;
use Mygento\Base\Api\DiscountHelperInterface as Discount;
use Mygento\Base\Helper\Discount\Tax;
use Mygento\Base\Model\Recalculator\ResultFactory;
use Mygento\Base\Test\OrderMockBuilder;

class SkippedItemFixer
{
    /**
     * @var \Mygento\Base\Api\DiscountHelperInterface
     */
    private $discountHelper;

    /**
     * @var \Mygento\Base\Model\Recalculator\ResultFactory
     */
    private $recalculateResultFactory;

    public function __construct(
        Discount $discountHelper,
        ResultFactory $recalculateResultFactory
    ) {
        $this->discountHelper = $discountHelper;
        $this->recalculateResultFactory = $recalculateResultFactory;
    }

    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $item
     * @throws \Exception
     * @return RecalculateResultItemInterface
     */
    public function execute($item): RecalculateResultItemInterface
    {
        $orderMock = $this->getDummyOrder($item);

        $rawResult = $this->discountHelper->getRecalculated($orderMock);
        $result = $this->recalculateResultFactory->create($rawResult);

        return $result->getItemById($item->getId());
    }

    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $sourceItem
     * @throws \Exception
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    private function getDummyOrder($sourceItem)
    {
        $rowTotalInclTax = $sourceItem->getRowTotalInclTax();
        $taxAmount = $sourceItem instanceof OrderItem
            ? $sourceItem->getTaxAmount()
            : $sourceItem->getOrderItem()->getTaxAmount();
        $taxPercent = $sourceItem instanceof OrderItem
            ? $sourceItem->getTaxPercent()
            : $sourceItem->getOrderItem()->getTaxPercent();

        $discountAmountInclTax = Tax::getDiscountAmountInclTax($sourceItem);

        $subTotal = $rowTotalInclTax;
        $grandTotal = bcsub($subTotal, $discountAmountInclTax, 4);
        $shippingInclTax = 0;

        $discountAmount = (-1) * $sourceItem->getDiscountAmount();

        $order = OrderMockBuilder::getNewOrderInstance($subTotal, $grandTotal, $shippingInclTax, 0, $discountAmount);
        $order->setTaxAmount($taxAmount);

        $item = OrderMockBuilder::getItem(
            $rowTotalInclTax,
            $sourceItem->getPriceInclTax(),
            $sourceItem->getDiscountAmount(),
            $sourceItem->getQty(),
            $sourceItem->getTaxPercent(),
            $taxAmount
        );
        $item->addData($sourceItem->getData());
        $item->setId($item->getItemId());
        $item->setTaxAmount($taxAmount);
        $item->setTaxPercent($taxPercent);

        OrderMockBuilder::addItem($order, $item);

        return $order;
    }
}
