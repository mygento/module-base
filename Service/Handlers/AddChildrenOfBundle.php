<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service\Handlers;

use Magento\Bundle\Model\Product\Type;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderInterface as Order;
use Magento\Sales\Api\Data\OrderItemInterface;
use Mygento\Base\Api\Data\RecalculateResultInterface;
use Mygento\Base\Api\DiscountHelperInterface;
use Mygento\Base\Api\DiscountHelperInterfaceFactory;
use Mygento\Base\Api\RecalculationHandler;
use Mygento\Base\Model\Recalculator\ResultFactory;
use Mygento\Base\Test\OrderItemMock;
use Mygento\Base\Test\OrderMock;

/**
 * Class AddChildrenOfBundle
 * Этот класс пересчитывает дочерние продукты для бандлов,
 * чтобы их цена тоже соответствовала пересчитанному родителю
 */
class AddChildrenOfBundle implements RecalculationHandler
{
    /**
     * @var \Mygento\Base\Api\DiscountHelperInterfaceFactory
     */
    private $discountHelperFactory;

    /**
     * @var \Mygento\Base\Model\Recalculator\ResultFactory
     */
    private $recalculateResultFactory;

    /**
     * @param \Mygento\Base\Api\DiscountHelperInterfaceFactory $discountHelperFactory
     * @param \Mygento\Base\Model\Recalculator\ResultFactory $recalculateResultFactory
     */
    public function __construct(
        DiscountHelperInterfaceFactory $discountHelperFactory,
        ResultFactory $recalculateResultFactory
    ) {
        $this->discountHelperFactory = $discountHelperFactory;
        $this->recalculateResultFactory = $recalculateResultFactory;
    }

    /**
     * @param Order $order
     * @param RecalculateResultInterface $recalcOriginal
     * @throws \Mygento\Base\Model\Recalculator\RecalculationException
     * @throws \Exception
     * @return RecalculateResultInterface
     */
    public function handle(Order $order, RecalculateResultInterface $recalcOriginal): RecalculateResultInterface
    {
        $items = $order->getAllVisibleItems() ?? $order->getAllItems();

        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($items as $item) {
            //Look for the bundle
            if ($item->getProductType() !== Type::TYPE_CODE) {
                continue;
            }

            $dummyOrder = $this->getDummyOrderBasedOnBundle($item, $recalcOriginal);

            $freshDiscountHelper = $this->discountHelperFactory->create();
            $freshDiscountHelper->setSpreadDiscOnAllUnits(true);

            $discountData = $freshDiscountHelper->getRecalculated($dummyOrder);
            $childrenResult = $this->recalculateResultFactory->create($discountData);

            $this->updateParentItem($item, $recalcOriginal, $childrenResult);
            $this->updateSum($recalcOriginal);
            $this->updateShippingAmount($recalcOriginal, $childrenResult);
            $this->updateExtraDiscountsOfChildren($item, $recalcOriginal, $dummyOrder);
        }

        return $recalcOriginal;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $parentItem
     * @param RecalculateResultInterface $recalcOriginal
     * @throws \Exception
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    private function getDummyOrderBasedOnBundle($parentItem, $recalcOriginal)
    {
        //DynamicPrice Enabled - в этом случае чайлды заполнены.
        //цены содержатся и в них и в паренте (как сумма чайлдов)
        //скидки только в чайлдах
        $isDynamicPriceEnabled = $parentItem->isChildrenCalculated();

        if ($isDynamicPriceEnabled) {
            return $this->getDummyOrderWithDynamicPrice($parentItem, $recalcOriginal);
        }

        //DynamicPrice Disabled - в этом случае чайлды пусты.
        //ни цен, ни скидок в них нет.
        return $this->getDummyOrderWithoutDynamicPrice($parentItem, $recalcOriginal);
    }

    /**
     * Вот это всё для случая когда `$isDynamicPriceEnabled === TRUE`
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $parentItem
     * @param \Mygento\Base\Api\Data\RecalculateResultInterface $recalcOriginal
     * @throws \Exception
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    private function getDummyOrderWithDynamicPrice($parentItem, $recalcOriginal)
    {
        $order = new OrderMock([]);

        $parentItemRecalculated = $recalcOriginal->getItemById($parentItem->getItemId());
        if ($parentItemRecalculated === null) {
            throw new \Exception("Parent bundle with id {$parentItem->getItemId()} not recalculated");
        }

        //Эта сумма должна быть распределена между дочерними позициями
        $grandTotal = $parentItemRecalculated->getSum();

        $st = 0.00;
        $discountOfBundle = 0.00;
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $child */
        foreach ($parentItem->getChildrenItems() as $child) {
            $item = new OrderItemMock();
            $item->setData('id', $child->getItemId());
            $item->setData('name', $child->getName());
            $item->setData('row_total_incl_tax', $child->getRowTotalInclTax());
            $item->setData('price_incl_tax', $child->getPriceInclTax());
            $item->setData('discount_amount', $child->getDiscountAmount());
            $item->setData('qty', $child->getQtyOrdered());
            $item->setData('tax_percent', $child->getTaxPercent());
            $item->setData('tax_amount', $child->getTaxAmount());
            $this->addItem($order, $item);

            $discountOfBundle += $child->getDiscountAmount();

            $st += $item->getRowTotalInclTax();
        }

        $order->setData('subtotal_incl_tax', $st);
        $order->setData('grand_total', $grandTotal);
        $order->setData('shipping_incl_tax', 0.00);
        $order->setData('discount_amount', 0.00);

        //Скидка на весь виртуальный заказ
        $discountAmount = bcsub($order->getSubtotalInclTax(), $order->getGrandTotal(), 4);
        $order->setData(DiscountHelperInterface::DA_INCL_TAX, $discountAmount);

        return $order;
    }

