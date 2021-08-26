<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade\AllHandlers;

use Magento\Bundle\Model\Product\Type as Bundle;
use Mygento\Base\Test\OrderMockBuilder;

class StepByStepBundleDataProvider
{
    public static function provide(): array
    {
        $final = [];
        $final['1. Заказ с 1 бандлом DynamicPrice = Disabled. Оплата Gift Card полная вкл доставку'] = self::test1();
        $final['2. Заказ с 1 бандлом DynamicPrice = Enabled. Оплата Gift Card полная вкл доставку'] = self::test2();
        $final['3. Заказ с 1 бандлом DynamicPrice = Disabled. Оплата Gift Card полная вкл доставку. Есть скидка.'] = self::test3();
        $final['4. Заказ с 1 бандлом DynamicPrice = Enabled. Оплата Gift Card полная вкл доставку. Есть скидка.'] = self::test4();

        return $final;
    }

    private static function test1(): array
    {
        $order = OrderMockBuilder::getNewOrderInstance(1293.6, 0.60, 200.0000, 0, 0);
        $order->setData('gift_cards_amount', 1493);

        $father = OrderMockBuilder::getItem(1293.6000, 1293.6000, 0);
        $father->setProductType(Bundle::TYPE_CODE);
        $child1 = OrderMockBuilder::getItem(null, null, 0);
        $child2 = OrderMockBuilder::getItem(null, null, 0);
        $father->setChildrenItems([$child1, $child2]);
        OrderMockBuilder::addItem($order, $father);

        //Virtual order based on bundle product
        $virtualOrder = OrderMockBuilder::getNewOrderInstance(1293.6, 0, 0);
        $virtualOrder->setDiscountAmount(0);
        $virtualOrder->setSubtotal(1293.60);
        $virtualItem1 = OrderMockBuilder::getItem(646.8, 646.8, 0);
        $virtualItem2 = OrderMockBuilder::getItem(646.8, 646.8, 0);
        OrderMockBuilder::addItem($virtualOrder, $virtualItem1);
        OrderMockBuilder::addItem($virtualOrder, $virtualItem2);

        $expected = [
            'sum' => 0.0,
            'origGrandTotal' => 0.6,
            'items' => [
                100501 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'gift_cards_amount' => 1293.6,
                    'children' => [
                        100502 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0.0,
                            'gift_cards_amount' => 646.8,
                        ],
                        100503 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0.0,
                            'gift_cards_amount' => 646.8,
                        ],
                    ],
                ],
                'shipping' => [
                    'price' => 0.6,
                    'quantity' => 1.0,
                    'sum' => 0.6,
                ],
            ],
        ];

        return [
            $order,
            [
                'expected' => $expected,
                'virtual_order' => [$virtualOrder],
            ],
        ];
    }

    private static function test2(): array
    {
        $order = OrderMockBuilder::getNewOrderInstance(1293.6, 0.60, 200.0000, 0, 0);
        $order->setData('gift_cards_amount', 1493);

        $father = OrderMockBuilder::getItem(1293.6000, 1293.6000, 0);
        $father->setProductType(Bundle::TYPE_CODE);
        $father->setData('isChildrenCalculated', true);
        $child1 = OrderMockBuilder::getItem(293.60, 293.60, 0);
        $child2 = OrderMockBuilder::getItem(1000, 1000, 0);
        $father->setChildrenItems([$child1, $child2]);
        OrderMockBuilder::addItem($order, $father);

        //Virtual order based on bundle product
        $virtualOrder = OrderMockBuilder::getNewOrderInstance(1293.6, 0, 0);
        $virtualOrder->setDiscountAmount(0);
        $virtualItem1 = OrderMockBuilder::getItem(293.6, 293.6, 0);
        $virtualItem2 = OrderMockBuilder::getItem(1000, 1000, 0);
        OrderMockBuilder::addItem($virtualOrder, $virtualItem1);
        OrderMockBuilder::addItem($virtualOrder, $virtualItem2);

        $expected = [
            'sum' => 0.0,
            'origGrandTotal' => 0.6,
            'items' => [
                100501 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'gift_cards_amount' => 1293.6,
                    'children' => [
                        100502 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0.0,
                            'gift_cards_amount' => 293.6,
                        ],
                        100503 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0.0,
                            'gift_cards_amount' => 1000,
                        ],
                    ],
                ],
                'shipping' => [
                    'price' => 0.6,
                    'quantity' => 1.0,
                    'sum' => 0.6,
                ],
            ],
        ];

        return [
            $order,
            [
                'expected' => $expected,
                'virtual_order' => [$virtualOrder],
            ],
        ];
    }

    private static function test3(): array
    {
        $order = OrderMockBuilder::getNewOrderInstance(1293.6, 0.50, 200.0000, 0, -0.1);
        $order->setData('gift_cards_amount', 1493);

        $father = OrderMockBuilder::getItem(1293.6000, 1293.6000, 0.1);
        $father->setRowTotal(1293.6000);
        $father->setProductType(Bundle::TYPE_CODE);
        $child1 = OrderMockBuilder::getItem(null, null, 0);
        $child2 = OrderMockBuilder::getItem(null, null, 0);
        $father->setChildrenItems([$child1, $child2]);
        OrderMockBuilder::addItem($order, $father);

        //Virtual order based on bundle product
        $virtualOrder = OrderMockBuilder::getNewOrderInstance(1293.6, 0, 0);
        $virtualOrder->setDiscountAmount(0);
        $virtualOrder->setSubtotal(1293.6);
        $virtualItem1 = OrderMockBuilder::getItem(646.8, 646.8, 0);
        $virtualItem2 = OrderMockBuilder::getItem(646.8, 646.8, 0);
        OrderMockBuilder::addItem($virtualOrder, $virtualItem1);
        OrderMockBuilder::addItem($virtualOrder, $virtualItem2);

        $expected = [
            'sum' => 0.0,
            'origGrandTotal' => 0.5,
            'items' => [
                100501 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'gift_cards_amount' => 1293.6,
                    'children' => [
                        100502 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0.0,
                            'gift_cards_amount' => 646.8,
                        ],
                        100503 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0.0,
                            'gift_cards_amount' => 646.8,
                        ],
                    ],
                ],
                'shipping' => [
                    'price' => 0.5,
                    'quantity' => 1.0,
                    'sum' => 0.5,
                ],
            ],
        ];

        return [
            $order,
            [
                'expected' => $expected,
                'virtual_order' => [$virtualOrder],
            ],
        ];
    }

    private static function test4(): array
    {
        $order = OrderMockBuilder::getNewOrderInstance(1293.6, 0.50, 200.0000, 0, -0.1);
        $order->setData('gift_cards_amount', 1493);

        $father = OrderMockBuilder::getItem(1293.6000, 1293.6000, 0.1);
        $father->setProductType(Bundle::TYPE_CODE);
        $father->setData('isChildrenCalculated', true);
        $child1 = OrderMockBuilder::getItem(293.60, 293.60, 0);
        $child2 = OrderMockBuilder::getItem(1000, 1000, 0.1);
        $father->setChildrenItems([$child1, $child2]);
        OrderMockBuilder::addItem($order, $father);

        //Virtual order based on bundle product
        $virtualOrder = OrderMockBuilder::getNewOrderInstance(1293.6, 0, 0);
        $virtualOrder->setDiscountAmount(0);
        $virtualItem1 = OrderMockBuilder::getItem(293.6, 293.6, 0);
        $virtualItem2 = OrderMockBuilder::getItem(1000, 1000, 0.1);
        OrderMockBuilder::addItem($virtualOrder, $virtualItem1);
        OrderMockBuilder::addItem($virtualOrder, $virtualItem2);

        $expected = [
            'sum' => 0.0,
            'origGrandTotal' => 0.5,
            'items' => [
                100501 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'gift_cards_amount' => 1293.6,
                    'children' => [
                        100502 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0.0,
                            'gift_cards_amount' => 293.6,
                        ],
                        100503 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0.0,
                            'gift_cards_amount' => 1000,
                        ],
                    ],
                ],
                'shipping' => [
                    'price' => 0.5,
                    'quantity' => 1.0,
                    'sum' => 0.5,
                ],
            ],
        ];

        return [
            $order,
            [
                'expected' => $expected,
                'virtual_order' => [$virtualOrder],
            ],
        ];
    }
}
