<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade\Handlers\DataProvider;

use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\Framework\DataObject;
use Mygento\Base\Test\OrderMockBuilder;

class BundlesDataProvider
{
    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public static function dataProviderBundles()
    {
        $final = [];

        //Обычный Bundle DynamicPrice=Disabled с 3 детками
        $order = OrderMockBuilder::getNewOrderInstance(1200.0000, 1230.0000, 30.0000);
        $father = OrderMockBuilder::getItem(1200.0000, 600.0000, 0, 2);
        $father->setProductType(Bundle::TYPE_CODE);
        $child1 = OrderMockBuilder::getItem(null, null, 0, 2);
        $child2 = OrderMockBuilder::getItem(null, null, 0, 2);
        $child3 = OrderMockBuilder::getItem(null, null, 0, 2);
        $father->setChildrenItems([$child1, $child2, $child3]);
        OrderMockBuilder::addItem($order, $father);

        $expected = [
            'sum' => 1200.0,
            'origGrandTotal' => 1230.0,
            'items' => [
                100501 => [
                    'price' => 600.0,
                    'quantity' => 2.0,
                    'sum' => 1200.0,
                    'children' => [
                        100502 => [
                            'price' => 200.0,
                            'quantity' => 2.0,
                            'sum' => 400.0,
                        ],
                        100503 => [
                            'price' => 200.0,
                            'quantity' => 2.0,
                            'sum' => 400.0,
                        ],
                        100504 => [
                            'price' => 200.0,
                            'quantity' => 2.0,
                            'sum' => 400.0,
                        ],
                    ],
                ],
                'shipping' => [
                    'price' => 30.0,
                    'quantity' => 1.0,
                    'sum' => 30.0,
                ],
            ],
        ];

        $final['1. Заказ с 1 бандлом DynamicPrice = Disabled'] = [$order, $expected];

        $order = OrderMockBuilder::getNewOrderInstance(866.0000, 881.0000, 15.0000);
        $father = OrderMockBuilder::getItem(866.0000, 866.0000, 0);
        $father->setProductType(Bundle::TYPE_CODE);
        $child1 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child1->setProduct(new DataObject(['final_price' => 180.00]));
        $child2 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child2->setProduct(new DataObject(['final_price' => 220.00]));
        $child3 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child3->setProduct(new DataObject(['final_price' => 1332.00]));
        $father->setChildrenItems([$child1, $child2, $child3]);
        OrderMockBuilder::addItem($order, $father);

        $expected = [
            'sum' => 866.0,
            'origGrandTotal' => 881.0,
            'items' => [
                100513 => [
                    'price' => 866.0,
                    'quantity' => 1.0,
                    'sum' => 866.0,
                    'children' => [
                        100514 => [
                            'price' => 90.0,
                            'quantity' => 1.0,
                            'sum' => 90.0,
                            'tax' => '',
                        ],
                        100515 => [
                            'price' => 110.0,
                            'quantity' => 1.0,
                            'sum' => 110.0,
                        ],
                        100516 => [
                            'price' => 666.0,
                            'quantity' => 1.0,
                            'sum' => 666.0,
                        ],
                    ],
                ],
                'shipping' => [
                    'price' => 15.0,
                    'quantity' => 1.0,
                    'sum' => 15.0,
                ],
            ],
        ];

        $final['2. Заказ с 1 бандлом DynamicPrice = Disabled. Цены дочерних берутся из продуктов'] = [$order, $expected];

        $order = OrderMockBuilder::getNewOrderInstance(866.0000, 880.0000, 15.0000);
        $father = OrderMockBuilder::getItem(866.0000, 866.0000, 0);
        $father->setProductType(Bundle::TYPE_CODE);
        //Dynamic Price = Enabled
        $father->setData('isChildrenCalculated', true);
        $child1 = OrderMockBuilder::getItem(100.00, 100.00, 0.12);
        $child2 = OrderMockBuilder::getItem(100.00, 100.00, 0.11);
        $child3 = OrderMockBuilder::getItem(666.00, 666.00, 0.77);
        $father->setChildrenItems([$child1, $child2, $child3]);
        OrderMockBuilder::addItem($order, $father);

        $expected = [
            'sum' => 864.99,
            'origGrandTotal' => 880.0,
            'items' => [
                100505 => [
                    'price' => 864.99,
                    'quantity' => 1.0,
                    'sum' => 864.99,
                    'children' => [
                        100506 => [
                            'price' => 99.88,
                            'quantity' => 1.0,
                            'sum' => 99.88,
                        ],
                        100507 => [
                            'price' => 99.88,
                            'quantity' => 1.0,
                            'sum' => 99.88,
                        ],
                        100508 => [
                            'price' => 665.23,
                            'quantity' => 1.0,
                            'sum' => 665.23,
                        ],
                    ],
                ],
                'shipping' => [
                    'price' => 15.01,
                    'quantity' => 1.0,
                    'sum' => 15.01,
                ],
            ],
        ];
        $final['3. Заказ с 1 бандлом DynamicPrice = Enabled и скидкой'] = [$order, $expected];

        //Старый пересчитанный заказ с 1 бандлом и Gift Card полная оплата.
        //Заказ созданный до фикса бага в пересчете
        $order = OrderMockBuilder::getNewOrderInstance(0.0000, 0.0000, 0, 0);
        $order->setData('gift_cards_amount', 1500);
        $father = OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0);
        $father->setProductType(Bundle::TYPE_CODE);
        $father->setData('gift_cards_amount', 1500);
        $child1 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child1->setData('gift_cards_amount', 500.00);
        $child2 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child2->setData('gift_cards_amount', 500.00);
        $child3 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child3->setData('gift_cards_amount', 500.00);
        $father->setChildrenItems([$child1, $child2, $child3]);
        OrderMockBuilder::addItem($order, $father);

