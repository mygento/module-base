<?php

namespace Mygento\Base\Service\PreHandlers\SkipItems;

use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface as OrderItem;
use Mygento\Base\Api\Data\RecalculateResultItemInterface;
use Mygento\Base\Api\DiscountHelperInterface as Discount;
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
     * @param OrderItemInterface|InvoiceItemInterface|CreditmemoItemInterface $item
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
     * @param OrderItemInterface|InvoiceItemInterface|CreditmemoItemInterface $sourceItem
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

        $discountAmountInclTax = $this->getDiscountAmountInclTax($sourceItem);

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

    /**
     * @param $item
     * @return string
     * @see Discount::getItemDiscountAmountInclTax
     */
    private function getDiscountAmountInclTax($item)
    {
        $discAmountInclTax = $item->getDiscountAmount();

        $taxAmount = $item instanceof OrderItem
            ? $item->getTaxAmount()
            : $item->getOrderItem()->getTaxAmount();
        $taxPercent = $item instanceof OrderItem
            ? $item->getTaxPercent()
            : $item->getOrderItem()->getTaxPercent();

        //В зависимости от настроек скидка может применятся до вычисления налога, а может и после
        //выражение не всегда справедливо:
        //RowTotalInclTax === RowTotal + TaxAmount

        /* $discountTaxAmount = $rowTotalInclTax - $rowTotal -$taxAmount; */
        $discountTaxAmount = bcsub(bcsub($item->getRowTotalInclTax(), $item->getRowTotal(), 4), $taxAmount, 4);

        $isTaxCalculationNeeded = $taxPercent && $taxAmount && $discountTaxAmount &&
            $item->getRowTotal() !== $item->getRowTotalInclTax();

        if ($isTaxCalculationNeeded) {
            $discAmountInclTax = round((1 + $taxPercent / 100) * $item->getDiscountAmount(), 2);
        }

        return $discAmountInclTax;
    }
}
