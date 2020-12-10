<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper\Discount;

class Math
{
    /**
     * Custom floor() function
     * @param float $val
     * @param int $precision
     * @return float|int
     */
    public static function slyFloor($val, $precision = 2)
    {
        $factor = 1.00;
        $divider = pow(10, $precision);

        if ($val < 0) {
            $factor = -1.00;
        }

        return (floor(abs($val) * $divider) / $divider) * $factor;
    }

    /**
     * Custom ceil() function
     * @param float $val
     * @param int $precision
     * @return float|int
     */
    public static function slyCeil($val, $precision = 2)
    {
        $factor = 1.00;
        $divider = pow(10, $precision);

        if ($val < 0) {
            $factor = -1.00;
        }

        return (ceil(abs($val) * $divider) / $divider) * $factor;
    }

    /**
     * @param int $x
     * @param int $y
     * @return int
     */
    public static function getDecimalsCountAfterDiv($x, $y): int
    {
        $divRes = (string) round($x / $y, 20);

        $pos = strrchr($divRes, '.');

        return $pos !== false ? strlen($pos) - 1 : 0;
    }

    /**
     * Calculates how many kopeyki can be added to item
     * considering number of items, rowTotal and rowDiscount
     * @param int $amountToSpread (in kops)
     * @param int $itemsCount
     * @param float $itemTotal
     * @param float $itemDiscount
     * @return int
     */
    public static function getDiscountIncrement($amountToSpread, $itemsCount, $itemTotal, $itemDiscount)
    {
        $sign = (int) ($amountToSpread / abs($amountToSpread));

        //Пытаемся размазать поровну
        $discPerItem = (int) (abs($amountToSpread) / $itemsCount);
        $inc = ($discPerItem > 1) && ($itemTotal - $itemDiscount) > $discPerItem
            ? $sign * $discPerItem
            : $sign;

        //Изменяем скидку позиции
        if (($itemTotal - $itemDiscount) > abs($inc)) {
            return $inc;
        }

        return 0;
    }
}
