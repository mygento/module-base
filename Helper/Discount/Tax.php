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
     * @return float|string|null
     */
    public static function getDiscountAmountInclTax($item)
    {
        if (self::canBeProcessedAsBundle($item)) {
            return self::getBundleDiscountAmountInclTax($item);
        }

        $discAmountInclTax = $item->getDiscountAmount();
        // В зависимости от настроек скидка может применяться до вычисления налога, а может и после
        if (self::isTaxCalculationNeeded($item)) {
            $taxPercent = self::getItemTaxPercent($item);
            $discAmountInclTax = round((1 + $taxPercent / 100) * $discAmountInclTax, 2);
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
        $isShippingsEqual = bccomp((string) $entity->getShippingAmount(), (string) $entity->getShippingInclTax(), 2) === 0;
        $isShippingNotNull = bccomp((string) $entity->getShippingAmount(), '0.00', 2) !== 0;

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
            (string) $entity->getShippingTaxAmount(),
            (string) ($entity->getShippingAmount() * ($ratio - 1)),
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
     * @return float|mixed|null
     */
    public static function getItemDiscountTaxCompensationAmount($item)
    {
        return $item instanceof OrderItemInterface
            ? $item->getDiscountTaxCompensationAmount()
            : $item->getOrderItem()->getDiscountTaxCompensationAmount();
    }

    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $item
     * @return string|null
     */
    public static function getItemProductType($item)
    {
        return $item instanceof OrderItemInterface
            ? $item->getProductType()
            : $item->getOrderItem()->getProductType();
    }

    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $item
     * @return array
     */
    public static function getItemChildrenItems($item)
    {
        return $item instanceof OrderItemInterface
            ? $item->getChildrenItems()
            : $item->getOrderItem()->getChildrenItems();
    }

    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $item
     * @return bool
     */
    private static function isTaxCalculationNeeded($item): bool
    {
        $taxAmount = self::getItemTaxAmount($item);
        $taxPercent = self::getItemTaxPercent($item);
        $discountTaxCompensationAmount = self::getItemDiscountTaxCompensationAmount($item);

        //В зависимости от настроек скидка может применятся до вычисления налога, а может и после
        //выражение не всегда справедливо:
        //RowTotalInclTax === RowTotal + TaxAmount + DiscountTaxCompensationAmount

        // $discountTaxAmount = $rowTotalInclTax - $rowTotal - $taxAmount - $discountTaxCompensationAmount;
        $discountTaxAmount = bcsub(bcsub(bcsub((string) $item->getRowTotalInclTax(), (string) $item->getRowTotal(), 4), (string) $taxAmount, 4), (string) $discountTaxCompensationAmount, 4);
        $isDiscountTaxAmountExist = $discountTaxAmount !== '0.0000';

        return $taxPercent &&
            (bccomp((string) $taxAmount, '0.00', 2) === 0 || $isDiscountTaxAmountExist) &&
            $item->getRowTotal() !== $item->getRowTotalInclTax() &&
            bccomp((string) ($item->getRowTotalInclTax() - $item->getDiscountAmount()), '0.00', 2) != 0;
    }

    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $item
     * @return bool
     */
    private static function canBeProcessedAsBundle($item)
    {
        return !floatval($item->getDiscountAmount())
            && self::getItemProductType($item) === 'bundle'
            && self::getItemChildrenItems($item);
    }

    /**
     * @param CreditmemoItemInterface|InvoiceItemInterface|OrderItemInterface $item
     * @return string
     */
    private static function getBundleDiscountAmountInclTax($item)
    {
        $discAmountInclTax = '0.0000';
        foreach (self::getItemChildrenItems($item) as $childItem) {
            $childDiscAmountInclTax = $childItem->getDiscountAmount();
            // В зависимости от настроек скидка может применяться до вычисления налога, а может и после
            if (self::isTaxCalculationNeeded($childItem)) {
                $taxPercent = self::getItemTaxPercent($childItem);
                $childDiscAmountInclTax = round((1 + $taxPercent / 100) * $childDiscAmountInclTax, 2);
            }

            $discAmountInclTax = bcadd((string) $discAmountInclTax, (string) $childDiscAmountInclTax, 4);
        }

        return $discAmountInclTax;
    }
}
