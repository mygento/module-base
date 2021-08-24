<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service\PreHandlers\SkipItems;

use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Mygento\Base\Api\Data\RecalculateResultItemInterface;
use Mygento\Base\Api\DiscountHelperInterfaceFactory;
use Mygento\Base\Helper\Discount\Tax;
use Mygento\Base\Model\Mock\OrderMockBuilder;
use Mygento\Base\Model\Recalculator\ResultFactory;

class SkippedItemFixer
{
    /**
     * @var \Mygento\Base\Api\DiscountHelperInterfaceFactory
     */
    private $discountHelperFactory;

    /**
     * @var \Mygento\Base\Model\Recalculator\ResultFactory
     */
    private $recalculateResultFactory;

    public function __construct(
        DiscountHelperInterfaceFactory $discountHelperFactory,
        ResultFactory $recalculateResultFactory
    ) {
        $this->discountHelperFactory = $discountHelperFactory;
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

        $freshDiscountHelper = $this->discountHelperFactory->create();
        $rawResult = $freshDiscountHelper->getRecalculated($orderMock);
        $result = $this->recalculateResultFactory->create($rawResult);

        return $result->getItemById($item->getId());
    }

    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $sourceItem
     * @throws \Exception
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    private function getDummyOrder($sourceItem): OrderInterface
    {
        $rowTotalInclTax = $sourceItem->getRowTotalInclTax();
        $taxAmount = Tax::getItemTaxAmount($sourceItem);
        $taxPercent = Tax::getItemTaxPercent($sourceItem);

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
