<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Mock;

use Magento\Sales\Api\Data\OrderInterface;

class OrderMockBuilder
{
    private const CHARS_LOWERS = 'abcdefghijklmnopqrstuvwxyz';
    private const CHARS_UPPERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const CHARS_DIGITS = '0123456789';

    /**
     * @param float $subTotalInclTax
     * @param float $grandTotal
     * @param float $shippingInclTax
     * @param float $rewardPoints
     * @param float|null $discountAmount
     * @return \Mygento\Base\Model\Mock\OrderMock
     */
    public static function getNewOrderInstance(
        $subTotalInclTax,
        $grandTotal,
        $shippingInclTax,
        $rewardPoints = 0.00,
        $discountAmount = null
    ): OrderInterface {
        $order = new OrderMock();
        $payment = new OrderPaymentMock();

        $order->setData('subtotal_incl_tax', $subTotalInclTax);
        $order->setData('grand_total', $grandTotal);
        $order->setData('shipping_incl_tax', $shippingInclTax);
        $order->setData('reward_currency_amount', $rewardPoints);
        $order->setData(
            'discount_amount',
            $discountAmount ??
            $grandTotal + $rewardPoints - $subTotalInclTax - $shippingInclTax
        );
        $order->setPayment($payment);

        return $order;
    }

    /**
     * @param float $rowTotalInclTax
     * @param float $priceInclTax
     * @param float $discountAmount
     * @param int $qty
     * @param int $taxPercent
     * @param float|int $taxAmount
     * @param float|null $rowTotal
     * @return \Mygento\Base\Model\Mock\OrderItemMock
     */
    public static function getItem(
        $rowTotalInclTax,
        $priceInclTax,
        $discountAmount,
        $qty = 1,
        $taxPercent = 0,
        $taxAmount = 0,
        $rowTotal = null
    ): OrderItemMock {
        static $id = 100500;
        $id++;

        $name = self::getRandomString(8);

        $item = new OrderItemMock();

        $item->setData('id', $id);
        $item->setData('item_id', $id);
        $item->setData('row_total_incl_tax', $rowTotalInclTax);
        $item->setData('price_incl_tax', $priceInclTax);
        $item->setData('discount_amount', $discountAmount);
        $item->setData('qty', $qty);
        $item->setData('qty_ordered', $qty);
        $item->setData('name', $name);
        $item->setData('tax_percent', $taxPercent);
        $item->setData('tax_amount', $taxAmount);
        $item->setData('row_total', $rowTotal === null ? ($rowTotalInclTax - $taxAmount) : $rowTotal);

        return $item;
    }

    public static function addItem($order, $item)
    {
        $items = (array) $order->getItems();
        $items[] = $item;

        $order->setData('items', $items);
        $order->setData('all_items', $items);
    }

    public static function getRandomString($len, $chars = null)
    {
        if (is_null($chars)) {
            $chars = self::CHARS_LOWERS . self::CHARS_UPPERS . self::CHARS_DIGITS;
        }
        for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
            $str .= $chars[random_int(0, $lc)];
        }

        return $str;
    }
}
