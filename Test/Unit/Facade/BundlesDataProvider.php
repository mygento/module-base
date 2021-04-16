<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade;

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

        //Заказ бесплатного пробника. Клиент оплачивает только доставку
        $order = OrderMockBuilder::getNewOrderInstance(0.0000, 0.0000, 0, 0);
        $order->setData('gift_cards_amount', 1500);
        $father = OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0);
        $father->setProductType(Bundle::TYPE_CODE);
        $child1 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child1->setData('gift_cards_amount', 500.00);
        $child2 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child2->setData('gift_cards_amount', 500.00);
        $child3 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child3->setData('gift_cards_amount', 500.00);
        $father->setChildrenItems([$child1, $child2, $child3]);
        OrderMockBuilder::addItem($order, $father);

        $final['4. Заказ с 1 бандлом пересчитанным бандлом и Gift Card полная оплата.'] = [$order, []];

        return $final;
    }
}
