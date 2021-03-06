<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade\SkipItem;

use Mygento\Base\Test\OrderMockBuilder;

class HandlerDataProvider
{
    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public static function dataProvider(): array
    {
        $final = [];

        $order = OrderMockBuilder::getNewOrderInstance(919.20, 924.20, 125.00, 0, -100);
        //Simple, который содержит структурную скидку и делится нацело
        //Скидка применена ДО начисления налога.
        $item1 = OrderMockBuilder::getItem(120.00, 120.00, 50, 1)
            ->setRowTotal(100.00)
            ->setTaxPercent(20.00)
            ->setTaxAmount(10.00)
            //key 'is_skipped' is used for testing. See Extra\TestItemSkipper
            ->setData('is_skipped', true);
        $item2 = OrderMockBuilder::getItem(799.20, 799.20, 50, 1)
            ->setRowTotal(666.00)
            ->setTaxPercent(20.00)
            ->setTaxAmount(123.20);

        OrderMockBuilder::addItem($order, $item1);
        OrderMockBuilder::addItem($order, $item2);

        $expected = [
            'sum' => 739.20,
            'origGrandTotal' => 864.2,
            'items' => [
                //Item with priceInclTax=120 is absent!
                101402 => [
                    'price' => 739.2,
                    'quantity' => 1.0,
                    'sum' => 739.2,
                    'tax' => '',
                ],
                'shipping' => [
                    'price' => 125.0,
                    'quantity' => 1.0,
                    'sum' => 125.0,
                    'tax' => '',
                ],
            ],
        ];

        $final['1. Simple Item, содержащий скидку, должен быть исключен.'] = [$order, $expected];

        return $final;
    }
}
