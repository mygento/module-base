<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade;

use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\Framework\DataObject;
use Mygento\Base\Model\Mock\OrderMockBuilder;
use Mygento\Base\Test\Unit\Facade\Handlers\DataProvider\SkipItemsDataProvider;

class AllHandlersDataProvider
{
    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public static function dataProvider()
    {
        $final = [];
        $final['1. Заказ с 1 бандлом DynamicPrice = Disabled. Оплата Gift Card полная вкл доставку'] = self::test1();
        $final['2. Заказ с 1 бандлом DynamicPrice = Disabled. Оплата Gift Card частично покрывает доставку'] = self::test2();
        $final['3. Заказ с 1 бандлом DynamicPrice = Enabled. Оплата Gift Card полная вкл доставку'] = self::test3();
        $final['Заказ с 1 бандлом, 1 симплом со скидкой и 1 Skipped. Баг с потерей стоимости бандла.'] = self::test4();

//        return $final;

        //Add tests for Skipper Handler
        return array_merge($final, self::testsForSkippedItems());
    }

    private static function test1(): array
    {
        $order = OrderMockBuilder::getNewOrderInstance(1293.6, 0.60, 200.0000, 0, 0);
        $order->setData('gift_cards_amount', 1493);

        $father = OrderMockBuilder::getItem(1293.6000, 1293.6000, 0)
            ->setHasChildren(true)
            ->setProductType(Bundle::TYPE_CODE);
        $child1 = OrderMockBuilder::getItem(null, null, 0);
        $child2 = OrderMockBuilder::getItem(null, null, 0);
        $father->setChildrenItems([$child1, $child2]);
        OrderMockBuilder::addItem($order, $father);

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

        return [$order, $expected];
    }

    private static function test4(): array
    {
        $order = OrderMockBuilder::getNewOrderInstance(10500.32, 10261.12, 0.0000, 0, -239.2);

        //Skipped simple
        $item1Skipped = OrderMockBuilder::getItem(2763.5500, 2763.5500, 0);
        $item1Skipped
            ->setRowTotal(2302.9600)
            ->setTaxPercent(20.00)
            ->setTaxAmount(460.59)
            //key 'is_skipped' is used for testing. See Extra\TestItemSkipper
            ->setData('is_skipped', true);

        //Bundle
        $father = OrderMockBuilder::getItem(5344.7700, 1781.5900, 0, 3)
            ->setData('isChildrenCalculated', true)
            ->setHasChildren(true)
            ->setProductType(Bundle::TYPE_CODE)
            ->setRowTotal(4453.9800)
            ->setTaxAmount(890.7900);
        $child1 = OrderMockBuilder::getItem(2666.3700, 888.7900, 0, 3)
            ->setRowTotal(2221.9800)
            ->setTaxPercent(20.00)
            ->setTaxAmount(444.3900);
        $child2 = OrderMockBuilder::getItem(2678.4000, 892.80, 0, 3)
            ->setRowTotal(2232.0000)
            ->setTaxPercent(20.00)
            ->setTaxAmount(446.4000);
        $father->setChildrenItems([$child1, $child2]);

        //Simple
        $item2 = OrderMockBuilder::getItem(2392.0000, 2392.0000, 239.2000)
            ->setRowTotal(1993.3300)
            ->setTaxPercent(20.00)
            ->setTaxAmount(398.6700);

        OrderMockBuilder::addItem($order, $item1Skipped);
        OrderMockBuilder::addItem($order, $father);
        OrderMockBuilder::addItem($order, $item2);

        $expected = [
            'sum' => 10261.12,
            'origGrandTotal' => 7497.57,
            'items' => [
                100502 => [
                    'price' => 1781.59,
                    'quantity' => 3.0,
                    'sum' => 5344.77,
                    'children' => [
                        100503 => [
                            'price' => 888.79,
                            'quantity' => 3.0,
                            'sum' => 2666.37,
                            'tax' => '',
                        ],
                        100504 => [
                            'price' => 892.8,
                            'quantity' => 3.0,
                            'sum' => 2678.4,
                            'tax' => '',
                        ],
                    ],
                ],
                100505 => [
                    'price' => 2152.8,
                    'quantity' => 1.0,
                    'sum' => 2152.8,
                    'tax' => '',
                ],
                'shipping' => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                ],
                100501 => [
                    'price' => 2763.55,
                    'quantity' => 1.0,
                    'sum' => 2763.55,
                    'tax' => '',
                ],
            ],
        ];

