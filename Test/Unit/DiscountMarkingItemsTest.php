<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit;

use Mygento\Base\Api\DiscountHelperInterface;

/**
 * Class DiscountMarkingItemsTest
 * has the same calculation as DiscountSplitItemsTest.
 * Marking-specific features are tested here.
 *
 * @package Mygento\Base\Test\Unit
 */
class DiscountMarkingItemsTest extends DiscountSplitItemsTest
{
    /**
     * @var string
     */
    private $markings = '';

    /**
     * Attention! Order of items in array is important!
     * @dataProvider dataProviderOrdersForCheckCalculation
     * @param mixed $order
     * @param mixed $expectedArray
     */
    public function testCalculation($order, $expectedArray)
    {
        DiscountGeneralTestCase::testCalculation($order, $expectedArray);

        $order->setShippingDescription('test_shipping');
        $this->assertTrue(method_exists($this->discountHelper, 'getRecalculated'));

        $recalculatedData = $this->discountHelper->getRecalculated($order, 'vat20', '', '', 'marking', 'marking_list', 'marking_refund');

        $this->assertEquals($recalculatedData['sum'], $expectedArray['sum'], 'Total sum failed');
        $this->assertEquals($recalculatedData['origGrandTotal'], $expectedArray['origGrandTotal']);

        $this->assertArrayHasKey('items', $recalculatedData);

        $recalcItems = array_values($recalculatedData['items']);
        $recalcExpectedItems = array_values($expectedArray['items']);

        $expectedQty = 0;

        foreach ($recalcExpectedItems as $expectedItem) {
            $expectedQty += $expectedItem['quantity'];
        }

        $recalcQty = 0;
        foreach ($recalcItems as $recalcItem) {
            if ($recalcItem['name'] !== 'test_shipping') {
                $this->assertStringStartsWith('SOME_MARK_', $recalcItem['marking'], 'Marking of item failed');
            }

            $this->assertEquals(1, $recalcItem['quantity']);
            $this->assertEquals($recalcItem['sum'], $recalcItem['price']);
            $recalcQty += $recalcItem['quantity'];
        }

        $this->assertEquals($expectedQty, $recalcQty, 'Items qty is incorrect');
    }

    /**
     * @inheritDoc
     */
    public function getItem(
        $rowTotalInclTax,
        $priceInclTax,
        $discountAmount,
        $qty = 1,
        $taxPercent = 0,
        $taxAmount = 0
    ) {
        $item = parent::getItem($rowTotalInclTax, $priceInclTax, $discountAmount, $qty, $taxPercent, $taxAmount);

        if (empty($this->markings)) {
            $markings = [];

            for ($i = 1; $i < 1000; $i++) {
                $markings[] = base64_encode('SOME_MARK_' . $i);
            }

            $this->markings = implode(',', $markings);
        }

        $markingList = $this->markings;

        $item->setData('marking', true);
        $item->setData('marking_list', $markingList);
        $item->setData('marking_refund', '');

        return $item;
    }

    /** Test splitting item mechanism
     *
     * @dataProvider dataProviderItemsForMarking
     * @param mixed $item
     * @param mixed $expectedArray
     */
    public function testProcessedItem($item, $expectedArray)
    {
        $discountHelper = $this->getDiscountHelperInstance();
        $discountHelper->setIsSplitItemsAllowed(true);

        $dHelper = new \ReflectionClass($discountHelper);

        $markingAttributeCodeAttr = $dHelper->getProperty('markingAttributeCode');
        $markingAttributeCodeAttr->setAccessible(true);
        $markingAttributeCodeAttr->setValue($discountHelper, DiscountHelperInterface::NAME_MARKING);

        $markingAttributeCodeListAttr = $dHelper->getProperty('markingListAttributeCode');
        $markingAttributeCodeListAttr->setAccessible(true);
        $markingAttributeCodeListAttr->setValue($discountHelper, DiscountHelperInterface::NAME_MARKING_LIST);

        $markingAttributeCodeRefundAttr = $dHelper->getProperty('markingRefundAttributeCode');
        $markingAttributeCodeRefundAttr->setAccessible(true);
        $markingAttributeCodeRefundAttr->setValue($discountHelper, DiscountHelperInterface::NAME_MARKING_REFUND);

        $getProcessedItem = $dHelper->getMethod('getProcessedItem');
        $getProcessedItem->setAccessible(true);

        $split = $getProcessedItem->invoke($discountHelper, $item);

        $this->assertCount(count($expectedArray), $split, 'Item was not splitted correctly!');

        $i = 0;
        foreach ($split as $splitItem) {
            $this->assertEquals($expectedArray[$i]['price'], $splitItem['price'], 'Price of item failed');
            $this->assertEquals($expectedArray[$i]['quantity'], $splitItem['quantity']);
            $this->assertEquals($expectedArray[$i]['sum'], $splitItem['sum'], 'Sum of item failed');
            $this->assertEquals($expectedArray[$i]['marking'], $splitItem['marking'], 'Marking of item failed');

            $this->assertEquals($splitItem['name'], $item->getName(), 'Name of item failed');

            $i++;
        }
    }

