<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\DiscountHelper;

class SpreadOnAllItemsTest extends GeneralTestCase
{
    protected function setUp(): void
    {
        $this->discountHelper = $this->getDiscountHelperInstance();
        $this->discountHelper->setSpreadDiscOnAllUnits(true);
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
            'sum' => 12069.29,
            'origGrandTotal' => 12069.30,
            'items' => [
                152 => [
                    'price' => 11717.5,
                    'quantity' => 1,
                    'sum' => 11717.5,
                ],
                153 => [
                    'price' => 351.79,
                    'quantity' => 1,
                    'sum' => 351.79,
                ],
                154 => [
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                ],
                'shipping' => [
                    'price' => 0.01,
                    'quantity' => 1,
                    'sum' => 0.01,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_2] = [
            'sum' => 5086.17,
            'origGrandTotal' => 9373.19,
            'items' => [
                152 => [
                    'price' => 5015.28,
                    'quantity' => 1,
                    'sum' => 5015.28,
                ],
                '153_1' => [
                    'price' => 23.63,
                    'quantity' => 3,
                    'sum' => 70.89,
                ],
                'shipping' => [
                    'price' => 4287.02,
                    'quantity' => 1,
                    'sum' => 4287.02,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_3] = [
            'sum' => 5086.17,
            'origGrandTotal' => 5106.19,
            'items' => [
                0 => [
                    'price' => 5015.28,
                    'name' => 'EUzlTJZ0',
                    'quantity' => 1,
                    'sum' => 5015.28,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 23.63,
                    'name' => 'QoPowh4G',
                    'quantity' => 3,
                    'sum' => 70.89,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 20.02,
                    'quantity' => 1,
                    'sum' => 20.02,
                    'tax' => '',
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
            'sum' => 202.07,
            'origGrandTotal' => 202.1,
            'items' => [
                152 => [
                    'price' => 36.41,
                    'quantity' => 3,
                    'sum' => 109.23,
                ],
                153 => [
                    'price' => 23.21,
                    'quantity' => 4,
                    'sum' => 92.84,
                ],
                'shipping' => [
                    'price' => 0.03,
                    'quantity' => 1,
                    'sum' => 0.03,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_6] = [
            'sum' => 702.03,
            'origGrandTotal' => 702.1,
            'items' => [
                '152' => [
                    'price' => 38.89,
                    'quantity' => 3,
                    'sum' => 116.67,
                ],
                '153' => [
                    'price' => 24.79,
                    'quantity' => 4,
                    'sum' => 99.16,
                ],
                154 => [
                    'price' => 97.24,
                    'quantity' => 5,
                    'sum' => 486.2,
                ],
                'shipping' => [
                    'price' => 0.07,
                    'quantity' => 1,
                    'sum' => 0.07,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_7] = [
            'sum' => 11690.99,
            'origGrandTotal' => 11691.0,
            'items' => [
                152 => [
                    'price' => 11673.02,
                    'quantity' => 1,
                    'sum' => 11673.02,
                ],
                153 => [
                    'price' => 17.97,
                    'quantity' => 1,
                    'sum' => 17.97,
                ],
                'shipping' => [
                    'price' => 0.01,
                    'quantity' => 1,
                    'sum' => 0.01,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_8] = [
            'sum' => 11610.99,
            'origGrandTotal' => 11611.0,
            'items' => [
                152 => [
                    'price' => 11593.15,
                    'quantity' => 1,
                    'sum' => 11593.15,
                ],
                153 => [
                    'price' => 17.84,
                    'quantity' => 1,
                    'sum' => 17.84,
                ],
                'shipping' => [
                    'price' => 0.01,
                    'quantity' => 1,
                    'sum' => 0.01,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_9] = [
            'sum' => 12890,
            'origGrandTotal' => 12890,
            'items' => [
                0 => [
                    'price' => 12890,
                    'quantity' => 1,
                    'sum' => 12890,
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

        $actualData[parent::TEST_CASE_NAME_10] = [
            'sum' => 12909.98,
            'origGrandTotal' => 12909.99,
            'items' => [
                152 => [
                    'price' => 12890.14,
                    'quantity' => 1,
                    'sum' => 12890.14,
                ],
                153 => [
                    'price' => 19.84,
                    'quantity' => 1,
                    'sum' => 19.84,
                ],
                'shipping' => [
                    'price' => 0.01,
                    'quantity' => 1,
                    'sum' => 0.01,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_11] = [
            'sum' => 32127.55,
            'origGrandTotal' => 32130.01,
            'items' => [
                0 => [
                    'price' => 17298.1,
                    'quantity' => 1,
                    'sum' => 17298.1,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 25.09,
                    'quantity' => 500,
                    'sum' => 12545,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 865.33,
                    'quantity' => 1,
                    'sum' => 865.33,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 354.78,
                    'quantity' => 4,
                    'sum' => 1419.12,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 2.46,
                    'quantity' => 1,
                    'sum' => 2.46,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_12] = [
            'sum' => 13188.13,
            'origGrandTotal' => 13189.99,
            'items' => [
                0 => [
                    'price' => 5793.73,
                    'quantity' => 1,
                    'sum' => 5793.73,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 26.1,
                    'quantity' => 40,
                    'sum' => 1044,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 26.1,
                    'quantity' => 30,
                    'sum' => 783,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 21.02,
                    'quantity' => 40,
                    'sum' => 840.8,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 21.02,
                    'quantity' => 50,
                    'sum' => 1051,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 26.1,
                    'quantity' => 30,
                    'sum' => 783,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 26.1,
                    'quantity' => 10,
                    'sum' => 261,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 26.1,
                    'quantity' => 50,
                    'sum' => 1305,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 23.92,
                    'quantity' => 10,
                    'sum' => 239.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 26.1,
                    'quantity' => 20,
                    'sum' => 522,
                    'tax' => 'vat20',
                ],
                10 => [
                    'price' => 28.27,
                    'quantity' => 20,
                    'sum' => 565.4,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 1.86,
                    'quantity' => 1,
                    'sum' => 1.86,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_13] = [
            'sum' => 5199,
            'origGrandTotal' => 5199.99,
            'items' => [
                0 => [
                    'price' => 18.35,
                    'quantity' => 40,
                    'sum' => 734,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 18.35,
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 14.78,
                    'quantity' => 40,
                    'sum' => 591.2,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 14.78,
                    'quantity' => 50,
                    'sum' => 739,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 18.35,
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 18.35,
                    'quantity' => 10,
                    'sum' => 183.5,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 18.35,
                    'quantity' => 50,
                    'sum' => 917.5,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 16.82,
                    'quantity' => 10,
                    'sum' => 168.2,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 18.35,
                    'quantity' => 20,
                    'sum' => 367,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 19.88,
                    'quantity' => 20,
                    'sum' => 397.6,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0.99,
                    'quantity' => 1,
                    'sum' => 0.99,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_14] = [
            'sum' => 13188.14,
            'origGrandTotal' => 13190.01,
            'items' => [
                0 => [
                    'price' => 5793.74,
                    'quantity' => 1,
                    'sum' => 5793.74,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 26.1,
                    'quantity' => 40,
                    'sum' => 1044,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 26.1,
                    'quantity' => 30,
                    'sum' => 783,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 21.02,
                    'quantity' => 40,
                    'sum' => 840.8,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 21.02,
                    'quantity' => 50,
                    'sum' => 1051,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 26.1,
                    'quantity' => 30,
                    'sum' => 783,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 26.1,
                    'quantity' => 10,
                    'sum' => 261,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 26.1,
                    'quantity' => 50,
                    'sum' => 1305,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 23.92,
                    'quantity' => 10,
                    'sum' => 239.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 26.1,
                    'quantity' => 20,
                    'sum' => 522,
                    'tax' => 'vat20',
                ],
                10 => [
                    'price' => 28.27,
                    'quantity' => 20,
                    'sum' => 565.4,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 1.87,
                    'quantity' => 1,
                    'sum' => 1.87,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_15] = [
            'sum' => 13188,
            'origGrandTotal' => 13189.69,
            'items' => [
                0 => [
                    'price' => 5793.6,
                    'quantity' => 1,
                    'sum' => 5793.6,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 26.1,
                    'quantity' => 40,
                    'sum' => 1044,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 26.1,
                    'quantity' => 30,
                    'sum' => 783,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 21.02,
                    'quantity' => 40,
                    'sum' => 840.8,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 21.02,
                    'quantity' => 50,
                    'sum' => 1051,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 26.1,
                    'quantity' => 30,
                    'sum' => 783,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 26.1,
                    'quantity' => 10,
                    'sum' => 261,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 26.1,
                    'quantity' => 50,
                    'sum' => 1305,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 23.92,
                    'quantity' => 10,
                    'sum' => 239.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 26.1,
                    'quantity' => 20,
                    'sum' => 522,
                    'tax' => 'vat20',
                ],
                10 => [
                    'price' => 28.27,
                    'quantity' => 20,
                    'sum' => 565.4,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 1.69,
                    'quantity' => 1,
                    'sum' => 1.69,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_16] = [
            'sum' => 5188,
            'origGrandTotal' => 5190.01,
            'items' => [
                0 => [
                    'price' => 18.31,
                    'quantity' => 40,
                    'sum' => 732.4,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 18.31,
                    'quantity' => 30,
                    'sum' => 549.3,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 14.75,
                    'quantity' => 40,
                    'sum' => 590,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 14.75,
                    'quantity' => 50,
                    'sum' => 737.5,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 18.31,
                    'quantity' => 30,
                    'sum' => 549.3,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 18.31,
                    'quantity' => 10,
                    'sum' => 183.1,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 18.31,
                    'quantity' => 50,
                    'sum' => 915.5,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 16.79,
                    'quantity' => 10,
                    'sum' => 167.9,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 18.31,
                    'quantity' => 20,
                    'sum' => 366.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 19.84,
                    'quantity' => 20,
                    'sum' => 396.8,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 2.01,
                    'quantity' => 1,
                    'sum' => 2.01,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_17] = [
            'sum' => 7989.54,
            'origGrandTotal' => 7989.99,
            'items' => [
                0 => [
                    'price' => 30.75,
                    'quantity' => 100,
                    'sum' => 3075,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 4914.54,
                    'quantity' => 1,
                    'sum' => 4914.54,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0.45,
                    'quantity' => 1,
                    'sum' => 0.45,
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
            'sum' => '999.98',
            'origGrandTotal' => 1000.0,
            'items' => [
                0 => [
                    'price' => 333.33,
                    'name' => 'YRvTdqS1',
                    'quantity' => 2.0,
                    'sum' => 666.66,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 166.66,
                    'name' => 'ucj8jQte',
                    'quantity' => 2.0,
                    'sum' => 333.32,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0.02,
                    'quantity' => 1.0,
                    'sum' => 0.02,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_20] = [
            'sum' => '14671.56',
            'origGrandTotal' => 14671.6,
            'items' => [
                100585 => [
                    'price' => 1144.57,
                    'quantity' => 5,
                    'sum' => 5722.85,
                    'tax' => 'vat20',
                ],
                100586 => [
                    'price' => 2801.85,
                    'quantity' => 3,
                    'sum' => 8405.55,
                    'tax' => 'vat20',
                ],
                100587 => [
                    'price' => 543.16,
                    'quantity' => 1,
                    'sum' => 543.16,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0.04,
                    'quantity' => 1,
                    'sum' => 0.04,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_21] = [
            'sum' => 17429.18,
            'origGrandTotal' => 17431.01,
            'items' => [
                100596 => [
                    'price' => 6350.68,
                    'quantity' => 1,
                    'sum' => 6350.68,
                    'tax' => 'vat20',
                ],
                100597 => [
                    'price' => 18.43,
                    'quantity' => 30,
                    'sum' => 552.9,
                    'tax' => 'vat20',
                ],
                100598 => [
                    'price' => 23.52,
                    'quantity' => 40,
                    'sum' => 940.8,
                    'tax' => 'vat20',
                ],
                100599 => [
                    'price' => 23.52,
                    'quantity' => 40,
                    'sum' => 940.8,
                    'tax' => 'vat20',
                ],
                100600 => [
                    'price' => 23.52,
                    'quantity' => 40,
                    'sum' => 940.8,
                    'tax' => 'vat20',
                ],
                100601 => [
                    'price' => 23.52,
                    'quantity' => 40,
                    'sum' => 940.8,
                    'tax' => 'vat20',
                ],
                100602 => [
                    'price' => 23.52,
                    'quantity' => 40,
                    'sum' => 940.8,
                    'tax' => 'vat20',
                ],
                100603 => [
                    'price' => 22.88,
                    'quantity' => 10,
                    'sum' => 228.8,
                    'tax' => 'vat20',
                ],
                100604 => [
                    'price' => 18.43,
                    'quantity' => 60,
                    'sum' => 1105.8,
                    'tax' => 'vat20',
                ],
                100605 => [
                    'price' => 18.43,
                    'quantity' => 80,
                    'sum' => 1474.4,
                    'tax' => 'vat20',
                ],
                100606 => [
                    'price' => 20.97,
                    'quantity' => 30,
                    'sum' => 629.1,
                    'tax' => 'vat20',
                ],
                100607 => [
                    'price' => 20.97,
                    'quantity' => 20,
                    'sum' => 419.4,
                    'tax' => 'vat20',
                ],
                100608 => [
                    'price' => 20.97,
                    'quantity' => 10,
                    'sum' => 209.7,
                    'tax' => 'vat20',
                ],
                100609 => [
                    'price' => 29.24,
                    'quantity' => 20,
                    'sum' => 584.8,
                    'tax' => 'vat20',
                ],
                100610 => [
                    'price' => 29.24,
                    'quantity' => 20,
                    'sum' => 584.8,
                    'tax' => 'vat20',
                ],
                100611 => [
                    'price' => 29.24,
                    'quantity' => 20,
                    'sum' => 584.8,
                    'tax' => 'vat20',
                ],
                100612 => [
                    'price' => 0,
                    'quantity' => 4,
                    'sum' => 0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 1.83,
                    'quantity' => 1,
                    'sum' => 1.83,
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
            'sum' => '19827.44',
            'origGrandTotal' => 19830.0,
            'items' => [
                100610 => [
                    'price' => 6643.24,
                    'name' => 'Product 1',
                    'quantity' => 1.0,
                    'sum' => 6643.24,
                    'tax' => 'vat20',
                ],
                100611 => [
                    'price' => 65.83,
                    'name' => 'Product 2',
                    'quantity' => 10.0,
                    'sum' => 658.3,
                    'tax' => 'vat20',
                ],
                100612 => [
                    'price' => 27.26,
                    'name' => 'Product 3',
                    'quantity' => 10.0,
                    'sum' => 272.6,
                    'tax' => 'vat20',
                ],
                100613 => [
                    'price' => 27.26,
                    'name' => 'Product 4',
                    'quantity' => 20.0,
                    'sum' => 545.2,
                    'tax' => 'vat20',
                ],
                100614 => [
                    'price' => 20.61,
                    'name' => 'Product 5',
                    'quantity' => 50.0,
                    'sum' => 1030.5,
                    'tax' => 'vat20',
                ],
                100615 => [
                    'price' => 20.61,
                    'name' => 'Product 6',
                    'quantity' => 60.0,
                    'sum' => 1236.6,
                    'tax' => 'vat20',
                ],
                100616 => [
                    'price' => 27.26,
                    'name' => 'Product 7',
                    'quantity' => 50.0,
                    'sum' => 1363.0,
                    'tax' => 'vat20',
                ],
                100617 => [
                    'price' => 25.93,
                    'name' => 'Product 8',
                    'quantity' => 50.0,
                    'sum' => 1296.5,
                    'tax' => 'vat20',
                ],
                100618 => [
                    'price' => 27.26,
                    'name' => 'Product 9',
                    'quantity' => 50.0,
                    'sum' => 1363.0,
                    'tax' => 'vat20',
                ],
                100619 => [
                    'price' => 27.26,
                    'name' => 'Product 10',
                    'quantity' => 50.0,
                    'sum' => 1363.0,
                    'tax' => 'vat20',
                ],
                100620 => [
                    'price' => 27.26,
                    'name' => 'Product 11',
                    'quantity' => 50.0,
                    'sum' => 1363.0,
                    'tax' => 'vat20',
                ],
                100621 => [
                    'price' => 20.61,
                    'name' => 'Product 12',
                    'quantity' => 50.0,
                    'sum' => 1030.5,
                    'tax' => 'vat20',
                ],
                100622 => [
                    'price' => 33.24,
                    'name' => 'Product 13',
                    'quantity' => 50.0,
                    'sum' => 1662.0,
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
                    'price' => 2.56,
                    'quantity' => 1.0,
                    'sum' => 2.56,
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
            'sum' => '1200.00',
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
            'sum' => '1200.00',
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
            'sum' => '1199.98',
            'origGrandTotal' => 1200.0,
            'items' => [
                100635 => [
                    'price' => 138.56,
                    'quantity' => 1.0,
                    'sum' => 138.56,
                    'tax' => 'vat20',
                ],
                100636 => [
                    'price' => 138.56,
                    'quantity' => 1.0,
                    'sum' => 138.56,
                    'tax' => 'vat20',
                ],
                100637 => [
                    'price' => 922.86,
                    'quantity' => 1.0,
                    'sum' => 922.86,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0.02,
                    'quantity' => 1.0,
                    'sum' => 0.02,
                    'tax' => '',
                ],
            ],
        ];

        return $actualData;
    }
}