    /**
     * Вот это всё для случая когда `$isDynamicPriceEnabled === FALSE`
     * Все данные о ценах и скидках в чайлдах отсутствуют
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $parentItem
     * @param \Mygento\Base\Api\Data\RecalculateResultInterface $recalcOriginal
     * @throws \Exception
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    private function getDummyOrderWithoutDynamicPrice($parentItem, $recalcOriginal)
    {
        $order = new OrderMock([]);
        $subtotal = 0.00;

        $parentItemRecalculated = $recalcOriginal->getItemById($parentItem->getItemId());
        if ($parentItemRecalculated === null) {
            throw new \Exception("Parent bundle with id {$parentItem->getItemId()} not recalculated");
        }

        //Эта сумма должна быть распределена между дочерними позициями
        $grandTotal = $parentItemRecalculated->getSum();

        $numberChildren = count($parentItem->getChildrenItems());

        /** @var \Magento\Sales\Api\Data\OrderItemInterface $child */
        foreach ($parentItem->getChildrenItems() as $child) {
            $item = new OrderItemMock();
            $qty = $child->getQtyOrdered();

            //Считаем виртуальные цены и виртуальный subtotal
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            $price = $child->getProduct()
                ? $child->getProduct()->getFinalPrice()
                : $grandTotal / ($numberChildren * $qty);

            $item->setData('id', $child->getItemId());
            $item->setData('name', $child->getName());
            $item->setData('qty', $qty);
            $item->setData('row_total_incl_tax', $qty * $price);
            $item->setData('price_incl_tax', $price);
            $this->addItem($order, $item);

            $subtotal += $item->getRowTotalInclTax();
        }

        $order->setSubtotalInclTax($subtotal);
        $order->setSubtotal($subtotal);
        $order->setGrandTotal($grandTotal);

        //Скидка на весь виртуальный заказ
        $discountAmount = bcsub($order->getSubtotalInclTax(), $order->getGrandTotal(), 4);
        $order->setData(DiscountHelperInterface::DA_INCL_TAX, $discountAmount);

