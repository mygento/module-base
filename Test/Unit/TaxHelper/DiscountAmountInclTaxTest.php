<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\TaxHelper;

use Mygento\Base\Helper\Discount\Tax;
use Mygento\Base\Model\Mock\OrderMockBuilder;
use Mygento\Base\Test\Extra\ExpectedMaker;
use PHPUnit\Framework\TestCase;

class DiscountAmountInclTaxTest extends TestCase
{
    // consts for getDiscountAmountInclTax() method
    public const TEST_CASE_NAME_1 = '#case 1. Простой товар. НДС 20%. Дисконт 15% применен к цене с НДС.';
    public const TEST_CASE_NAME_2 = '#case 2. Бандл с динамической ценой. НДС 20%. Дисконт 15% применен к цене с НДС.';

    /**
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @dataProvider dataProviderOrdersForCheckCalculation
     * @param mixed $orderItem
     * @param mixed $expectedArray
     */
    public function testCalculation($orderItem, $expectedArray)
    {
        $this->assertTrue(method_exists(Tax::class, 'getDiscountAmountInclTax'));
        $result = Tax::getDiscountAmountInclTax($orderItem);

        if (is_null($expectedArray)) {
            ExpectedMaker::dump($result);
        }

        $this->assertEquals($result, $expectedArray, 'Invalid result');
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function dataProviderOrdersForCheckCalculation()
    {
        $final = [];

        //Тест кейсы одинаковые для всех вариантов настроек класса Discount
        $orderItems = self::getOrderItems();
        //А ожидаемые результаты должны быть в каждом классе свои
        $expected = static::getExpected();

        foreach ($orderItems as $key => $orderItem) {
            $final[$key] = [$orderItem, $expected[$key] ?? null];
        }

        return $final;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function getOrderItems()
    {
        $final = [];

        $final[self::TEST_CASE_NAME_1] = OrderMockBuilder::getItem(54.0000, 54.0000, 8.1000, 1, 20.0000, 7.6500, 45.0000)
            ->setProductType('simple')
            ->setDiscountPercent(15.0)
            ->setDiscountTaxCompensationAmount(1.35);

        $final[self::TEST_CASE_NAME_2] = OrderMockBuilder::getItem(73.2000, 73.2000, 0.0000, 1, null, 10.3700, 61.0000)
            ->setProductType('bundle')
            ->setDiscountPercent(15.0)
            ->setChildrenItems([
                OrderMockBuilder::getItem(22.8000, 22.8000, 3.4200, 1, 20.0000, 3.2300, 19.0000)
                    ->setProductType('simple')
                    ->setDiscountTaxCompensationAmount(0.5700),
                OrderMockBuilder::getItem(16.8000, 16.8000, 2.5200, 1, 20.0000, 2.3800, 14.0000)
                    ->setProductType('simple')
                    ->setDiscountTaxCompensationAmount(0.4200),
                OrderMockBuilder::getItem(6.0000, 6.0000, 0.9000, 1, 20.0000, 0.8500, 5.0000)
                    ->setProductType('simple')
                    ->setDiscountTaxCompensationAmount(0.1500),
                OrderMockBuilder::getItem(27.6000, 27.6000, 4.1400, 1, 20.0000, 3.9100, 23.0000)
                    ->setProductType('simple')
                    ->setDiscountTaxCompensationAmount(0.6900),
            ]);

        return $final;
    }

    /**
     * getExpected description
     */
    protected static function getExpected()
    {
        return [
            self::TEST_CASE_NAME_1 => 8.10,
            self::TEST_CASE_NAME_2 => 10.98,
        ];
    }

    protected function onNotSuccessfulTest(\Throwable $e): void
    {
        //beautify output
        echo "\033[1;31m"; // light red
        echo "\t" . $e->getMessage() . "\n";
        echo "\033[0m"; //reset color

        throw $e;
    }
}
