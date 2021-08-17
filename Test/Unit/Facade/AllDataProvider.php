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

class AllDataProvider
{
    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public static function dataProvider()
    {
        $final = [];
        $order = OrderMockBuilder::getNewOrderInstance(1293.6, 0.60, 200.0000, 0, 0);
        $order->setData('gift_cards_amount', 1493);

        $father = OrderMockBuilder::getItem(1293.6000, 1293.6000, 0);
        $father->setProductType(Bundle::TYPE_CODE);
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

        $final['1. Заказ с 1 бандлом DynamicPrice = Disabled. Оплата Gift Card полная вкл доставку'] = [$order, $expected];

        $order = OrderMockBuilder::getNewOrderInstance(1293.6, 0.60, 200.0000, 0, 0);
        $order->setData('gift_cards_amount', 1493);

        $father = OrderMockBuilder::getItem(1293.6000, 1293.6000, 0);
        $father->setProductType(Bundle::TYPE_CODE);
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
                    'price' => 0.5999, // TODO: WRONG
                    'quantity' => 1.0,
                    'sum' => 0.5999, //TODO: WRONG
                ],
            ],
        ];

        // $final['2. Заказ с 1 бандлом DynamicPrice = Enabled. Оплата Gift Card полная вкл доставку'] = [$order, $expected];

        return $final;
    }
}
