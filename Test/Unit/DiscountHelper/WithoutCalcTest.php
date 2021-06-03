<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\DiscountHelper;

class WithoutCalcTest extends GeneralTestCase
{
    protected function setUp(): void
    {
        $this->discountHelper = $this->getDiscountHelperInstance();
        $this->discountHelper->setDoCalculation(false);
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

            $sumEqual = bccomp($recalcExpectedItems[$index]['sum'], $recalcItem['sum']);
            $this->assertEquals($sumEqual, 0, 'Sum of item failed');
        }
    }

    /** Для этой группы тестов - если есть глобальная скидка, то мы все равно пытаемся её распределить между позициями
     * @return mixed
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected static function getExpected()
    {
        $actualData[parent::TEST_CASE_NAME_1] = [
            'sum' => 12069.30,
            'origGrandTotal' => 12069.30,
            'items' => [
                0 => [
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 11691,
                ],
                1 => [
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 378.30,
                ],
                2 => [
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 0,
                ],
                'shipping' => [
                    'price' => 0,
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
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 5054.4,
                ],
                153 => [
                    'price' => 1,
                    'quantity' => 3,
                    'sum' => 31.79,
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
                0 => [
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 5015.28,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 1,
                    'quantity' => 3,
                    'sum' => 70.91,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 20,
                    'quantity' => 1,
                    'sum' => 20,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_4] = [
            'sum' => 5000.86,
            'origGrandTotal' => 5200.86,
            'items' => [
                152 => [
                    'price' => 1,
                    'quantity' => 2,
                    'sum' => 1000.82,
                ],
                153 => [
                    'price' => 1,
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
                    'price' => 1,
                    'quantity' => 3,
                    'sum' => 101,
                ],
                '153_1' => [
                    'price' => 1,
                    'quantity' => 4,
                    'sum' => 101.01,
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
                0 => [
                    'price' => 1,
                    'quantity' => 3,
                    'sum' => 101,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 1,
                    'quantity' => 4,
                    'sum' => 101.1,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 1,
                    'quantity' => 5,
                    'sum' => 500,
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

        $actualData[parent::TEST_CASE_NAME_7] = [
            'sum' => 11691,
            'origGrandTotal' => 11691,
            'items' => [
                0 => [
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 11691,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 1,
                    'quantity' => 1,
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

        $actualData[parent::TEST_CASE_NAME_8] = [
            'sum' => 11610.99,
            'origGrandTotal' => 11611,
            'items' => [
                0 => [
                    'price' => 11591.15,
                    'quantity' => 1,
                    'sum' => 11591.15,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 19.84,
                    'quantity' => 1,
                    'sum' => 19.84,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0.01,
                    'quantity' => 1,
                    'sum' => 0.01,
                    'tax' => '',
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
                0 => [
                    'price' => 12890.14,
                    'quantity' => 1,
                    'sum' => 12890.14,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 19.84,
                    'quantity' => 1,
                    'sum' => 19.84,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0.01,
                    'quantity' => 1,
                    'sum' => 0.01,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_11] = [
            'sum' => 32130.01,
            'origGrandTotal' => 32130.01,
            'items' => [
                0 => [
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 19990,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 1,
                    'quantity' => 500,
                    'sum' => 9500,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 1000.01,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 1,
                    'quantity' => 4,
                    'sum' => 1640,
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

        $actualData[parent::TEST_CASE_NAME_12] = [
            'sum' => 13188.99,
            'origGrandTotal' => 13189.99,
            'items' => [
                0 => [
                    'price' => 7989.99,
                    'quantity' => 1,
                    'sum' => 7989.99,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 18.35,
                    'quantity' => 40,
                    'sum' => 734,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 18.35,
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 14.78,
                    'quantity' => 40,
                    'sum' => 591.2,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 14.78,
                    'quantity' => 50,
                    'sum' => 739,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 18.35,
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 18.35,
                    'quantity' => 10,
                    'sum' => 183.5,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 18.35,
                    'quantity' => 50,
                    'sum' => 917.5,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 16.82,
                    'quantity' => 10,
                    'sum' => 168.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 18.35,
                    'quantity' => 20,
                    'sum' => 367,
                    'tax' => 'vat20',
                ],
                10 => [
                    'price' => 19.88,
                    'quantity' => 20,
                    'sum' => 397.6,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 1,
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
            'sum' => 13189.01,
            'origGrandTotal' => 13190.01,
            'items' => [
                0 => [
                    'price' => 7990.01,
                    'quantity' => 1,
                    'sum' => 7990.01,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 18.35,
                    'quantity' => 40,
                    'sum' => 734,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 18.35,
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 14.78,
                    'quantity' => 40,
                    'sum' => 591.2,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 14.78,
                    'quantity' => 50,
                    'sum' => 739,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 18.35,
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 18.35,
                    'quantity' => 10,
                    'sum' => 183.5,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 18.35,
                    'quantity' => 50,
                    'sum' => 917.5,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 16.82,
                    'quantity' => 10,
                    'sum' => 168.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 18.35,
                    'quantity' => 20,
                    'sum' => 367,
                    'tax' => 'vat20',
                ],
                10 => [
                    'price' => 19.88,
                    'quantity' => 20,
                    'sum' => 397.6,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 1,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_15] = [
            'sum' => 13188.96,
            'origGrandTotal' => 13189.69,
            'items' => [
                0 => [
                    'price' => 7989.96,
                    'quantity' => 1,
                    'sum' => 7989.96,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 18.35,
                    'quantity' => 40,
                    'sum' => 734,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 18.35,
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 14.78,
                    'quantity' => 40,
                    'sum' => 591.2,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 14.78,
                    'quantity' => 50,
                    'sum' => 739,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 18.35,
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 18.35,
                    'quantity' => 10,
                    'sum' => 183.5,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 18.35,
                    'quantity' => 50,
                    'sum' => 917.5,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 16.82,
                    'quantity' => 10,
                    'sum' => 168.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 18.35,
                    'quantity' => 20,
                    'sum' => 367,
                    'tax' => 'vat20',
                ],
                10 => [
                    'price' => 19.88,
                    'quantity' => 20,
                    'sum' => 397.6,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0.73,
                    'quantity' => 1,
                    'sum' => 0.73,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_16] = [
            'sum' => 5188.4,
            'origGrandTotal' => 5190.01,
            'items' => [
                0 => [
                    'price' => 18.31,
                    'quantity' => 40,
                    'sum' => 732.4,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 18.3,
                    'quantity' => 30,
                    'sum' => 549,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 14.75,
                    'quantity' => 40,
                    'sum' => 590,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 14.76,
                    'quantity' => 50,
                    'sum' => 738,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 18.31,
                    'quantity' => 30,
                    'sum' => 549.3,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 18.26,
                    'quantity' => 10,
                    'sum' => 182.6,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 18.33,
                    'quantity' => 50,
                    'sum' => 916.5,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 16.74,
                    'quantity' => 10,
                    'sum' => 167.4,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 18.31,
                    'quantity' => 20,
                    'sum' => 366.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 19.85,
                    'quantity' => 20,
                    'sum' => 397,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 1.61,
                    'quantity' => 1,
                    'sum' => 1.61,
                    'tax' => '',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_17] = [
            'sum' => 7989.99,
            'origGrandTotal' => 7989.99,
            'items' => [
                0 => [
                    'price' => 0,
                    'quantity' => 100,
                    'sum' => 0,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 7989.99,
                    'quantity' => 1,
                    'sum' => 7989.99,
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
            'sum' => 14671.56,
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
            'sum' => 17431.01,
            'origGrandTotal' => 17431.01,
            'items' => [
                100596 => [
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 1,
                    'tax' => 'vat20',
                ],
                100597 => [
                    'price' => 1,
                    'quantity' => 30,
                    'sum' => 870.01,
                    'tax' => 'vat20',
                ],
                100598 => [
                    'price' => 1,
                    'quantity' => 40,
                    'sum' => 1480,
                    'tax' => 'vat20',
                ],
                100599 => [
                    'price' => 1,
                    'quantity' => 40,
                    'sum' => 1480,
                    'tax' => 'vat20',
                ],
                100600 => [
                    'price' => 1,
                    'quantity' => 40,
                    'sum' => 1480,
                    'tax' => 'vat20',
                ],
                100601 => [
                    'price' => 1,
                    'quantity' => 40,
                    'sum' => 1480,
                    'tax' => 'vat20',
                ],
                100602 => [
                    'price' => 1,
                    'quantity' => 40,
                    'sum' => 1480,
                    'tax' => 'vat20',
                ],
                100603 => [
                    'price' => 1,
                    'quantity' => 10,
                    'sum' => 360,
                    'tax' => 'vat20',
                ],
                100604 => [
                    'price' => 1,
                    'quantity' => 60,
                    'sum' => 1740,
                    'tax' => 'vat20',
                ],
                100605 => [
                    'price' => 1,
                    'quantity' => 80,
                    'sum' => 2320,
                    'tax' => 'vat20',
                ],
                100606 => [
                    'price' => 1,
                    'quantity' => 30,
                    'sum' => 990,
                    'tax' => 'vat20',
                ],
                100607 => [
                    'price' => 1,
                    'quantity' => 20,
                    'sum' => 660,
                    'tax' => 'vat20',
                ],
                100608 => [
                    'price' => 1,
                    'quantity' => 10,
                    'sum' => 330,
                    'tax' => 'vat20',
                ],
                100609 => [
                    'price' => 1,
                    'quantity' => 20,
                    'sum' => 920,
                    'tax' => 'vat20',
                ],
                100610 => [
                    'price' => 1,
                    'quantity' => 20,
                    'sum' => 920,
                    'tax' => 'vat20',
                ],
                100611 => [
                    'price' => 1,
                    'quantity' => 20,
                    'sum' => 920,
                    'tax' => 'vat20',
                ],
                100612 => [
                    'price' => 1,
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
                    'price' => 1,
                    'name' => 'MI1yi2wG',
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100606 => [
                    'price' => 1,
                    'name' => 'oAScgwsB',
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 10,
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
                    'price' => 1,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100608 => [
                    'price' => 1,
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
                    'price' => 1,
                    'name' => 'sGl8Rksk',
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
                    'price' => 1,
                    'name' => 'Product 1',
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100611 => [
                    'price' => 1,
                    'name' => 'Product 2',
                    'quantity' => 10.0,
                    'sum' => 990.0,
                    'tax' => 'vat20',
                ],
                100612 => [
                    'price' => 1,
                    'name' => 'Product 3',
                    'quantity' => 10.0,
                    'sum' => 410.0,
                    'tax' => 'vat20',
                ],
                100613 => [
                    'price' => 1,
                    'name' => 'Product 4',
                    'quantity' => 20.0,
                    'sum' => 820.0,
                    'tax' => 'vat20',
                ],
                100614 => [
                    'price' => 1,
                    'name' => 'Product 5',
                    'quantity' => 50.0,
                    'sum' => 1550.0,
                    'tax' => 'vat20',
                ],
                100615 => [
                    'price' => 1,
                    'name' => 'Product 6',
                    'quantity' => 60.0,
                    'sum' => 1860.0,
                    'tax' => 'vat20',
                ],
                100616 => [
                    'price' => 1,
                    'name' => 'Product 7',
                    'quantity' => 50.0,
                    'sum' => 2050.0,
                    'tax' => 'vat20',
                ],
                100617 => [
                    'price' => 1,
                    'name' => 'Product 8',
                    'quantity' => 50.0,
                    'sum' => 1950.0,
                    'tax' => 'vat20',
                ],
                100618 => [
                    'price' => 1,
                    'name' => 'Product 9',
                    'quantity' => 50.0,
                    'sum' => 2050.0,
                    'tax' => 'vat20',
                ],
                100619 => [
                    'price' => 1,
                    'name' => 'Product 10',
                    'quantity' => 50.0,
                    'sum' => 2050.0,
                    'tax' => 'vat20',
                ],
                100620 => [
                    'price' => 1,
                    'name' => 'Product 11',
                    'quantity' => 50.0,
                    'sum' => 2050.0,
                    'tax' => 'vat20',
                ],
                100621 => [
                    'price' => 1,
                    'name' => 'Product 12',
                    'quantity' => 50.0,
                    'sum' => 1550.0,
                    'tax' => 'vat20',
                ],
                100622 => [
                    'price' => 1,
                    'name' => 'Product 13',
                    'quantity' => 50.0,
                    'sum' => 2500.0,
                    'tax' => 'vat20',
                ],
                100623 => [
                    'price' => 1,
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
                    'price' => 10307.32,
                    'quantity' => 1.0,
                    'sum' => 10307.32,
                    'tax' => 'vat20',
                ],
                100627 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100628 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
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
                    'price' => 1,
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
                    'price' => 1.0,
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
                    'price' => 1.0,
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
            'sum' => 1199.98,
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

        $actualData[parent::TEST_CASE_NAME_32] = [
            'sum' => '0.00',
            'origGrandTotal' => 0.0,
            'items' => [
                100635 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_33] = [
            'sum' => '0.00',
            'origGrandTotal' => 5.0,
            'items' => [
                100636 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 5.0,
                    'quantity' => 1.0,
                    'sum' => 5.0,
                    'tax' => 'vat20',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_34] = [
            'sum' => '0.00',
            'origGrandTotal' => 0.0,
            'items' => [
                100637 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100638 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100639 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
            ],
        ];

        return $actualData;
    }
}
