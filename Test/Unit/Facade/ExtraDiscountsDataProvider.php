<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade;

use Magento\Bundle\Model\Product\Type as Bundle;
use Mygento\Base\Test\OrderMockBuilder;

class ExtraDiscountsDataProvider
{
    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public static function dataProvider()
    {
        $final = [];

        //Оплата полностью GiftCard.
        //Ранее пересчитанный заказ снова попадает в процесс пересчета.
        //Bug Division by zero
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

        $final['1. Заказ с 1 пересчитанным бандлом и Gift Card полная оплата. Division by zero.'] = [$order, []];

        return $final;
    }
}