    /**
     * @dataProvider dataProviderItemsMarkItems
     * @param mixed $item
     * @param mixed $expectedArray
     */
    public function testMarkItems($item, $expectedArray)
    {
        $discountHelper = $this->getDiscountHelperInstance();

        $dHelper = new \ReflectionClass($discountHelper);

        $getProcessedItem = $dHelper->getMethod('markItems');
        $getProcessedItem->setAccessible(true);

        $marked = $getProcessedItem->invokeArgs($discountHelper, [
            'items' => $item[0],
            'marks' => $item[1],
        ]);

        $i = 0;
        foreach ($marked as $result) {
            $this->assertEquals($expectedArray[$i]['marking'], $result['marking'], 'Marking of item failed');
            $i++;
        }
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function dataProviderItemsMarkItems()
    {
        return [
            '#case 1. 10 элементов с 10 маркировками' => [
                [
                    [[], [], [], [], [], [], [], [], [], []],
                    [
                        'SOME_MARK_1', 'SOME_MARK_2', 'SOME_MARK_3', 'SOME_MARK_4', 'SOME_MARK_5', 'SOME_MARK_6',
                        'SOME_MARK_7', 'SOME_MARK_8', 'SOME_MARK_9', 'SOME_MARK_10',
                    ],
                ],
                [
                    [
                        'marking' => 'SOME_MARK_1',
                    ],
                    [
                        'marking' => 'SOME_MARK_2',
                    ],
                    [
                        'marking' => 'SOME_MARK_3',
                    ],
                    [
                        'marking' => 'SOME_MARK_4',
                    ],
                    [
                        'marking' => 'SOME_MARK_5',
                    ],
                    [
                        'marking' => 'SOME_MARK_6',
                    ],
                    [
                        'marking' => 'SOME_MARK_7',
                    ],
                    [
                        'marking' => 'SOME_MARK_8',
                    ],
                    [
                        'marking' => 'SOME_MARK_9',
                    ],
                    [
                        'marking' => 'SOME_MARK_10',
                    ],
                ],
            ],
            '#case 2. 3 элемента, 2 маркировки' => [
                [
                    [[], [], []], ['SOME_MARK_1', 'SOME_MARK_2'],
                ],
                [
                    [
                        'marking' => 'SOME_MARK_1',
                    ],
                    [
                        'marking' => 'SOME_MARK_2',
                    ],
                    [
                        'marking' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderItemsForPackItems
     * @param mixed $item
     * @param mixed $expectedArray
     */
    public function testPackItems($item, $expectedArray)
    {
        $discountHelper = $this->getDiscountHelperInstance();

        $dHelper = new \ReflectionClass($discountHelper);

        $getProcessedItem = $dHelper->getMethod('packItems');
        $getProcessedItem->setAccessible(true);

        $packed = $getProcessedItem->invokeArgs($discountHelper, $item);
        $this->assertEquals(array_keys($expectedArray), array_keys($packed), 'Packing of item failed');
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function dataProviderItemsForPackItems()
    {
        $final = [];

        $item1 = $this->getItem(0, 0, 0, 1);
        $item1->setData(DiscountHelperInterface::NAME_ROW_DIFF, 2);
        $item1->setData(DiscountHelperInterface::NAME_UNIT_PRICE, 10.59);

        $final['#case 1. qty = 1.'] = [
            [
                'item' => $item1,
                'items' => [[]],
            ],
            [
                $item1->getId() => [],
            ],
        ];

        $item2 = $this->getItem(0, 0, 0, 3);
        $item2->setData(DiscountHelperInterface::NAME_ROW_DIFF, 2);
        $item2->setData(DiscountHelperInterface::NAME_UNIT_PRICE, 10.59);

        $final['#case 2. qty = 3.'] = [
            [
                'item' => $item1,
                'items' => [[], [], []],
            ],
            [
                $item1->getId() . '_1' => [],
                $item1->getId() . '_2' => [],
                $item1->getId() . '_3' => [],
            ],
        ];

        return $final;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function dataProviderItemsForMarking()
    {
        $final = [];

        // #1 rowDiff = 2 kop. qty = 3. qtyUpdate = 3
        $item = $this->getItem(0, 0, 0, 3);
        $item->setData(DiscountHelperInterface::NAME_ROW_DIFF, 2);
        $item->setData(DiscountHelperInterface::NAME_UNIT_PRICE, 10.59);
        $item->setData(DiscountHelperInterface::NAME_MARKING, true);
        $item->setData(
            DiscountHelperInterface::NAME_MARKING_LIST,
            implode(
                ',',
                array_map(
                    'base64_encode',
                    ['SOME_MARK_1', 'SOME_MARK_2', 'SOME_MARK_3']
                )
            )
        );

        $expected = [
            [
                'price' => 10.59,
                'quantity' => 1,
                'sum' => 10.59,
                'tax' => null,
                'marking' => 'SOME_MARK_1',
            ],
            [
                'price' => 10.6,
                'quantity' => 1,
                'sum' => 10.6,
                'tax' => null,
                'marking' => 'SOME_MARK_2',
            ],
            [
                'price' => 10.6,
                'quantity' => 1,
                'sum' => 10.6,
                'tax' => null,
                'marking' => 'SOME_MARK_3',
            ],
        ];
        $final['#case 1. 2 копейки распределить по 3м товарам.'] = [$item, $expected];

        return $final;
    }
}
