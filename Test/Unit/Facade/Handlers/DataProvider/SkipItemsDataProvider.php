<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade\Handlers\DataProvider;

use Mygento\Base\Model\Mock\OrderMockBuilder;

class SkipItemsDataProvider
{
    public const TEST_1_NAME = '1. Simple Item, содержащий скидку, должен быть исключен. В заказе есть другие позиции.';
    public const TEST_2_NAME = '2. В заказе 1 позиция и она должна быть исключена.';
    public const TEST_3_NAME = '3. В заказе только позиции, которые должны быть исключены.';

    /**
     * @SuppressWarnings(PHPMD)
     */
    public static function dataProvider(): array
    {
        $final = [];
        $final[self::TEST_1_NAME] = self::test1();
        $final[self::TEST_2_NAME] = self::test2();
        $final[self::TEST_3_NAME] = self::test3();

        return $final;
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public static function dataProviderForVirtualOrder(): array
    {
        $final = [];
        $final[self::TEST_1_NAME] = self::test1();
        $final[self::TEST_2_NAME] = self::test2();

        return $final;
    }

    private static function test1(): array
    {
        $order = OrderMockBuilder::getNewOrderInstance(919.20, 924.20, 125.00, 0, -100);
        $order->setEntityId(100500);
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

        //Virtual order: should be the same order without skipped items
        $virtualOrder = OrderMockBuilder::getNewOrderInstance(799.20, 864.20, 125);
        $virtualOrder->setEntityId(100500);
        $virtualOrder->setDiscountAmount(-50);
        $virtualOrder->setTaxAmount(10);
        OrderMockBuilder::addItem($virtualOrder, $item2);

        return [$order, $expected, $virtualOrder];
    }

    private static function test2(): array
    {
        $order = OrderMockBuilder::getNewOrderInstance(120.00, 170, 100.00, 0, -50);
        $order->setEntityId(100500);
        //Simple, который содержит структурную скидку и делится нацело
        $item1 = OrderMockBuilder::getItem(120.00, 120.00, 50, 1)
            ->setTaxPercent(20.00)
            ->setTaxAmount(20.00)
            //key 'is_skipped' is used for testing. See Extra\TestItemSkipper
            ->setData('is_skipped', true);

        OrderMockBuilder::addItem($order, $item1);

        $expected = [
            'sum' => 0.0,
            'origGrandTotal' => 100.0,
            'items' => [
                'shipping' => [
                    'price' => 100.0,
                    'quantity' => 1.0,
                    'sum' => 100.0,
                    'tax' => '',
                ],
            ],
        ];

        //Virtual order: should be the same order without skipped items
        $virtualOrder = OrderMockBuilder::getNewOrderInstance(0.00, 100.0, 100);
        $virtualOrder->setEntityId(100500);

        return [$order, $expected, $virtualOrder];
    }

    private static function test3(): array
    {
        $order = OrderMockBuilder::getNewOrderInstance(919.20, 924.20, 125.00, 0, -100);
        $order->setEntityId(100600);
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
            ->setTaxAmount(123.20)
            ->setData('is_skipped', true);

        OrderMockBuilder::addItem($order, $item1);
        OrderMockBuilder::addItem($order, $item2);

        $expected = [
            'sum' => 0.0,
            'origGrandTotal' => 125.0,
            'items' => [
                'shipping' => [
                    'price' => 125.0,
                    'quantity' => 1.0,
                    'sum' => 125.0,
                    'tax' => '',
                ],
            ],
        ];

        //Virtual order: should be the same order without skipped items
        $virtualOrder = OrderMockBuilder::getNewOrderInstance(0.00, 125.0, 125);
        $virtualOrder->setEntityId(100600);

        return [$order, $expected];
    }
}