        return [$order, $expected];
    }

    private static function test2(): array
    {
        $order = OrderMockBuilder::getNewOrderInstance(1293.6, 0.60, 200.0000, 0, 0);
        $order->setData('gift_cards_amount', 1493);

        $father = OrderMockBuilder::getItem(1293.6000, 1293.6000, 0)
            ->setHasChildren(true)
            ->setProductType(Bundle::TYPE_CODE);
        $child1 = OrderMockBuilder::getItem(null, null, 0);
        $child1->setProduct(new DataObject(['final_price' => 293.60]));
        $child2 = OrderMockBuilder::getItem(null, null, 0);
        $child2->setProduct(new DataObject(['final_price' => 1000.00]));
        $father->setChildrenItems([$child1, $child2]);
        OrderMockBuilder::addItem($order, $father);

        $expected = [
            'sum' => 0,
            'origGrandTotal' => 0.60,
            'items' => [
                100601 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'gift_cards_amount' => 1293.6,
                    'children' => [
                        100602 => [
                            'price' => 0,
                            'quantity' => 1.0,
                            'sum' => 0,
                            'gift_cards_amount' => 293.6,
                        ],
                        100603 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0,
                            'gift_cards_amount' => 1000.0,
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

        return [$order, $expected];
    }

    private static function test3(): array
    {
        $order = OrderMockBuilder::getNewOrderInstance(1293.6, 0.60, 200.0000, 0, 0);
        $order->setData('gift_cards_amount', 1493);

        $father = OrderMockBuilder::getItem(1293.6000, 1293.6000, 0)
            ->setProductType(Bundle::TYPE_CODE)
            ->setHasChildren(true)
            ->setData('isChildrenCalculated', true);
        $child1 = OrderMockBuilder::getItem(293.60, 293.60, 0);
        $child2 = OrderMockBuilder::getItem(1000, 1000, 0);
        $father->setChildrenItems([$child1, $child2]);
        OrderMockBuilder::addItem($order, $father);

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

        return [$order, $expected];
    }

    private static function testsForSkippedItems(): array
    {
        $tests = SkipItemsDataProvider::dataProvider();

        $key1 = SkipItemsDataProvider::TEST_1_NAME;
        $newKey = str_replace('1.', '4. Skipper:', $key1);
        $tests[$newKey] = $tests[$key1];
        $expected = [
            'sum' => 799.2,
            'origGrandTotal' => 864.2,
            'items' => [
                100511 => [
                    'price' => 739.2,
                    'quantity' => 1.0,
                    'sum' => 739.2,
                    'tax' => '',
                ],
                'shipping' => [
                    'price' => 125.00,
                    'quantity' => 1.0,
                    'sum' => 125.00,
                    'tax' => '',
                ],
                100510 => [
                    'price' => 60.0,
                    'quantity' => 1.0,
                    'sum' => 60.0,
                    'tax' => '',
                ],
            ],
        ];

        $tests[$newKey][1] = $expected;

        $key2 = SkipItemsDataProvider::TEST_2_NAME;
        $newKey = str_replace('2.', '5. Skipper:', $key2);
        $tests[$newKey] = $tests[$key2];
        $expected = [
            'sum' => 70.0,
            'origGrandTotal' => 100.0,
            'items' => [
                'shipping' => [
                    'price' => 100.00,
                    'quantity' => 1.0,
                    'sum' => 100.00,
                    'tax' => '',
                ],
                100503 => [
                    'price' => 70.0,
                    'quantity' => 1.0,
                    'sum' => 70.0,
                    'tax' => '',
                ],
            ],
        ];
        $tests[$newKey][1] = $expected;

        $key3 = SkipItemsDataProvider::TEST_3_NAME;
        $newKey = str_replace('3.', '6. Skipper:', $key3);
        $tests[$newKey] = $tests[$key3];
        $expected = [
            'sum' => 799.2,
            'origGrandTotal' => 125.0,
            'items' => [
                'shipping' => [
                    'price' => 125.00,
                    'quantity' => 1.0,
                    'sum' => 125.00,
                    'tax' => '',
                ],
                100504 => [
                    'price' => 60.0,
                    'quantity' => 1.0,
                    'sum' => 60.0,
                    'tax' => '',
                ],
                100505 => [
                    'price' => 739.2,
                    'quantity' => 1.0,
                    'sum' => 739.2,
                    'tax' => '',
                ],
            ],
        ];
        $tests[$newKey][1] = $expected;

        //Remove tests with old names
        unset($tests[$key1], $tests[$key2], $tests[$key3]);

        return $tests;
    }
}
