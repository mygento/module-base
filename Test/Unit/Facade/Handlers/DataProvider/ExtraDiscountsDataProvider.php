<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade\Handlers\DataProvider;

use Magento\Bundle\Model\Product\Type as Bundle;
use Mygento\Base\Api\Data\PaymentInterface;
use Mygento\Base\Model\Mock\OrderMockBuilder;

class ExtraDiscountsDataProvider
{
    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public static function dataProvider()
    {
        $final = [];

        //Оплата Баллами.
        $order = OrderMockBuilder::getNewOrderInstance(6899.40000, 3471.4000, 0, 2928, -500.00);

        $simple1 = OrderMockBuilder::getItem(1429.4000, 1429.4000, 0.0000, 1, 20);
        $simple2 = OrderMockBuilder::getItem(1562.000, 1562.0000, 500.0000, 1, 20);
        $simple3 = OrderMockBuilder::getItem(1162.50, 1162.50, 0.0000, 1, 20);
        $simple4 = OrderMockBuilder::getItem(0.0, 0.0, 0.0000, 1, 20);
        $simple5 = OrderMockBuilder::getItem(0.0, 0.0, 0.0000, 1, 20);
        $simple6 = OrderMockBuilder::getItem(0.0, 0.0, 0.0000, 1, 20);
        $simple7 = OrderMockBuilder::getItem(0.0, 0.0, 0.0000, 1, 20);
        $simple8 = OrderMockBuilder::getItem(0.0, 0.0, 0.0000, 1, 20);

        $father = OrderMockBuilder::getItem(2745.500, 2745.500, 0.0000, 1, 0)
            ->setName('Bundle')
            ->setHasChildren(true)
            ->setProductType(Bundle::TYPE_CODE)
            ->setData('isChildrenCalculated', true);
        $child1 = OrderMockBuilder::getItem(1515.55, 1515.55, 0, 1);
        $child1->setName('Child 1');
        $child2 = OrderMockBuilder::getItem(1229.95, 1229.95, 0, 1);
        $child2->setName('Child 2');

        $father->setChildrenItems([$child1, $child2]);
        OrderMockBuilder::addItem($order, $father);
        OrderMockBuilder::addItem($order, $simple1);
        OrderMockBuilder::addItem($order, $simple2);
        OrderMockBuilder::addItem($order, $simple3);
        OrderMockBuilder::addItem($order, $simple4);
        OrderMockBuilder::addItem($order, $simple5);
        OrderMockBuilder::addItem($order, $simple6);
        OrderMockBuilder::addItem($order, $simple7);
        OrderMockBuilder::addItem($order, $simple8);

        $expected = [
            'sum' => 3471.37,
            'origGrandTotal' => 3471.4,
            'items' => [
                100509 => [
                    'price' => 1489.31,
                    'name' => 'Bundle',
                    'quantity' => 1.0,
                    'sum' => 1489.31,
                    'tax' => '',
                    'reward_currency_amount' => 1165.15,
                ],
                100501 => [
                    'price' => 775.38,
                    'quantity' => 1.0,
                    'sum' => 775.38,
                    'tax' => '',
                    'reward_currency_amount' => 606.62,
                ],
                100502 => [
                    'price' => 576.08,
                    'quantity' => 1.0,
                    'sum' => 576.08,
                    'tax' => '',
                    'reward_currency_amount' => 662.89,
                ],
                100503 => [
                    'price' => 630.6,
                    'quantity' => 1.0,
                    'sum' => 630.6,
                    'tax' => '',
                    'reward_currency_amount' => 493.35,
                ],
                100504 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'reward_currency_amount' => 0.0,
                ],
                100505 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'reward_currency_amount' => 0.0,
                ],
                100506 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'reward_currency_amount' => 0.0,
                ],
                100507 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'reward_currency_amount' => 0.0,
                ],
                100508 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'reward_currency_amount' => 0.0,
                ],
                'shipping' => [
                    'price' => 0.03,
                    'quantity' => 1.0,
                    'sum' => 0.03,
                    'tax' => '',
                ],
            ],
        ];

        $final['1. Расчет распределения баллов'] = [$order, $expected];

        return $final;
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function dataProviderDivisionByZero()
    {
        $final = [];

        //Оплата полностью GiftCard.
        //Ранее пересчитанный заказ снова попадает в процесс пересчета.
        //Error Division by zero
        //В заказе нет флага о том, что он был пересчитан. Поэтому кейс негативный, проверяем наличие ошибки.
        $order = OrderMockBuilder::getNewOrderInstance(0.0000, 0.0000, 0, 0);
        $order->setData('gift_cards_amount', 1500);
        $father = OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0)
            ->setHasChildren(true)
            ->setProductType(Bundle::TYPE_CODE);
        $child1 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child1->setData('gift_cards_amount', 500.00);
        $child2 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child2->setData('gift_cards_amount', 500.00);
        $child3 = OrderMockBuilder::getItem(null, null, 0, 1);
        $child3->setData('gift_cards_amount', 500.00);
        $father->setChildrenItems([$child1, $child2, $child3]);
        OrderMockBuilder::addItem($order, $father);

        $expected = phpversion() <= '8.0'
            ? new \PHPUnit\Framework\Exception('Warning: Division by zero', 2)
            : new \DivisionByZeroError('Warning: Division by zero', 2);

        $final['1. Заказ с 1 пересчитанным бандлом и Gift Card полная оплата. Division by zero.'] = [$order, $expected];

        //Пересчитанный заказ. При повторном пересчете порождает ошибку
        //Division by zero
        //Сетим в заказ флаг о том, что он был пересчитан.
        //Кейс позитивный, ошибка не должна появится.
        $order = OrderMockBuilder::getNewOrderInstance(0.0000, 0.0000, 0, 0);
        $order->setData('gift_cards_amount', 3000);
        $order->setData('reward_points', 724);
        $order->getPayment()->setAdditionalInformation(PaymentInterface::RECALCULATED_FLAG, true);
        $item1 = OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0);
        $item1->setData('gift_cards_amount', 3000.00);
        $item1->setData('reward_points', 724);
        OrderMockBuilder::addItem($order, $item1);
        $item2 = OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0);
        OrderMockBuilder::addItem($order, $item2);
        $item3 = OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0);
        OrderMockBuilder::addItem($order, $item3);
        $item4 = OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0);
        OrderMockBuilder::addItem($order, $item4);
        $item5 = OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0);
        OrderMockBuilder::addItem($order, $item5);
        $item6 = OrderMockBuilder::getItem(0.0000, 0.0000, 0.0000, 1, 20, 0);
        OrderMockBuilder::addItem($order, $item6);

        $expected = [
            'sum' => 0.00,
            'origGrandTotal' => 0.0,
            'items' => [
                100516 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'gift_cards_amount' => 3000.0,
                ],
                100517 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'gift_cards_amount' => null,
                ],
                100518 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'gift_cards_amount' => null,
                ],
                100519 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'gift_cards_amount' => null,
                ],
                100520 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'gift_cards_amount' => null,
                ],
                100521 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                    'gift_cards_amount' => null,
                ],
                'shipping' => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                ],
            ],
        ];
        $final['2. Заказ с пересчитанными Simple товарами, баллами и Gift Card. Division by zero.'] = [
            $order,
            $expected,
        ];

        return $final;
    }
}