        return $order;
    }

    /**
     * @param OrderInterface $order
     * @param OrderItemInterface $item
     */
    private function addItem($order, $item): void
    {
        $items = (array) $order->getData('all_items');
        $items[] = $item;

        $order->setData('all_items', $items);
        $order->setData('items', $items);
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $parentItem
     * @param \Mygento\Base\Api\Data\RecalculateResultInterface $recalcOriginalObject
     * @param \Mygento\Base\Api\Data\RecalculateResultInterface $childrenResultObject
     * @throws \Mygento\Base\Model\Recalculator\RecalculationException
     * @return \Mygento\Base\Api\Data\RecalculateResultInterface
     */
    private function updateParentItem(
        OrderItemInterface $parentItem,
        RecalculateResultInterface $recalcOriginalObject,
        RecalculateResultInterface $childrenResultObject
    ): RecalculateResultInterface {
        $parentRecalculateItem = $recalcOriginalObject->getItemById($parentItem->getItemId());
        $children = $childrenResultObject->getItems();
        unset($children['shipping']);

        $newParentPrice = $childrenResultObject->getSum() / $parentRecalculateItem->getQuantity();
        $newParentSum = $childrenResultObject->getSum();

        $parentRecalculateItem->setChildren($children);
        $parentRecalculateItem->setPrice($newParentPrice);
        $parentRecalculateItem->setSum($newParentSum);

        return $recalcOriginalObject;
    }

    /**
     * @param \Mygento\Base\Api\Data\RecalculateResultInterface $recalcOriginal
     */
    private function updateSum(RecalculateResultInterface $recalcOriginal): void
    {
        $newSum = array_sum(
            array_map(
                static function ($item, $key) {
                    return $key !== 'shipping' ? $item->getSum() : 0;
                },
                $recalcOriginal->getItems(),
                array_keys($recalcOriginal->getItems())
            )
        );

        $recalcOriginal->setSum($newSum);
    }

    /**
     * @param \Mygento\Base\Api\Data\RecalculateResultInterface $recalcOriginal
     * @param \Mygento\Base\Api\Data\RecalculateResultInterface $childrenResult
     */
    private function updateShippingAmount(
        RecalculateResultInterface $recalcOriginal,
        RecalculateResultInterface $childrenResult
    ): void {
        $childShipping = $childrenResult->getItemById('shipping');
        $masterShipping = $recalcOriginal->getItemById('shipping');

        $newShippingAmount = bcadd($masterShipping->getPrice(), $childShipping->getPrice(), 4);
        $masterShipping->setPrice($newShippingAmount);
        $masterShipping->setSum($newShippingAmount);
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $parentItem
     * @param \Mygento\Base\Api\Data\RecalculateResultInterface $recalcOriginalObject
     * @param \Magento\Sales\Api\Data\OrderInterface $dummyOrder
     */
    private function updateExtraDiscountsOfChildren(
        OrderItemInterface $parentItem,
        RecalculateResultInterface $recalcOriginalObject,
        OrderInterface $dummyOrder
    ) {
        $recalculatedItem = $recalcOriginalObject->getItemById($parentItem->getItemId());
        $freshDiscountHelper = $this->discountHelperFactory->create();
        $freshDiscountHelper->setSpreadDiscOnAllUnits(true);

        $extraAmounts = [
            'reward_currency_amount',
            'gift_cards_amount',
            'customer_balance_amount',
        ];

        foreach ($extraAmounts as $extraAmountKey) {
            $extraAmount = $recalculatedItem->getData($extraAmountKey);

            //Do nothing if $extraAmount === 0
            if (bccomp((string) $extraAmount, '0.00', 2) === 0) {
                continue;
            }
            $dummyOrder->setData($extraAmountKey, $extraAmount);

            $freshDiscountHelper->applyDiscount($dummyOrder, (float) $extraAmount);

            $sum = 0;
            foreach ($dummyOrder->getAllItems() as $item) {
                $amount = $item->getData(DiscountHelperInterface::NAME_ROW_AMOUNT_TO_SPREAD);
                $sum += $amount;
                $recalcOriginalObject->getItemById($item->getId())[$extraAmountKey] = $amount;
            }

            $recalculatedItem->setData($extraAmountKey, $sum);
        }
    }
}
