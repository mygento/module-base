<?php


namespace Mygento\Base\Helper\Discount;

use Magento\Sales\Api\Data\OrderItemInterface;

class Tax
{
    public static function getDiscountAmountInclTax($item)
    {
        $discAmountInclTax = $item->getDiscountAmount();

        $taxAmount = $item instanceof OrderItemInterface
            ? $item->getTaxAmount()
            : $item->getOrderItem()->getTaxAmount();
        $taxPercent = $item instanceof OrderItemInterface
            ? $item->getTaxPercent()
            : $item->getOrderItem()->getTaxPercent();

        //В зависимости от настроек скидка может применятся до вычисления налога, а может и после
        //выражение не всегда справедливо:
        //RowTotalInclTax === RowTotal + TaxAmount

        // $discountTaxAmount = $rowTotalInclTax - $rowTotal -$taxAmount;
        $discountTaxAmount = bcsub(
            bcsub($item->getRowTotalInclTax(), $item->getRowTotal(), 4),
            $taxAmount,
            4
        );

        $isTaxCalculationNeeded = $taxPercent && $taxAmount && $discountTaxAmount &&
            $item->getRowTotal() !== $item->getRowTotalInclTax();

        if ($isTaxCalculationNeeded) {
            $discAmountInclTax = round((1 + $taxPercent / 100) * $item->getDiscountAmount(), 2);
        }

        return $discAmountInclTax;
    }
}
