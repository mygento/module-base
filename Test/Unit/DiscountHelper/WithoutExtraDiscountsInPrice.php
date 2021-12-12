<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\DiscountHelper;

use Mygento\Base\Model\Mock\OrderMockBuilder;

class WithoutExtraDiscountsInPrice extends GeneralTestCase
{
    protected function setUp(): void
    {
        $this->discountHelper = $this->getDiscountHelperInstance();
        $this->discountHelper->setIsAddGiftCardToPrice(false);
        $this->discountHelper->setIsAddRewardsToPrice(false);
    }

    /**
     * Attention! Order of items in array is important!
     * @dataProvider dataProviderOrdersForCheckCalculation
     * @param mixed $order
     * @param mixed $expectedArray
     */
    public function testCalculation($order, $expectedArray)
    {
        parent::testCalculation($order, $expectedArray);

        self::assertTrue(method_exists($this->discountHelper, 'getRecalculated'));

        $recalculatedData = $this->discountHelper->getRecalculated($order, 'vat20');

        self::assertEquals($recalculatedData['sum'], $expectedArray['sum'], 'Total sum failed');
        self::assertEquals($recalculatedData['origGrandTotal'], $expectedArray['origGrandTotal']);

        self::assertArrayHasKey('items', $recalculatedData);

        $recalcItems = array_values($recalculatedData['items']);
        $recalcExpectedItems = array_values($expectedArray['items']);

        foreach ($recalcItems as $index => $recalcItem) {
            self::assertEquals($recalcExpectedItems[$index]['price'], $recalcItem['price'], 'Price of item failed');
            self::assertEquals($recalcExpectedItems[$index]['quantity'], $recalcItem['quantity']);

            $sumEqual = bccomp($recalcExpectedItems[$index]['sum'], $recalcItem['sum']);
            self::assertEquals($sumEqual, 0, 'Sum of item failed');
        }
    }

    public function dataProviderOrdersForCheckCalculation()
    {
        $final = [];

        //Gift Card - полная оплата
        $order = OrderMockBuilder::getNewOrderInstance(1500.0000, 0.0000, 0.0000);
        $order->setData('discount_amount', 0);
        $order->setData('gift_cards_amount', 1500);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(1000.0000, 1000.0000, 0, 1));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(500.0000, 250.0000, 0, 2));

        $expected = [
            'sum' => 1500.00,
            'origGrandTotal' => 0.0,
            'items' => [
                100501 => [
                    'price' => 1000.0,
                    'quantity' => 1.0,
                    'sum' => 1000.0,
                    'tax' => 'vat20',
                ],
                100502 => [
                    'price' => 250.0,
                    'quantity' => 2.0,
                    'sum' => 500.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                ],
            ],
        ];
        $final['1. GiftCard присутствует, но не распределяется в цены'] = [$order, $expected];

        //Reward Points included
        $order = OrderMockBuilder::getNewOrderInstance(13010.0000, 11611.0000, 0, 100);
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(12990.0000, 12990.0000, 1299.0000, 1));
        OrderMockBuilder::addItem($order, OrderMockBuilder::getItem(20.0000, 20.0000, 0.0000, 1));

        $expected = [
            'sum' => 11711.00,
            'origGrandTotal' => 11611.0,
            'items' => [
                100503 => [
                    'price' => 11691.0,
                    'quantity' => 1.0,
                    'sum' => 11691.0,
                    'tax' => 'vat20',
                ],
                100504 => [
                    'price' => 20.0,
                    'quantity' => 1.0,
                    'sum' => 20.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                ],
            ],
        ];
        $final['2. Баллы присутствуют, но не распределяются в цены'] = [$order, $expected];

        return $final;
    }
}
