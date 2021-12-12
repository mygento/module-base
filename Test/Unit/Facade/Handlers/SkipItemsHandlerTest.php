<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade\Handlers;

use Magento\Sales\Api\Data\OrderInterface;
use Mygento\Base\Service\PreHandlers\SkipItems;
use Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemsCollector;
use Mygento\Base\Service\RecalculatorFacade;
use Mygento\Base\Test\Extra\ExpectedMaker;
use Mygento\Base\Test\Extra\TableOutput;
use Mygento\Base\Test\Extra\TestItemSkipper;
use Mygento\Base\Test\Unit\Facade\AbstractFacadeTest;

class SkipItemsHandlerTest extends AbstractFacadeTest
{
    /**
     * @dataProvider \Mygento\Base\Test\Unit\Facade\Handlers\DataProvider\SkipItemsDataProvider::dataProvider()
     * @param mixed $order
     * @param mixed $expected
     * @throws \Exception
     */
    public function testCalculation($order, $expected)
    {
        $facade = $this->getFacadeInstance();

        $result = $facade->execute($order);

        if (!$expected) {
            ExpectedMaker::dump($result);
        }

        self::assertEquals($expected['sum'], $result->getSum(), 'Total sum failed');

        $expectedItems = $expected['items'];

        foreach ($result->getItems() as $recalcItem) {
            $expectedItem = array_shift($expectedItems);

            self::assertEquals($expectedItem['price'], $recalcItem->getPrice(), 'Price of item failed');
            self::assertEquals($expectedItem['quantity'], $recalcItem->getQuantity());
            self::assertEquals($expectedItem['sum'], $recalcItem->getSum(), 'Sum of item failed');
        }
    }

    /**
     * @dataProvider \Mygento\Base\Test\Unit\Facade\Handlers\DataProvider\SkipItemsDataProvider::dataProviderForVirtualOrder()
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param mixed $order
     */
    public function testVirtualOrder($order, array $expected, OrderInterface $expectedVirtualOrder)
    {
        $skipItemsHandler = $this->getSkipItemsHandler();
        $virtualOrder = $skipItemsHandler->handle($order);

        TableOutput::dumpOrder($order, '1. Before Skipping items');
        TableOutput::dumpOrder($virtualOrder, '2. After Skipping items');

        self::assertNotNull($virtualOrder->getId(), 'Order ID is not set.');
        self::assertEquals($expectedVirtualOrder->getId(), $virtualOrder->getId(), 'Order ID is not equal.');

        $itemsCountOrig = count($order->getItems());
        $itemsCountAfterSkipping = count($virtualOrder->getItems());

        self::assertNotEquals($itemsCountOrig, $itemsCountAfterSkipping, 'Items were not skipped.');

        self::assertEquals(
            $expectedVirtualOrder->getSubtotalInclTax(),
            $virtualOrder->getSubtotalInclTax(),
            'Virtual order: SubtotalInclTax failed'
        );
        self::assertEquals(
            $expectedVirtualOrder->getGrandTotal(),
            $virtualOrder->getGrandTotal(),
            'Virtual order: GrandTotal failed'
        );
        self::assertEquals(
            $expectedVirtualOrder->getDiscountAmount(),
            $virtualOrder->getDiscountAmount(),
            'Virtual order: Discount Amount failed'
        );
    }

    /**
     * @return \Mygento\Base\Service\RecalculatorFacade
     */
    protected function getFacadeInstance(): RecalculatorFacade
    {
        $discountHelperFactory = $this->getDiscountHelperFactory();
        $skipItemsPreHandler = $this->getSkipItemsHandler();

        return $this->getObjectManager()->getObject(
            RecalculatorFacade::class,
            [
                'discountHelper' => $discountHelperFactory->create(),
                'recalculateResultFactory' => $this->getRecalculateResultFactory(),
                'preHandlers' => [$skipItemsPreHandler],
            ]
        );
    }

    protected function getSkipItemsHandler(): SkipItems
    {
        $testItemSkipper = $this->getObjectManager()->getObject(
            TestItemSkipper::class
        );

        $skippedItemsCollector = $this->getObjectManager()->getObject(
            SkippedItemsCollector::class,
            [
                'skippers' => [$testItemSkipper],
            ]
        );

        return $this->getObjectManager()->getObject(
            SkipItems::class,
            [
                'skippedItemsCollector' => $skippedItemsCollector,
            ]
        );
    }
}
