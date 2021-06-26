<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper\Discount;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class Tax
{
    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $item
     * @return float|null
     */
    public static function getDiscountAmountInclTax($item)
    {
        $taxPercent = self::getItemTaxPercent($item);
        $discAmountInclTax = $item->getDiscountAmount();

        //В зависимости от настроек скидка может применятся до вычисления налога, а может и после
        if (self::isTaxCalculationNeeded($item)) {
            $discAmountInclTax = round((1 + $taxPercent / 100) * $item->getDiscountAmount(), 2);
        }

        return $discAmountInclTax;
    }

    /**
     * Calculate shipping discount with tax
     * @param CreditmemoInterface|InvoiceInterface|OrderInterface $entity
     * @return float
     */
    public static function getShippingDiscountAmountInclTax($entity)
    {
        $ratio = 1;

        //bccomp returns 0 if operands are equal
        $isShippingsEqual = bccomp($entity->getShippingAmount(), $entity->getShippingInclTax(), 2) === 0;
        $isShippingNotNull = bccomp($entity->getShippingAmount(), 0.00, 2) !== 0;

        if (!$isShippingsEqual && $isShippingNotNull) {
            $ratio = round($entity->getShippingInclTax() / $entity->getShippingAmount(), 2);
        }

        $shippingDiscount = $entity->getShippingDiscountAmount();

        $isOrder = $entity instanceof OrderInterface;
        if (!$isOrder) {
            $shippingDiscount = $entity->getOrder()->getShippingDiscountAmount();
        }

        //При различных настройках налогов Magento - налог на скидку доставки либо уже применен либо нет.
        //Если ShTA === (ShAmount - DiscShip) * 20% - то налог должен быть посчитан на скидку доставки
        //Если ShTA === ShAmount * 20% - то налог уже включен в скидку и доп расчет не нужен
        //где ShTA - shipping_tax_amount, ShAmount - shipping_amount, DiscShip - shipping_discount_amount
        $hasTaxInShippingDiscount = bccomp(
            $entity->getShippingTaxAmount(),
            $entity->getShippingAmount() * ($ratio - 1),
            2
        ) === 0;

        return $hasTaxInShippingDiscount
            ? $shippingDiscount
            : $shippingDiscount * $ratio;
    }

    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $item
     * @return float|mixed|null
     */
    public static function getItemTaxPercent($item)
    {
        return $item instanceof OrderItemInterface
            ? $item->getTaxPercent()
            : $item->getOrderItem()->getTaxPercent();
    }

    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $item
     * @return float|string|null
     */
    public static function getItemTaxAmount($item)
    {
        return $item instanceof OrderItemInterface
            ? $item->getTaxAmount()
            : $item->getOrderItem()->getTaxAmount();
    }

    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $item
     * @return bool
     */
    private static function isTaxCalculationNeeded($item): bool
    {
        $taxAmount = self::getItemTaxAmount($item);
        $taxPercent = self::getItemTaxPercent($item);

        return $taxPercent &&
            //bccomp returns 0 if operands are equal
            bccomp($taxAmount, '0.00', 2) === 0 &&
            $item->getRowTotal() !== $item->getRowTotalInclTax() &&
            //Bug NN-3475 Проверка остатка стоимости с налогом за вычетом скидки
            bccomp(($item->getRowTotalInclTax() - $item->getDiscountAmount()), '0.00', 2) != 0;
    }
}
