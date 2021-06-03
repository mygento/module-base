<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\DiscountHelper;

class SpreadAndSplitTest extends GeneralTestCase
{
    protected function setUp(): void
    {
        $this->discountHelper = $this->getDiscountHelperInstance();
        $this->discountHelper->setSpreadDiscOnAllUnits(true);
        $this->discountHelper->setIsSplitItemsAllowed(true);
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

        $this->assertTrue(method_exists($this->discountHelper, 'getRecalculated'));

        $recalculatedData = $this->discountHelper->getRecalculated($order, 'vat20');

        $this->assertEquals($recalculatedData['sum'], $expectedArray['sum'], 'Total sum failed');
        $this->assertEquals($recalculatedData['origGrandTotal'], $expectedArray['origGrandTotal']);

        $this->assertArrayHasKey('items', $recalculatedData);

        $recalcItems = array_values($recalculatedData['items']);
        $recalcExpectedItems = array_values($expectedArray['items']);

        foreach ($recalcItems as $index => $recalcItem) {
            $this->assertEquals($recalcExpectedItems[$index]['price'], $recalcItem['price'], 'Price of item failed');
            $this->assertEquals($recalcExpectedItems[$index]['quantity'], $recalcItem['quantity']);
            $this->assertEquals($recalcExpectedItems[$index]['sum'], $recalcItem['sum'], 'Sum of item failed');
        }
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected static function getExpected()
    {
        $actualData[parent::TEST_CASE_NAME_1] = [
            'sum' => 12069.30,
            'origGrandTotal' => 12069.30,
            'items' => [
                152 => [
                    'price' => 11717.5,
                    'quantity' => 1,
                    'sum' => 11717.5,
                ],
                153 => [
                    'price' => 351.8,
                    'quantity' => 1,
                    'sum' => 351.8,
                ],
                154 => [
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                ],
                'shipping' => [
                    'price' => 0.00,
                    'quantity' => 1,
                    'sum' => 0,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_2] = [
            'sum' => 5086.19,
            'origGrandTotal' => 9373.19,
            'items' => [
                152 => [
                    'price' => 5015.28,
                    'quantity' => 1,
                    'sum' => 5015.28,
                ],
                '153_1' => [
                    'price' => 23.63,
                    'quantity' => 1,
                    'sum' => 23.63,
                ],
                '153_2' => [
                    'price' => 23.64,
                    'quantity' => 2,
                    'sum' => 47.28,
                ],
                'shipping' => [
                    'price' => 4287.00,
                    'quantity' => 1,
                    'sum' => 4287.00,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_3] = [
            'sum' => 5086.19,
            'origGrandTotal' => 5106.19,
            'items' => [
                152 => [
                    'price' => 5015.28,
                    'quantity' => 1,
                    'sum' => 5015.28,
                ],
                '153_1' => [
                    'price' => 23.63,
                    'quantity' => 1,
                    'sum' => 23.63,
                ],
                '153_2' => [
                    'price' => 23.64,
                    'quantity' => 2,
                    'sum' => 47.28,
                ],
                'shipping' => [
                    'price' => 20.00,
                    'quantity' => 1,
                    'sum' => 20.00,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_4] = [
            'sum' => 5000.86,
            'origGrandTotal' => 5200.86,
            'items' => [
                152 => [
                    'price' => 500.41,
                    'quantity' => 2,
                    'sum' => 1000.82,
                ],
                153 => [
                    'price' => 1000.01,
                    'quantity' => 4,
                    'sum' => 4000.04,
                ],
                'shipping' => [
                    'price' => 200,
                    'quantity' => 1,
                    'sum' => 200,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_5] = [
            'sum' => 202.1,
            'origGrandTotal' => 202.1,
            'items' => [
                '152_1' => [
                    'price' => 36.41,
                    'quantity' => 2,
                    'sum' => 72.82,
                ],
                '152_2' => [
                    'price' => 36.42,
                    'quantity' => 1,
                    'sum' => 36.42,
                ],
                '153_1' => [
                    'price' => 23.21,
                    'quantity' => 2,
                    'sum' => 46.42,
                ],
                '153_2' => [
                    'price' => 23.22,
                    'quantity' => 2,
                    'sum' => 46.44,
                ],
                'shipping' => [
                    'price' => 0.0,
                    'quantity' => 1,
                    'sum' => 0.00,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_6] = [
            'sum' => 702.1,
            'origGrandTotal' => 702.1,
            'items' => [
                '152_1' => [
                    'price' => 38.89,
                    'quantity' => 1,
                    'sum' => 38.89,
                ],
                '152_2' => [
                    'price' => 38.9,
                    'quantity' => 2,
                    'sum' => 77.8,
                ],
                '153_1' => [
                    'price' => 24.79,
                    'quantity' => 1,
                    'sum' => 24.79,
                ],
                '153_2' => [
                    'price' => 24.8,
                    'quantity' => 3,
                    'sum' => 74.40,
                ],
                '154_1' => [
                    'price' => 97.24,
                    'quantity' => 3,
                    'sum' => 291.72,
                ],
                '154_2' => [
                    'price' => 97.25,
                    'quantity' => 2,
                    'sum' => 194.5,
                ],
                'shipping' => [
                    'price' => 0.00,
                    'quantity' => 1,
                    'sum' => 0.00,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_7] = [
            'sum' => 11691.0,
            'origGrandTotal' => 11691.0,
            'items' => [
                152 => [
                    'price' => 11673.03,
                    'quantity' => 1,
                    'sum' => 11673.03,
                ],
                153 => [
                    'price' => 17.97,
                    'quantity' => 1,
                    'sum' => 17.97,
                ],
                'shipping' => [
                    'price' => 0.00,
                    'quantity' => 1,
                    'sum' => 0.00,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_8] = [
            'sum' => 11611.0,
            'origGrandTotal' => 11611.0,
            'items' => [
                152 => [
                    'price' => 11593.15,
                    'quantity' => 1,
                    'sum' => 11593.15,
                ],
                153 => [
                    'price' => 17.85,
                    'quantity' => 1,
                    'sum' => 17.85,
                ],
                'shipping' => [
                    'price' => 0.00,
                    'quantity' => 1,
                    'sum' => 0.00,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_9] = [
            'sum' => 12890,
            'origGrandTotal' => 12890,
            'items' => [
                0 => [
                    'price' => 12890,
                    'name' => 'qGsDb5jK',
                    'quantity' => 1,
                    'sum' => 12890,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_10] = [
            'sum' => 12909.99,
            'origGrandTotal' => 12909.99,
            'items' => [
                152 => [
                    'price' => 12890.14,
                    'quantity' => 1,
                    'sum' => 12890.14,
                ],
                153 => [
                    'price' => 19.85,
                    'quantity' => 1,
                    'sum' => 19.85,
                ],
                'shipping' => [
                    'price' => 0.00,
                    'quantity' => 1,
                    'sum' => 0.00,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_11] = [
            'sum' => 32130.01,
            'origGrandTotal' => 32130.01,
            'items' => [
                0 => [
                    'price' => 17298.11,
                    'name' => 'tVPQqM6P',
                    'quantity' => 1,
                    'sum' => 17298.11,
                    'tax' => 'vat20',
                ],
                '100523_1' => [
                    'price' => 25.09,
                    'name' => 'dawEIFWS',
                    'quantity' => 260,
                    'sum' => 6523.4,
                    'tax' => 'vat20',
                ],
                '100523_2' => [
                    'price' => 25.1,
                    'name' => 'dawEIFWS',
                    'quantity' => 240,
                    'sum' => 6024,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 865.35,
                    'name' => 'GLr5lhBO',
                    'quantity' => 1,
                    'sum' => 865.35,
                    'tax' => 'vat20',
                ],
                '100525_1' => [
                    'price' => 354.78,
                    'name' => 'DgCaf1zm',
                    'quantity' => 1,
                    'sum' => 354.78,
                    'tax' => 'vat20',
                ],
                '100525_2' => [
                    'price' => 354.79,
                    'name' => 'DgCaf1zm',
                    'quantity' => 3,
                    'sum' => 1064.37,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_12] = [
            'sum' => 13189.99,
            'origGrandTotal' => 13189.99,
            'items' => [
                0 => [
                    'price' => 5793.74,
                    'name' => 'KJmABDf6',
                    'quantity' => 1,
                    'sum' => 5793.74,
                    'tax' => 'vat20',
                ],
                '100527_1' => [
                    'price' => 26.1,
                    'name' => '6nFZC8V8',
                    'quantity' => 22,
                    'sum' => 574.2,
                    'tax' => 'vat20',
                ],
                '100527_2' => [
                    'price' => 26.11,
                    'name' => '6nFZC8V8',
                    'quantity' => 18,
                    'sum' => 469.98,
                    'tax' => 'vat20',
                ],
                '100528_1' => [
                    'price' => 26.1,
                    'name' => '0YMYJeK7',
                    'quantity' => 17,
                    'sum' => 443.7,
                    'tax' => 'vat20',
                ],
                '100528_2' => [
                    'price' => 26.11,
                    'name' => '0YMYJeK7',
                    'quantity' => 13,
                    'sum' => 339.43,
                    'tax' => 'vat20',
                ],
                '100529_1' => [
                    'price' => 21.02,
                    'name' => '1jPzAF9j',
                    'quantity' => 6,
                    'sum' => 126.12,
                    'tax' => 'vat20',
                ],
                '100529_2' => [
                    'price' => 21.03,
                    'name' => '1jPzAF9j',
                    'quantity' => 34,
                    'sum' => 715.02,
                    'tax' => 'vat20',
                ],
                '100530_1' => [
                    'price' => 21.02,
                    'name' => 'gD8Tjaok',
                    'quantity' => 7,
                    'sum' => 147.14,
                    'tax' => 'vat20',
                ],
                '100530_2' => [
                    'price' => 21.03,
                    'name' => 'gD8Tjaok',
                    'quantity' => 43,
                    'sum' => 904.29,
                    'tax' => 'vat20',
                ],
                '100531_1' => [
                    'price' => 26.1,
                    'name' => 'rLfMKyoC',
                    'quantity' => 17,
                    'sum' => 443.7,
                    'tax' => 'vat20',
                ],
                '100531_2' => [
                    'price' => 26.11,
                    'name' => 'rLfMKyoC',
                    'quantity' => 13,
                    'sum' => 339.43,
                    'tax' => 'vat20',
                ],
                '100532_1' => [
                    'price' => 26.1,
                    'name' => 'L4IqEshr',
                    'quantity' => 6,
                    'sum' => 156.6,
                    'tax' => 'vat20',
                ],
                '100532_2' => [
                    'price' => 26.11,
                    'name' => 'L4IqEshr',
                    'quantity' => 4,
                    'sum' => 104.44,
                    'tax' => 'vat20',
                ],
                '100533_1' => [
                    'price' => 26.1,
                    'name' => 'l4p1n791',
                    'quantity' => 28,
                    'sum' => 730.8,
                    'tax' => 'vat20',
                ],
                '100533_2' => [
                    'price' => 26.11,
                    'name' => 'l4p1n791',
                    'quantity' => 22,
                    'sum' => 574.42,
                    'tax' => 'vat20',
                ],
                '100534_1' => [
                    'price' => 23.92,
                    'name' => 'hfzoDuFf',
                    'quantity' => 1,
                    'sum' => 23.92,
                    'tax' => 'vat20',
                ],
                '100534_2' => [
                    'price' => 23.93,
                    'name' => 'hfzoDuFf',
                    'quantity' => 9,
                    'sum' => 215.37,
                    'tax' => 'vat20',
                ],
                '100535_1' => [
                    'price' => 26.1,
                    'name' => '8PWR3Pop',
                    'quantity' => 11,
                    'sum' => 287.1,
                    'tax' => 'vat20',
                ],
                '100535_2' => [
                    'price' => 26.11,
                    'name' => '8PWR3Pop',
                    'quantity' => 9,
                    'sum' => 234.99,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 28.28,
                    'name' => 'kOto9Vfv',
                    'quantity' => 20,
                    'sum' => 565.6,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_13] = [
            'sum' => 5199.99,
            'origGrandTotal' => 5199.99,
            'items' => [
                '100537_1' => [
                    'price' => 18.35,
                    'name' => 'LAgOo1AI',
                    'quantity' => 28,
                    'sum' => 513.8,
                    'tax' => 'vat20',
                ],
                '100537_2' => [
                    'price' => 18.36,
                    'name' => 'LAgOo1AI',
                    'quantity' => 12,
                    'sum' => 220.32,
                    'tax' => 'vat20',
                ],
                '100538_1' => [
                    'price' => 18.35,
                    'name' => 'NqIQuqd7',
                    'quantity' => 21,
                    'sum' => 385.35,
                    'tax' => 'vat20',
                ],
                '100538_2' => [
                    'price' => 18.36,
                    'name' => 'NqIQuqd7',
                    'quantity' => 9,
                    'sum' => 165.24,
                    'tax' => 'vat20',
                ],
                '100539_1' => [
                    'price' => 14.78,
                    'name' => 'xzZITJyE',
                    'quantity' => 23,
                    'sum' => 339.94,
                    'tax' => 'vat20',
                ],
                '100539_2' => [
                    'price' => 14.79,
                    'name' => 'xzZITJyE',
                    'quantity' => 17,
                    'sum' => 251.43,
                    'tax' => 'vat20',
                ],
                '100540_1' => [
                    'price' => 14.78,
                    'name' => 'wKiM556l',
                    'quantity' => 29,
                    'sum' => 428.62,
                    'tax' => 'vat20',
                ],
                '100540_2' => [
                    'price' => 14.79,
                    'name' => 'wKiM556l',
                    'quantity' => 21,
                    'sum' => 310.59,
                    'tax' => 'vat20',
                ],
                '100541_1' => [
                    'price' => 18.35,
                    'name' => 'n1MkSUN8',
                    'quantity' => 21,
                    'sum' => 385.35,
                    'tax' => 'vat20',
                ],
                '100541_2' => [
                    'price' => 18.36,
                    'name' => 'n1MkSUN8',
                    'quantity' => 9,
                    'sum' => 165.24,
                    'tax' => 'vat20',
                ],
                '100542_1' => [
                    'price' => 18.35,
                    'name' => 'yG0B7EKY',
                    'quantity' => 7,
                    'sum' => 128.45,
                    'tax' => 'vat20',
                ],
                '100542_2' => [
                    'price' => 18.36,
                    'name' => 'yG0B7EKY',
                    'quantity' => 3,
                    'sum' => 55.08,
                    'tax' => 'vat20',
                ],
                '100543_1' => [
                    'price' => 18.35,
                    'name' => 'P2aMixwX',
                    'quantity' => 36,
                    'sum' => 660.6,
                    'tax' => 'vat20',
                ],
                '100543_2' => [
                    'price' => 18.36,
                    'name' => 'P2aMixwX',
                    'quantity' => 14,
                    'sum' => 257.04,
                    'tax' => 'vat20',
                ],
                '100544_1' => [
                    'price' => 16.82,
                    'name' => 'RdIoXoWg',
                    'quantity' => 7,
                    'sum' => 117.74,
                    'tax' => 'vat20',
                ],
                '100544_2' => [
                    'price' => 16.83,
                    'name' => 'RdIoXoWg',
                    'quantity' => 3,
                    'sum' => 50.49,
                    'tax' => 'vat20',
                ],
                '100545_1' => [
                    'price' => 18.35,
                    'name' => 'i0mX1Des',
                    'quantity' => 14,
                    'sum' => 256.9,
                    'tax' => 'vat20',
                ],
                '100545_2' => [
                    'price' => 18.36,
                    'name' => 'i0mX1Des',
                    'quantity' => 6,
                    'sum' => 110.16,
                    'tax' => 'vat20',
                ],
                '100546_1' => [
                    'price' => 19.88,
                    'name' => 'u40iqwaA',
                    'quantity' => 15,
                    'sum' => 298.2,
                    'tax' => 'vat20',
                ],
                '100546_2' => [
                    'price' => 19.89,
                    'name' => 'u40iqwaA',
                    'quantity' => 5,
                    'sum' => 99.45,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_14] = [
            'sum' => 13190.01,
            'origGrandTotal' => 13190.01,
            'items' => [
                0 => [
                    'price' => 5793.75,
                    'name' => '84MEoc1Y',
                    'quantity' => 1,
                    'sum' => 5793.75,
                    'tax' => 'vat20',
                ],
                '100548_1' => [
                    'price' => 26.1,
                    'name' => 'VDAYw0zS',
                    'quantity' => 21,
                    'sum' => 548.1,
                    'tax' => 'vat20',
                ],
                '100548_2' => [
                    'price' => 26.11,
                    'name' => 'VDAYw0zS',
                    'quantity' => 19,
                    'sum' => 496.09,
                    'tax' => 'vat20',
                ],
                '100549_1' => [
                    'price' => 26.1,
                    'name' => '4UUBllTv',
                    'quantity' => 17,
                    'sum' => 443.7,
                    'tax' => 'vat20',
                ],
                '100549_2' => [
                    'price' => 26.11,
                    'name' => '4UUBllTv',
                    'quantity' => 13,
                    'sum' => 339.43,
                    'tax' => 'vat20',
                ],
                '100550_1' => [
                    'price' => 21.02,
                    'name' => 'lVP8ceEm',
                    'quantity' => 6,
                    'sum' => 126.12,
                    'tax' => 'vat20',
                ],
                '100550_2' => [
                    'price' => 21.03,
                    'name' => 'lVP8ceEm',
                    'quantity' => 34,
                    'sum' => 715.02,
                    'tax' => 'vat20',
                ],
                '100551_1' => [
                    'price' => 21.02,
                    'name' => 'Lx52oORB',
                    'quantity' => 7,
                    'sum' => 147.14,
                    'tax' => 'vat20',
                ],
                '100551_2' => [
                    'price' => 21.03,
                    'name' => 'Lx52oORB',
                    'quantity' => 43,
                    'sum' => 904.29,
                    'tax' => 'vat20',
                ],
                '100552_1' => [
                    'price' => 26.1,
                    'name' => '9APZJHJX',
                    'quantity' => 17,
                    'sum' => 443.7,
                    'tax' => 'vat20',
                ],
                '100552_2' => [
                    'price' => 26.11,
                    'name' => '9APZJHJX',
                    'quantity' => 13,
                    'sum' => 339.43,
                    'tax' => 'vat20',
                ],
                '100553_1' => [
                    'price' => 26.1,
                    'name' => '1jSEl731',
                    'quantity' => 6,
                    'sum' => 156.6,
                    'tax' => 'vat20',
                ],
                '100553_2' => [
                    'price' => 26.11,
                    'name' => '1jSEl731',
                    'quantity' => 4,
                    'sum' => 104.44,
                    'tax' => 'vat20',
                ],
                '100554_1' => [
                    'price' => 26.1,
                    'name' => 'mU5bfDEV',
                    'quantity' => 28,
                    'sum' => 730.8,
                    'tax' => 'vat20',
                ],
                '100554_2' => [
                    'price' => 26.11,
                    'name' => 'mU5bfDEV',
                    'quantity' => 22,
                    'sum' => 574.42,
                    'tax' => 'vat20',
                ],
                '100555_1' => [
                    'price' => 23.92,
                    'name' => '3seC8xNP',
                    'quantity' => 1,
                    'sum' => 23.92,
                    'tax' => 'vat20',
                ],
                '100555_2' => [
                    'price' => 23.93,
                    'name' => '3seC8xNP',
                    'quantity' => 9,
                    'sum' => 215.37,
                    'tax' => 'vat20',
                ],
                '100556_1' => [
                    'price' => 26.1,
                    'name' => 'Yf5U4J8g',
                    'quantity' => 11,
                    'sum' => 287.1,
                    'tax' => 'vat20',
                ],
                '100556_2' => [
                    'price' => 26.11,
                    'name' => 'Yf5U4J8g',
                    'quantity' => 9,
                    'sum' => 234.99,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 28.28,
                    'name' => 'cbn8w9ya',
                    'quantity' => 20,
                    'sum' => 565.6,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_15] = [
            'sum' => 13189.69,
            'origGrandTotal' => 13189.69,
            'items' => [
                0 => [
                    'price' => 5793.6,
                    'name' => 'kz5oHmL6',
                    'quantity' => 1,
                    'sum' => 5793.6,
                    'tax' => 'vat20',
                ],
                '100559_1' => [
                    'price' => 26.1,
                    'name' => 'cewKmxkL',
                    'quantity' => 25,
                    'sum' => 652.5,
                    'tax' => 'vat20',
                ],
                '100559_2' => [
                    'price' => 26.11,
                    'name' => 'cewKmxkL',
                    'quantity' => 15,
                    'sum' => 391.65,
                    'tax' => 'vat20',
                ],
                '100560_1' => [
                    'price' => 26.1,
                    'name' => 'Mkr1KjKu',
                    'quantity' => 18,
                    'sum' => 469.8,
                    'tax' => 'vat20',
                ],
                '100560_2' => [
                    'price' => 26.11,
                    'name' => 'Mkr1KjKu',
                    'quantity' => 12,
                    'sum' => 313.32,
                    'tax' => 'vat20',
                ],
                '100561_1' => [
                    'price' => 21.02,
                    'name' => '2lqFDqfm',
                    'quantity' => 8,
                    'sum' => 168.16,
                    'tax' => 'vat20',
                ],
                '100561_2' => [
                    'price' => 21.03,
                    'name' => '2lqFDqfm',
                    'quantity' => 32,
                    'sum' => 672.96,
                    'tax' => 'vat20',
                ],
                '100562_1' => [
                    'price' => 21.02,
                    'name' => '91IG7jzc',
                    'quantity' => 10,
                    'sum' => 210.2,
                    'tax' => 'vat20',
                ],
                '100562_2' => [
                    'price' => 21.03,
                    'name' => '91IG7jzc',
                    'quantity' => 40,
                    'sum' => 841.2,
                    'tax' => 'vat20',
                ],
                '100563_1' => [
                    'price' => 26.1,
                    'name' => 'ICYKymBz',
                    'quantity' => 18,
                    'sum' => 469.8,
                    'tax' => 'vat20',
                ],
                '100563_2' => [
                    'price' => 26.11,
                    'name' => 'ICYKymBz',
                    'quantity' => 12,
                    'sum' => 313.32,
                    'tax' => 'vat20',
                ],
                '100564_1' => [
                    'price' => 26.1,
                    'name' => 'gjn12tUv',
                    'quantity' => 6,
                    'sum' => 156.6,
                    'tax' => 'vat20',
                ],
                '100564_2' => [
                    'price' => 26.11,
                    'name' => 'gjn12tUv',
                    'quantity' => 4,
                    'sum' => 104.44,
                    'tax' => 'vat20',
                ],
                '100565_1' => [
                    'price' => 26.1,
                    'name' => '25wUYkjA',
                    'quantity' => 31,
                    'sum' => 809.1,
                    'tax' => 'vat20',
                ],
                '100565_2' => [
                    'price' => 26.11,
                    'name' => '25wUYkjA',
                    'quantity' => 19,
                    'sum' => 496.09,
                    'tax' => 'vat20',
                ],
                '100566_1' => [
                    'price' => 23.92,
                    'name' => 'iFEcrv17',
                    'quantity' => 1,
                    'sum' => 23.92,
                    'tax' => 'vat20',
                ],
                '100566_2' => [
                    'price' => 23.93,
                    'name' => 'iFEcrv17',
                    'quantity' => 9,
                    'sum' => 215.37,
                    'tax' => 'vat20',
                ],
                '100567_1' => [
                    'price' => 26.1,
                    'name' => 'znVtmS3R',
                    'quantity' => 12,
                    'sum' => 313.2,
                    'tax' => 'vat20',
                ],
                '100567_2' => [
                    'price' => 26.11,
                    'name' => 'znVtmS3R',
                    'quantity' => 8,
                    'sum' => 208.88,
                    'tax' => 'vat20',
                ],
                '100568_1' => [
                    'price' => 28.27,
                    'name' => 'ItbDrgQm',
                    'quantity' => 2,
                    'sum' => 56.54,
                    'tax' => 'vat20',
                ],
                '100568_2' => [
                    'price' => 28.28,
                    'name' => 'ItbDrgQm',
                    'quantity' => 18,
                    'sum' => 509.04,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_16] = [
            'sum' => 5190.01,
            'origGrandTotal' => 5190.01,
            'items' => [
                '100569_1' => [
                    'price' => 18.31,
                    'name' => 'v24x4rdR',
                    'quantity' => 9,
                    'sum' => 164.79,
                    'tax' => 'vat20',
                ],
                '100569_2' => [
                    'price' => 18.32,
                    'name' => 'v24x4rdR',
                    'quantity' => 31,
                    'sum' => 567.92,
                    'tax' => 'vat20',
                ],
                '100570_1' => [
                    'price' => 18.31,
                    'name' => 'fbt8jFkP',
                    'quantity' => 7,
                    'sum' => 128.17,
                    'tax' => 'vat20',
                ],
                '100570_2' => [
                    'price' => 18.32,
                    'name' => 'fbt8jFkP',
                    'quantity' => 23,
                    'sum' => 421.36,
                    'tax' => 'vat20',
                ],
                '100571_1' => [
                    'price' => 14.75,
                    'name' => '8x5ke1tD',
                    'quantity' => 16,
                    'sum' => 236,
                    'tax' => 'vat20',
                ],
                '100571_2' => [
                    'price' => 14.76,
                    'name' => '8x5ke1tD',
                    'quantity' => 24,
                    'sum' => 354.24,
                    'tax' => 'vat20',
                ],
                '100572_1' => [
                    'price' => 14.75,
                    'name' => 'abCGLpaj',
                    'quantity' => 20,
                    'sum' => 295,
                    'tax' => 'vat20',
                ],
                '100572_2' => [
                    'price' => 14.76,
                    'name' => 'abCGLpaj',
                    'quantity' => 30,
                    'sum' => 442.8,
                    'tax' => 'vat20',
                ],
                '100573_1' => [
                    'price' => 18.31,
                    'name' => 'X7sQC7ys',
                    'quantity' => 7,
                    'sum' => 128.17,
                    'tax' => 'vat20',
                ],
                '100573_2' => [
                    'price' => 18.32,
                    'name' => 'X7sQC7ys',
                    'quantity' => 23,
                    'sum' => 421.36,
                    'tax' => 'vat20',
                ],
                '100574_1' => [
                    'price' => 18.31,
                    'name' => 'JIiAUxQW',
                    'quantity' => 2,
                    'sum' => 36.62,
                    'tax' => 'vat20',
                ],
                '100574_2' => [
                    'price' => 18.32,
                    'name' => 'JIiAUxQW',
                    'quantity' => 8,
                    'sum' => 146.56,
                    'tax' => 'vat20',
                ],
                '100575_1' => [
                    'price' => 18.31,
                    'name' => '7hqtKHLX',
                    'quantity' => 12,
                    'sum' => 219.72,
                    'tax' => 'vat20',
                ],
                '100575_2' => [
                    'price' => 18.32,
                    'name' => '7hqtKHLX',
                    'quantity' => 38,
                    'sum' => 696.16,
                    'tax' => 'vat20',
                ],
                '100576_1' => [
                    'price' => 16.79,
                    'name' => '4IfUtXvp',
                    'quantity' => 9,
                    'sum' => 151.11,
                    'tax' => 'vat20',
                ],
                '100576_2' => [
                    'price' => 16.8,
                    'name' => '4IfUtXvp',
                    'quantity' => 1,
                    'sum' => 16.8,
                    'tax' => 'vat20',
                ],
                '100577_1' => [
                    'price' => 18.31,
                    'name' => 'cEdxQcNd',
                    'quantity' => 5,
                    'sum' => 91.55,
                    'tax' => 'vat20',
                ],
                '100577_2' => [
                    'price' => 18.32,
                    'name' => 'cEdxQcNd',
                    'quantity' => 15,
                    'sum' => 274.8,
                    'tax' => 'vat20',
                ],
                '100578_1' => [
                    'price' => 19.84,
                    'name' => 'q5Lk3U2W',
                    'quantity' => 12,
                    'sum' => 238.08,
                    'tax' => 'vat20',
                ],
                '100578_2' => [
                    'price' => 19.85,
                    'name' => 'q5Lk3U2W',
                    'quantity' => 8,
                    'sum' => 158.8,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_17] = [
            'sum' => 7989.99,
            'origGrandTotal' => 7989.99,
            'items' => [
                '100579_1' => [
                    'price' => 30.75,
                    'name' => '4kiTDqhU',
                    'quantity' => 56,
                    'sum' => 1722,
                    'tax' => 'vat20',
                ],
                '100579_2' => [
                    'price' => 30.76,
                    'name' => '4kiTDqhU',
                    'quantity' => 44,
                    'sum' => 1353.44,
                    'tax' => 'vat20',
                ],
                0 => [
                    'price' => 4914.55,
                    'name' => 'PqbrzrVx',
                    'quantity' => 1,
                    'sum' => 4914.55,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_18] = [
            'sum' => 0.0,
            'origGrandTotal' => 0.0,
            'items' => [
                0 => [
                    'price' => 0.0,
                    'name' => '4hhM1Zad',
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 0.0,
                    'name' => 'nWllUitS',
                    'quantity' => 2.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_19] = [
            'sum' => '1000.00',
            'origGrandTotal' => 1000.0,
            'items' => [
                '100583_1' => [
                    'price' => 333.33,
                    'name' => 'Lros5n7g',
                    'quantity' => 1,
                    'sum' => 333.33,
                    'tax' => 'vat20',
                ],
                '100583_2' => [
                    'price' => 333.34,
                    'name' => 'Lros5n7g',
                    'quantity' => 1,
                    'sum' => 333.34,
                    'tax' => 'vat20',
                ],
                '100584_1' => [
                    'price' => 166.66,
                    'name' => 'NrBPsYYE',
                    'quantity' => 1.0,
                    'sum' => 166.66,
                    'tax' => 'vat20',
                ],
                '100584_2' => [
                    'price' => 166.67,
                    'name' => 'NrBPsYYE',
                    'quantity' => 1,
                    'sum' => 166.67,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_20] = [
            'sum' => 14671.60,
            'origGrandTotal' => 14671.6,
            'items' => [
                '100585_1' => [
                    'price' => 1144.57,
                    'quantity' => 3.0,
                    'sum' => 3433.71,
                    'tax' => 'vat20',
                ],
                '100585_2' => [
                    'price' => 1144.58,
                    'quantity' => 2,
                    'sum' => 2289.16,
                    'tax' => 'vat20',
                ],
                '100586_1' => [
                    'price' => 2801.85,
                    'name' => 'nNm1wcl2',
                    'quantity' => 1.0,
                    'sum' => 2801.85,
                    'tax' => 'vat20',
                ],
                '100586_2' => [
                    'price' => 2801.86,
                    'quantity' => 2,
                    'sum' => 5603.72,
                    'tax' => 'vat20',
                ],
                100587 => [
                    'price' => 543.16,
                    'quantity' => 1.0,
                    'sum' => 543.16,
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

        $actualData[parent::TEST_CASE_NAME_21] = [
            'sum' => 17431.01,
            'origGrandTotal' => 17431.01,
            'items' => [
                100596 => [
                    'price' => 6350.7,
                    'quantity' => 1,
                    'sum' => 6350.7,
                    'tax' => 'vat20',
                ],
                '100597_1' => [
                    'price' => 18.43,
                    'quantity' => 14,
                    'sum' => 258.02,
                    'tax' => 'vat20',
                ],
                '100597_2' => [
                    'price' => 18.44,
                    'quantity' => 16,
                    'sum' => 295.04,
                    'tax' => 'vat20',
                ],
                '100598_1' => [
                    'price' => 23.52,
                    'quantity' => 36,
                    'sum' => 846.72,
                    'tax' => 'vat20',
                ],
                '100598_2' => [
                    'price' => 23.53,
                    'quantity' => 4,
                    'sum' => 94.12,
                    'tax' => 'vat20',
                ],
                '100599_1' => [
                    'price' => 23.52,
                    'quantity' => 36,
                    'sum' => 846.72,
                    'tax' => 'vat20',
                ],
                '100599_2' => [
                    'price' => 23.53,
                    'quantity' => 4,
                    'sum' => 94.12,
                    'tax' => 'vat20',
                ],
                '100600_1' => [
                    'price' => 23.52,
                    'quantity' => 36,
                    'sum' => 846.72,
                    'tax' => 'vat20',
                ],
                '100600_2' => [
                    'price' => 23.53,
                    'quantity' => 4,
                    'sum' => 94.12,
                    'tax' => 'vat20',
                ],
                '100601_1' => [
                    'price' => 23.52,
                    'quantity' => 36,
                    'sum' => 846.72,
                    'tax' => 'vat20',
                ],
                '100601_2' => [
                    'price' => 23.53,
                    'quantity' => 4,
                    'sum' => 94.12,
                    'tax' => 'vat20',
                ],
                '100602_1' => [
                    'price' => 23.52,
                    'quantity' => 36,
                    'sum' => 846.72,
                    'tax' => 'vat20',
                ],
                '100602_2' => [
                    'price' => 23.53,
                    'quantity' => 4,
                    'sum' => 94.12,
                    'tax' => 'vat20',
                ],
                '100603_1' => [
                    'price' => 22.88,
                    'quantity' => 5,
                    'sum' => 114.4,
                    'tax' => 'vat20',
                ],
                '100603_2' => [
                    'price' => 22.89,
                    'quantity' => 5,
                    'sum' => 114.45,
                    'tax' => 'vat20',
                ],
                '100604_1' => [
                    'price' => 18.43,
                    'quantity' => 27,
                    'sum' => 497.61,
                    'tax' => 'vat20',
                ],
                '100604_2' => [
                    'price' => 18.44,
                    'quantity' => 33,
                    'sum' => 608.52,
                    'tax' => 'vat20',
                ],
                '100605_1' => [
                    'price' => 18.43,
                    'quantity' => 37,
                    'sum' => 681.91,
                    'tax' => 'vat20',
                ],
                '100605_2' => [
                    'price' => 18.44,
                    'quantity' => 43,
                    'sum' => 792.92,
                    'tax' => 'vat20',
                ],
                '100606_1' => [
                    'price' => 20.97,
                    'quantity' => 5,
                    'sum' => 104.85,
                    'tax' => 'vat20',
                ],
                '100606_2' => [
                    'price' => 20.98,
                    'quantity' => 25,
                    'sum' => 524.5,
                    'tax' => 'vat20',
                ],
                '100607_1' => [
                    'price' => 20.97,
                    'quantity' => 4,
                    'sum' => 83.88,
                    'tax' => 'vat20',
                ],
                '100607_2' => [
                    'price' => 20.98,
                    'quantity' => 16,
                    'sum' => 335.68,
                    'tax' => 'vat20',
                ],
                '100608_1' => [
                    'price' => 20.97,
                    'quantity' => 2,
                    'sum' => 41.94,
                    'tax' => 'vat20',
                ],
                '100608_2' => [
                    'price' => 20.98,
                    'quantity' => 8,
                    'sum' => 167.84,
                    'tax' => 'vat20',
                ],
                '100609_1' => [
                    'price' => 29.24,
                    'quantity' => 15,
                    'sum' => 438.6,
                    'tax' => 'vat20',
                ],
                '100609_2' => [
                    'price' => 29.25,
                    'quantity' => 5,
                    'sum' => 146.25,
                    'tax' => 'vat20',
                ],
                '100610_1' => [
                    'price' => 29.24,
                    'quantity' => 15,
                    'sum' => 438.6,
                    'tax' => 'vat20',
                ],
                '100610_2' => [
                    'price' => 29.25,
                    'quantity' => 5,
                    'sum' => 146.25,
                    'tax' => 'vat20',
                ],
                '100611_1' => [
                    'price' => 29.24,
                    'quantity' => 15,
                    'sum' => 438.6,
                    'tax' => 'vat20',
                ],
                '100611_2' => [
                    'price' => 29.25,
                    'quantity' => 5,
                    'sum' => 146.25,
                    'tax' => 'vat20',
                ],
                100612 => [
                    'price' => 0,
                    'quantity' => 4,
                    'sum' => 0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_22] = [
            'sum' => 0.0,
            'origGrandTotal' => 10.0,
            'items' => [
                100605 => [
                    'price' => 0.0,
                    'name' => 'MI1yi2wG',
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100606 => [
                    'price' => 0.0,
                    'name' => 'oAScgwsB',
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 10.0,
                    'quantity' => 1.0,
                    'sum' => 10.0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_23] = [
            'sum' => 0.0,
            'origGrandTotal' => 200.0,
            'items' => [
                100607 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100608 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 200.0,
                    'quantity' => 1.0,
                    'sum' => 200.0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_24] = [
            'sum' => 0.0,
            'origGrandTotal' => 0.0,
            'items' => [
                100609 => [
                    'price' => 0.0,
                    'name' => 'LIprnTaA',
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_25] = [
            'sum' => '19830.00',
            'origGrandTotal' => 19830.0,
            'items' => [
                100610 => [
                    'price' => 6643.26,
                    'name' => 'Product 1',
                    'quantity' => 1,
                    'sum' => 6643.26,
                    'tax' => 'vat20',
                ],
                '100611_1' => [
                    'price' => 65.83,
                    'name' => 'Product 2',
                    'quantity' => 6,
                    'sum' => 394.98,
                    'tax' => 'vat20',
                ],
                '100611_2' => [
                    'price' => 65.84,
                    'name' => 'Product 2',
                    'quantity' => 4,
                    'sum' => 263.36,
                    'tax' => 'vat20',
                ],
                '100612_1' => [
                    'price' => 27.26,
                    'name' => 'Product 3',
                    'quantity' => 5,
                    'sum' => 136.3,
                    'tax' => 'vat20',
                ],
                '100612_2' => [
                    'price' => 27.27,
                    'name' => 'Product 3',
                    'quantity' => 5,
                    'sum' => 136.35,
                    'tax' => 'vat20',
                ],
                '100613_1' => [
                    'price' => 27.26,
                    'name' => 'Product 4',
                    'quantity' => 11,
                    'sum' => 299.86,
                    'tax' => 'vat20',
                ],
                '100613_2' => [
                    'price' => 27.27,
                    'name' => 'Product 4',
                    'quantity' => 9,
                    'sum' => 245.43,
                    'tax' => 'vat20',
                ],
                '100614_1' => [
                    'price' => 20.61,
                    'name' => 'Product 5',
                    'quantity' => 27,
                    'sum' => 556.47,
                    'tax' => 'vat20',
                ],
                '100614_2' => [
                    'price' => 20.62,
                    'name' => 'Product 5',
                    'quantity' => 23,
                    'sum' => 474.26,
                    'tax' => 'vat20',
                ],
                '100615_1' => [
                    'price' => 20.61,
                    'name' => 'Product 6',
                    'quantity' => 32,
                    'sum' => 659.52,
                    'tax' => 'vat20',
                ],
                '100615_2' => [
                    'price' => 20.62,
                    'name' => 'Product 6',
                    'quantity' => 28,
                    'sum' => 577.36,
                    'tax' => 'vat20',
                ],
                '100616_1' => [
                    'price' => 27.26,
                    'name' => 'Product 7',
                    'quantity' => 27,
                    'sum' => 736.02,
                    'tax' => 'vat20',
                ],
                '100616_2' => [
                    'price' => 27.27,
                    'name' => 'Product 7',
                    'quantity' => 23,
                    'sum' => 627.21,
                    'tax' => 'vat20',
                ],
                '100617_1' => [
                    'price' => 25.93,
                    'name' => 'Product 8',
                    'quantity' => 27,
                    'sum' => 700.11,
                    'tax' => 'vat20',
                ],
                '100617_2' => [
                    'price' => 25.94,
                    'name' => 'Product 8',
                    'quantity' => 23,
                    'sum' => 596.62,
                    'tax' => 'vat20',
                ],
                '100618_1' => [
                    'price' => 27.26,
                    'name' => 'Product 9',
                    'quantity' => 27,
                    'sum' => 736.02,
                    'tax' => 'vat20',
                ],
                '100618_2' => [
                    'price' => 27.27,
                    'name' => 'Product 9',
                    'quantity' => 23,
                    'sum' => 627.21,
                    'tax' => 'vat20',
                ],
                '100619_1' => [
                    'price' => 27.26,
                    'name' => 'Product 10',
                    'quantity' => 27,
                    'sum' => 736.02,
                    'tax' => 'vat20',
                ],
                '100619_2' => [
                    'price' => 27.27,
                    'name' => 'Product 10',
                    'quantity' => 23,
                    'sum' => 627.21,
                    'tax' => 'vat20',
                ],
                '100620_1' => [
                    'price' => 27.26,
                    'name' => 'Product 11',
                    'quantity' => 27,
                    'sum' => 736.02,
                    'tax' => 'vat20',
                ],
                '100620_2' => [
                    'price' => 27.27,
                    'name' => 'Product 11',
                    'quantity' => 23,
                    'sum' => 627.21,
                    'tax' => 'vat20',
                ],
                '100621_1' => [
                    'price' => 20.61,
                    'name' => 'Product 12',
                    'quantity' => 27,
                    'sum' => 556.47,
                    'tax' => 'vat20',
                ],
                '100621_2' => [
                    'price' => 20.62,
                    'name' => 'Product 12',
                    'quantity' => 23,
                    'sum' => 474.26,
                    'tax' => 'vat20',
                ],
                '100622_1' => [
                    'price' => 33.24,
                    'name' => 'Product 13',
                    'quantity' => 3,
                    'sum' => 99.72,
                    'tax' => 'vat20',
                ],
                '100622_2' => [
                    'price' => 33.25,
                    'name' => 'Product 13',
                    'quantity' => 47,
                    'sum' => 1562.75,
                    'tax' => 'vat20',
                ],
                100623 => [
                    'price' => 0.0,
                    'name' => 'Product 14',
                    'quantity' => 4.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_26] = [
            'sum' => '10307.32',
            'origGrandTotal' => 10307.32,
            'items' => [
                100624 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100625 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100626 => [
                    'price' => 5153.66,
                    'quantity' => 1.0,
                    'sum' => 5153.66,
                    'tax' => 'vat20',
                ],
                100627 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100628 => [
                    'price' => 5153.66,
                    'quantity' => 1.0,
                    'sum' => 5153.66,
                    'tax' => 'vat20',
                ],
                100629 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
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

        $actualData[parent::TEST_CASE_NAME_27] = $actualData[parent::TEST_CASE_NAME_18];

        $actualData[parent::TEST_CASE_NAME_28] = [
            'sum' => '0.00',
            'origGrandTotal' => 100.0,
            'items' => [
                100630 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 100.0,
                    'quantity' => 1.0,
                    'sum' => 100.0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_29] = [
            'sum' => 1200.00,
            'origGrandTotal' => 1320.0,
            'items' => [
                100633 => [
                    'price' => 1200.0,
                    'quantity' => 1.0,
                    'sum' => 1200.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 120.0,
                    'quantity' => 1.0,
                    'sum' => 120.0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_30] = [
            'sum' => 1200.00,
            'origGrandTotal' => 1314.0,
            'items' => [
                100634 => [
                    'price' => 1200.0,
                    'quantity' => 1.0,
                    'sum' => 1200.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 114.0,
                    'quantity' => 1.0,
                    'sum' => 114.0,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_31] = [
            'sum' => 1200.00,
            'origGrandTotal' => 1200.0,
            'items' => [
                100635 => [
                    'price' => 138.57,
                    'quantity' => 1,
                    'sum' => 138.57,
                    'tax' => 'vat20',
                ],
                100636 => [
                    'price' => 138.57,
                    'quantity' => 1,
                    'sum' => 138.57,
                    'tax' => 'vat20',
                ],
                100637 => [
                    'price' => 922.86,
                    'quantity' => 1.0,
                    'sum' => 922.86,
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

        return $actualData;
    }
}
