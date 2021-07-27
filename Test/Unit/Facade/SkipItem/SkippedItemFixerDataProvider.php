<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade\SkipItem;

use Mygento\Base\Test\OrderMockBuilder;

class SkippedItemFixerDataProvider
{
    /**
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public static function dataProviderItemsToSkip(): array
    {
        $final = [];

        //Simple, который содержит структурную скидку и делится нацело
        //Скидка применена ДО начисления налога.
        $item = OrderMockBuilder::getItem(120.00, 120.00, 50, 1)
            ->setRowTotal(100.00)
            ->setTaxPercent(20.00)
            ->setTaxAmount(10.00);

        $expected = [
            'price' => 60.0,
            'quantity' => 1.0,
            'sum' => 60.0,
        ];

        $final['1. Содержит скидку. Скидка применена до расчета налогов.'] = [$item, $expected];

        //Simple, который содержит структурную скидку и НЕ делится нацело
        $item = OrderMockBuilder::getItem(300.00, 100.00, 10, 3)
            ->setRowTotal(300.00);

        $expected = [
            'price' => 96.66,
            'quantity' => 3.0,
            'sum' => 289.98,
        ];

        $final['2. Содержит скидку. Не делится нацело.'] = [$item, $expected];

        $item = OrderMockBuilder::getItem(2170.00, 2170.0000, 217.0000, 1)
            ->setRowTotal(1808.33)
            ->setTaxPercent(20.00)
            ->setTaxAmount(361.67);
        $expected = [
            'price' => 1953.00,
            'quantity' => 1.0,
            'sum' => 1953.00,
        ];

        $final['3. Делится нацело. Баг с некорректным расчетом налога на скидку.'] = [$item, $expected];

        return $final;
    }
}