        $expected = [
            'sum' => 0.0,
            'origGrandTotal' => 0.0,
            'items' => [
                100513 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'children' => [
                        100514 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0.0,
                            'tax' => '',
                        ],
                        100515 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0.0,
                            'tax' => '',
                        ],
                        100516 => [
                            'price' => 0.0,
                            'quantity' => 1.0,
                            'sum' => 0.0,
                            'tax' => '',
                        ],
                    ],
                ],
                'shipping' => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                ],
            ],
        ];

        $final['4. Пересчитанный заказ с 1 бандлом и Gift Card полная оплата.'] = [$order, $expected];

        //Повторный пересчет
        $order = OrderMockBuilder::getNewOrderInstance(749.700, 862.21, 150.0000);
        $father = OrderMockBuilder::getItem(749.7000, 749.7000, 0, 1);
        $father->setProductType(Bundle::TYPE_CODE);
        //Dynamic Price = Enabled
        $father->setData('isChildrenCalculated', true);
        $child1 = OrderMockBuilder::getItem(338.10, 338.10, 16.91);
        $child2 = OrderMockBuilder::getItem(411.60, 411.60, 20.58);
        $father->setChildrenItems([$child1, $child2]);
        OrderMockBuilder::addItem($order, $father);

        $expected = [
            'sum' => 712.2,
            'origGrandTotal' => 862.21,
            'items' => [
                100501 => [
                    'price' => 712.2,
                    'quantity' => 1.0,
                    'sum' => 712.2,
                    'tax' => '',
                    'children' => [
                        100502 => [
                            'price' => 321.19,
                            'quantity' => 1.0,
                            'sum' => 321.19,
                            'tax' => '',
                        ],
                        100503 => [
                            'price' => 391.01,
                            'quantity' => 1.0,
                            'sum' => 391.01,
                            'tax' => '',
                        ],
                    ],
                ],
                'shipping' => [
                    'price' => 150.01,
                    'quantity' => 1.0,
                    'sum' => 150.01,
                    'tax' => '',
                ],
            ],
        ];

        $final['5. Попытка пересчета заказа с бандлом, который был ранее пересчитан'] = [$order, $expected];

        return $final;
    }
}
