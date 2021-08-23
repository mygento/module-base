<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\DiscountHelper;

class AffectsShippingTest extends GeneralTestCase
{
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
                    'price' => 11691,
                    'quantity' => 1,
                    'sum' => 11691,
                ],
                153 => [
                    'price' => 378.30,
                    'quantity' => 1,
                    'sum' => 378.30,
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
            'sum' => 5086.17,
            'origGrandTotal' => 9373.19,
            'items' => [
                152 => [
                    'price' => 5054.4,
                    'quantity' => 1,
                    'sum' => 5054.4,
                ],
                153 => [
                    'price' => 10.59,
                    'quantity' => 3,
                    'sum' => 31.77,
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
                    'quantity' => 1,
                    'sum' => 5015.28,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 23.63,
                    'quantity' => 3,
                    'sum' => 70.89,
                    'tax' => 'vat20',
                ],
                'shipping' => [
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
            'sum' => 202.06,
            'origGrandTotal' => 202.1,
            'items' => [
                152 => [
                    'price' => 33.66,
                    'quantity' => 3,
                    'sum' => 100.98,
                ],
                153 => [
                    'price' => 25.27,
                    'quantity' => 4,
                    'sum' => 101.08,
                ],
                'shipping' => [
                    'price' => 0.04,
                    'quantity' => 1,
                    'sum' => 0.04,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_6] = [
            'sum' => 702.06,
            'origGrandTotal' => 702.1,
            'items' => [
                152 => [
                    'price' => 33.66,
                    'quantity' => 3,
                    'sum' => 100.98,
                ],
                153 => [
                    'price' => 25.27,
                    'quantity' => 4,
                    'sum' => 101.08,
                ],
                154 => [
                    'price' => 100,
                    'quantity' => 5,
                    'sum' => 500,
                ],
                'shipping' => [
                    'price' => 0.04,
                    'quantity' => 1,
                    'sum' => 0.04,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_7] = [
            'sum' => 11691.0,
            'origGrandTotal' => 11691.0,
            'items' => [
                152 => [
                    'price' => 11691.0,
                    'quantity' => 1,
                    'sum' => 11691.0,
                ],
                153 => [
                    'price' => 0.0,
                    'quantity' => 1,
                    'sum' => 0.0,
                ],
                'shipping' => [
                    'price' => 0.00,
                    'quantity' => 1,
                    'sum' => 0.00,
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_8] = [
            'sum' => 11610.99,
            'origGrandTotal' => 11611.0,
            'items' => [
                152 => [
                    'price' => 11591.15,
                    'quantity' => 1,
                    'sum' => 11591.15,
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

        $actualData[parent::TEST_CASE_NAME_9] = [
            'sum' => 12890.0,
            'origGrandTotal' => 12890.0,
            'items' => [
                152 => [
                    'price' => 12890.0,
                    'quantity' => 1,
                    'sum' => 12890.0,
                ],
                'shipping' => [
                    'price' => 0.00,
                    'quantity' => 1,
                    'sum' => 0.00,
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
            'sum' => 32130.01,
            'origGrandTotal' => 32130.01,
            'items' => [
                0 => [
                    'price' => 19990,
                    'quantity' => 1,
                    'sum' => 19990,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 19,
                    'quantity' => 500,
                    'sum' => 9500,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 1000.01,
                    'quantity' => 1,
                    'sum' => 1000.01,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 410,
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
                    'name' => 'P9MNKYhl',
                    'quantity' => 1,
                    'sum' => 7989.99,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 18.35,
                    'name' => '4ERY91mu',
                    'quantity' => 40,
                    'sum' => 734,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 18.35,
                    'name' => 'KfeQ4b7b',
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 14.78,
                    'name' => 't5A2Rxrh',
                    'quantity' => 40,
                    'sum' => 591.2,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 14.78,
                    'name' => 'hK09CiPH',
                    'quantity' => 50,
                    'sum' => 739,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 18.35,
                    'name' => 'tcL2igh1',
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 18.35,
                    'name' => 'xeFWcmLR',
                    'quantity' => 10,
                    'sum' => 183.5,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 18.35,
                    'name' => 'igEqwmIn',
                    'quantity' => 50,
                    'sum' => 917.5,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 16.82,
                    'name' => 'eEIuB9Qa',
                    'quantity' => 10,
                    'sum' => 168.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 18.35,
                    'name' => 'Xbkx2msA',
                    'quantity' => 20,
                    'sum' => 367,
                    'tax' => 'vat20',
                ],
                10 => [
                    'price' => 19.88,
                    'name' => 'Frg6nmw6',
                    'quantity' => 20,
                    'sum' => 397.6,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
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
                    'name' => 'MZq6NgUu',
                    'quantity' => 40,
                    'sum' => 734,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 18.35,
                    'name' => 'rFGIjBMZ',
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 14.78,
                    'name' => 'LjayXom2',
                    'quantity' => 40,
                    'sum' => 591.2,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 14.78,
                    'name' => 'n2uBhwkF',
                    'quantity' => 50,
                    'sum' => 739,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 18.35,
                    'name' => 'JFe1Ch42',
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 18.35,
                    'name' => 'lA3FKtTZ',
                    'quantity' => 10,
                    'sum' => 183.5,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 18.35,
                    'name' => 'XKrOuj4K',
                    'quantity' => 50,
                    'sum' => 917.5,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 16.82,
                    'name' => 'Xwd89uKe',
                    'quantity' => 10,
                    'sum' => 168.2,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 18.35,
                    'name' => 'Zf8D4wlJ',
                    'quantity' => 20,
                    'sum' => 367,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 19.88,
                    'name' => 'IerjMQ0P',
                    'quantity' => 20,
                    'sum' => 397.6,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
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
                    'name' => '5pX9HUkK',
                    'quantity' => 1,
                    'sum' => 7990.01,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 18.35,
                    'name' => 'RVauXqDg',
                    'quantity' => 40,
                    'sum' => 734,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 18.35,
                    'name' => 'eXMVmtNM',
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 14.78,
                    'name' => 'rKpM7xsp',
                    'quantity' => 40,
                    'sum' => 591.2,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 14.78,
                    'name' => 'f3UEyJsu',
                    'quantity' => 50,
                    'sum' => 739,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 18.35,
                    'name' => 'QRZPcPcn',
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 18.35,
                    'name' => 'SjPkhLxi',
                    'quantity' => 10,
                    'sum' => 183.5,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 18.35,
                    'name' => 'ogIuOKZy',
                    'quantity' => 50,
                    'sum' => 917.5,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 16.82,
                    'name' => 'ShFH5gnF',
                    'quantity' => 10,
                    'sum' => 168.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 18.35,
                    'name' => 'ZdKlAkHt',
                    'quantity' => 20,
                    'sum' => 367,
                    'tax' => 'vat20',
                ],
                10 => [
                    'price' => 19.88,
                    'name' => 'QOrfYuvX',
                    'quantity' => 20,
                    'sum' => 397.6,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
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
                    'name' => 'Z7aMJRHe',
                    'quantity' => 1,
                    'sum' => 7989.96,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 18.35,
                    'name' => 'ZGzlY3lG',
                    'quantity' => 40,
                    'sum' => 734,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 18.35,
                    'name' => '0QhYCfOx',
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 14.78,
                    'name' => 'Ddebej9A',
                    'quantity' => 40,
                    'sum' => 591.2,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 14.78,
                    'name' => 'x3B4cOC0',
                    'quantity' => 50,
                    'sum' => 739,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 18.35,
                    'name' => 'y8k9XsO1',
                    'quantity' => 30,
                    'sum' => 550.5,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 18.35,
                    'name' => 'jKIoyDr1',
                    'quantity' => 10,
                    'sum' => 183.5,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 18.35,
                    'name' => 'ScpiBhc7',
                    'quantity' => 50,
                    'sum' => 917.5,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 16.82,
                    'name' => 'ZsULLCo2',
                    'quantity' => 10,
                    'sum' => 168.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 18.35,
                    'name' => 'C2mcTUaA',
                    'quantity' => 20,
                    'sum' => 367,
                    'tax' => 'vat20',
                ],
                10 => [
                    'price' => 19.88,
                    'name' => 'XyibNKuf',
                    'quantity' => 20,
                    'sum' => 397.6,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
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
                    'name' => 'PYGIbulV',
                    'quantity' => 40,
                    'sum' => 732.4,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 18.3,
                    'name' => 'Fhor0s1t',
                    'quantity' => 30,
                    'sum' => 549,
                    'tax' => 'vat20',
                ],
                2 => [
                    'price' => 14.75,
                    'name' => '2QE7DEhC',
                    'quantity' => 40,
                    'sum' => 590,
                    'tax' => 'vat20',
                ],
                3 => [
                    'price' => 14.76,
                    'name' => 'cuhpCjPM',
                    'quantity' => 50,
                    'sum' => 738,
                    'tax' => 'vat20',
                ],
                4 => [
                    'price' => 18.31,
                    'name' => 'jYG8dyAQ',
                    'quantity' => 30,
                    'sum' => 549.3,
                    'tax' => 'vat20',
                ],
                5 => [
                    'price' => 18.26,
                    'name' => 'Wc9zz8mX',
                    'quantity' => 10,
                    'sum' => 182.6,
                    'tax' => 'vat20',
                ],
                6 => [
                    'price' => 18.33,
                    'name' => 'ShtjbsVl',
                    'quantity' => 50,
                    'sum' => 916.5,
                    'tax' => 'vat20',
                ],
                7 => [
                    'price' => 16.74,
                    'name' => 'JJLF9Lim',
                    'quantity' => 10,
                    'sum' => 167.4,
                    'tax' => 'vat20',
                ],
                8 => [
                    'price' => 18.31,
                    'name' => 'Exyvd5Gy',
                    'quantity' => 20,
                    'sum' => 366.2,
                    'tax' => 'vat20',
                ],
                9 => [
                    'price' => 19.85,
                    'name' => 'YLrmTbGC',
                    'quantity' => 20,
                    'sum' => 397,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
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
                    'name' => 'NGUlxstu',
                    'quantity' => 100,
                    'sum' => 0,
                    'tax' => 'vat20',
                ],
                1 => [
                    'price' => 7989.99,
                    'name' => 'fMLjGnBE',
                    'quantity' => 1,
                    'sum' => 7989.99,
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
            'sum' => 999.98,
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
            'sum' => 17431.3,
            'origGrandTotal' => 17431.01,
            'items' => [
                100596 => [
                    'price' => 1,
                    'quantity' => 1,
                    'sum' => 1,
                    'tax' => 'vat20',
                ],
                100597 => [
                    'price' => 29.01,
                    'quantity' => 30,
                    'sum' => 870.3,
                    'tax' => 'vat20',
                ],
                100598 => [
                    'price' => 37,
                    'quantity' => 40,
                    'sum' => 1480,
                    'tax' => 'vat20',
                ],
                100599 => [
                    'price' => 37,
                    'quantity' => 40,
                    'sum' => 1480,
                    'tax' => 'vat20',
                ],
                100600 => [
                    'price' => 37,
                    'quantity' => 40,
                    'sum' => 1480,
                    'tax' => 'vat20',
                ],
                100601 => [
                    'price' => 37,
                    'quantity' => 40,
                    'sum' => 1480,
                    'tax' => 'vat20',
                ],
                100602 => [
                    'price' => 37,
                    'quantity' => 40,
                    'sum' => 1480,
                    'tax' => 'vat20',
                ],
                100603 => [
                    'price' => 36,
                    'quantity' => 10,
                    'sum' => 360,
                    'tax' => 'vat20',
                ],
                100604 => [
                    'price' => 29,
                    'quantity' => 60,
                    'sum' => 1740,
                    'tax' => 'vat20',
                ],
                100605 => [
                    'price' => 29,
                    'quantity' => 80,
                    'sum' => 2320,
                    'tax' => 'vat20',
                ],
                100606 => [
                    'price' => 33,
                    'quantity' => 30,
                    'sum' => 990,
                    'tax' => 'vat20',
                ],
                100607 => [
                    'price' => 33,
                    'quantity' => 20,
                    'sum' => 660,
                    'tax' => 'vat20',
                ],
                100608 => [
                    'price' => 33,
                    'quantity' => 10,
                    'sum' => 330,
                    'tax' => 'vat20',
                ],
                100609 => [
                    'price' => 46,
                    'quantity' => 20,
                    'sum' => 920,
                    'tax' => 'vat20',
                ],
                100610 => [
                    'price' => 46,
                    'quantity' => 20,
                    'sum' => 920,
                    'tax' => 'vat20',
                ],
                100611 => [
                    'price' => 46,
                    'quantity' => 20,
                    'sum' => 920,
                    'tax' => 'vat20',
                ],
                100612 => [
                    'price' => 0,
                    'quantity' => 4,
                    'sum' => 0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'price' => -0.29,
                    'quantity' => 1,
                    'sum' => -0.29,
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
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                100606 => [
                    'price' => 0.0,
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
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
                    'name' => '4j3FAJT7',
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
            'sum' => 19830.00,
            'origGrandTotal' => 19830,
            'items' => [
                100501 => [
                    'price' => 0,
                    'name' => 'Product 1',
                    'quantity' => 1,
                    'sum' => 0,
                    'tax' => 'vat20',
                ],
                100502 => [
                    'price' => 99,
                    'name' => 'Product 2',
                    'quantity' => 10,
                    'sum' => 990,
                    'tax' => 'vat20',
                ],
                100503 => [
                    'price' => 41,
                    'name' => 'Product 3',
                    'quantity' => 10,
                    'sum' => 410,
                    'tax' => 'vat20',
                ],
                100504 => [
                    'price' => 41,
                    'name' => 'Product 4',
                    'quantity' => 20,
                    'sum' => 820,
                    'tax' => 'vat20',
                ],
                100505 => [
                    'price' => 31,
                    'name' => 'Product 5',
                    'quantity' => 50,
                    'sum' => 1550,
                    'tax' => 'vat20',
                ],
                100506 => [
                    'price' => 31,
                    'name' => 'Product 6',
                    'quantity' => 60,
                    'sum' => 1860,
                    'tax' => 'vat20',
                ],
                100507 => [
                    'price' => 41,
                    'name' => 'Product 7',
                    'quantity' => 50,
                    'sum' => 2050,
                    'tax' => 'vat20',
                ],
                100508 => [
                    'price' => 39,
                    'name' => 'Product 8',
                    'quantity' => 50,
                    'sum' => 1950,
                    'tax' => 'vat20',
                ],
                100509 => [
                    'price' => 41,
                    'name' => 'Product 9',
                    'quantity' => 50,
                    'sum' => 2050,
                    'tax' => 'vat20',
                ],
                100510 => [
                    'price' => 41,
                    'name' => 'Product 10',
                    'quantity' => 50,
                    'sum' => 2050,
                    'tax' => 'vat20',
                ],
                100511 => [
                    'price' => 41,
                    'name' => 'Product 11',
                    'quantity' => 50,
                    'sum' => 2050,
                    'tax' => 'vat20',
                ],
                100512 => [
                    'price' => 31,
                    'name' => 'Product 12',
                    'quantity' => 50,
                    'sum' => 1550,
                    'tax' => 'vat20',
                ],
                100513 => [
                    'price' => 50,
                    'name' => 'Product 13',
                    'quantity' => 50,
                    'sum' => 2500,
                    'tax' => 'vat20',
                ],
                100514 => [
                    'price' => 0,
                    'name' => 'Product 14',
                    'quantity' => 4,
                    'sum' => 0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0,
                    'quantity' => 1,
                    'sum' => 0,
                    'tax' => 'vat20',
                ],
            ],
        ];

        $actualData[parent::TEST_CASE_NAME_26] = [
            'sum' => 10307.32,
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
            'sum' => 0.00,
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
            'sum' => 0.00,
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
            'sum' => 0.00,
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
            'sum' => 0.00,
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

        $actualData[parent::TEST_CASE_NAME_35] = [
            'sum' => 5567.00,
            'origGrandTotal' => 5567.03,
            'items' => [
                100501 => [
                    'price' => 532.0,
                    'name' => 'NRqmmImx',
                    'quantity' => 8.0,
                    'sum' => 4256.0,
                    'tax' => 'vat20',
                ],
                100502 => [
                    'price' => 655.5,
                    'name' => 'wPtiqmj9',
                    'quantity' => 2.0,
                    'sum' => 1311.0,
                    'tax' => 'vat20',
                ],
                100503 => [
                    'price' => 0.0,
                    'name' => 'NRgfA8Fx',
                    'quantity' => 1.0,
                    'sum' => 0.0,
                    'tax' => 'vat20',
                ],
                'shipping' => [
                    'name' => '',
                    'price' => 0.03,
                    'quantity' => 1.0,
                    'sum' => 0.03,
                    'tax' => '',
                ],
            ],
        ];

        return $actualData;
    }
}
