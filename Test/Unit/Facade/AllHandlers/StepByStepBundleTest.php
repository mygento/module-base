<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade\AllHandlers;

use Magento\Bundle\Model\Product\Type;
use Magento\Sales\Api\Data\OrderInterface;
use Mygento\Base\Api\Data\RecalculateResultInterface;
use Mygento\Base\Api\Data\RecalculateResultItemInterface;
use Mygento\Base\Service\PostHandlers\AddChildrenOfBundle;
use Mygento\Base\Service\PostHandlers\AddExtraDiscounts;
// use Mygento\Base\Service\PostHandlers\RestoreSkippedItems;
use Mygento\Base\Service\PreHandlers\SkipItems;
use Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemsCollector;
use Mygento\Base\Service\RecalculatorFacade;
use Mygento\Base\Test\Extra\ExpectedMaker;
use Mygento\Base\Test\Extra\TableOutput;
use Mygento\Base\Test\Extra\TestItemSkipper;
use Mygento\Base\Test\Unit\Facade\AbstractFacadeTest;

class StepByStepBundleTest extends AbstractFacadeTest
{
    /**
     * @dataProvider \Mygento\Base\Test\Unit\Facade\AllHandlers\StepByStepBundleDataProvider::provide
     */
    public function testCalculation(OrderInterface $order, array $expectedData)
    {
        //TableOutput::dumpOrder($order, 'init');
        $facade = $this->getFacadeInstance();

        $expected = $expectedData['expected'];

        //Step 1. Vanilla
        $vanillaResult = $facade->execute($order);
        //TableOutput::dumpResult($vanillaResult, 'vanilla');

        if (!$expected) {
            ExpectedMaker::dump($vanillaResult);
        }

        $expectedItems = $expected['items'];

        self::assertEquals($expected['sum'], $vanillaResult->getSum(), 'Vanilla Total sum failed');
        foreach ($vanillaResult->getItems() as $recalcItem) {
            $expectedItem = array_shift($expectedItems);
            self::assertItem($expectedItem, $recalcItem);
        }

        //Step 2. Apply AddExtraDiscountsHandler
        $addExtraDiscountsHandler = $this->getAddExtraDiscountsHandler();
        $extraResult = $addExtraDiscountsHandler->handle($order, $vanillaResult);
        TableOutput::dumpResult($extraResult, '1. After AddExtraDiscountHandler');

        $expectedItems = $expected['items'];
        self::assertEquals($expected['sum'], $extraResult->getSum(), 'Extra Total sum failed');
        foreach ($extraResult->getItems() as $recalcItem) {
            $expectedItem = array_shift($expectedItems);
            self::assertItem($expectedItem, $recalcItem);
            self::assertGiftCard($expectedItem, $recalcItem);
        }
        $addChildrenOfBundleHandler = $this->getAddChildrenOfBundleHandler();

        //Step 3. Check virtual orders for bundle products
        $this->assertVirtualOrderForBundles($order, $expectedData, $extraResult);

        //Step 4. Apply AddChildrenOfBundleHandler
        $childrenResult = $addChildrenOfBundleHandler->handle($order, $extraResult);
        TableOutput::dumpResult($childrenResult, '3. With bundle children');
        $expectedItems = $expected['items'];
        foreach ($childrenResult->getItems() as $key => $recalcItem) {
            $expectedItem = array_shift($expectedItems);

            //Validate Parent Item after all handlers
            self::assertItem($expectedItem, $recalcItem);
            self::assertGiftCard($expectedItem, $recalcItem);

            //Validate Children Items after all handlers
            foreach ($recalcItem->getChildren() as $child) {
                self::assertArrayHasKey(RecalculateResultItemInterface::CHILDREN, $expectedItem);
                $expectedChild = array_shift($expectedItem[RecalculateResultItemInterface::CHILDREN]);

                self::assertItem($expectedChild, $child);
                self::assertGiftCard($expectedChild, $child);
            }
        }
    }

    /**
     * @return \Mygento\Base\Service\RecalculatorFacade
     */
    protected function getFacadeInstance(): RecalculatorFacade
    {
        $discountHelperFactory = $this->getDiscountHelperFactory();
        $resultFactory = $this->getRecalculateResultFactory();

        $testItemSkipper = $this->getObjectManager()->getObject(
            TestItemSkipper::class
        );

        $skippedItemsCollector = $this->getObjectManager()->getObject(
            SkippedItemsCollector::class,
            [
                'skippers' => [$testItemSkipper],
            ]
        );

        $skipItemsPreHandler = $this->getObjectManager()->getObject(
            SkipItems::class,
            [
                'skippedItemsCollector' => $skippedItemsCollector,
            ]
        );

        return $this->getObjectManager()->getObject(
            RecalculatorFacade::class,
            [
                'discountHelper' => $discountHelperFactory->create(),
                'recalculateResultFactory' => $resultFactory,
                'preHandlers' => [$skipItemsPreHandler],
                'postHandlers' => [],
            ]
        );
    }

    private function getAddExtraDiscountsHandler(): AddExtraDiscounts
    {
        return $this->getObjectManager()->getObject(
            AddExtraDiscounts::class,
            [
                'discountHelperFactory' => $this->getDiscountHelperFactory(),
            ]
        );
    }

    private function getAddChildrenOfBundleHandler(): AddChildrenOfBundle
    {
        return $this->getObjectManager()->getObject(
            AddChildrenOfBundle::class,
            [
                'discountHelperFactory' => $this->getDiscountHelperFactory(),
                'recalculateResultFactory' => $this->getRecalculateResultFactory(),
            ]
        );
    }

    private function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    private static function assertItem(array $expectedItem, RecalculateResultItemInterface $recalcItem): void
    {
        $name = $recalcItem->getName();

        self::assertEquals($expectedItem['price'], $recalcItem->getPrice(), $name . ' Price of item failed');
        self::assertEquals($expectedItem['quantity'], $recalcItem->getQuantity());
        self::assertEquals($expectedItem['sum'], $recalcItem->getSum(), $name . ' Sum of item failed');
    }

    private static function assertGiftCard(array $expectedItem, RecalculateResultItemInterface $recalcItem): void
    {
        if (!isset($expectedItem['gift_cards_amount'])) {
            return;
        }
        $name = $recalcItem->getName();
        $giftCardAmount = $recalcItem->getGiftCardAmount();

        self::assertEquals($expectedItem['gift_cards_amount'], $giftCardAmount, $name . ' GC of item failed');
    }

    private function assertVirtualOrderForBundles(
        OrderInterface $order,
        array $expectedData,
        RecalculateResultInterface $recalculateResult
    ): void {
        $addChildrenOfBundleHandler = $this->getAddChildrenOfBundleHandler();
        $orderItems = $order->getAllVisibleItems() ?? $order->getAllItems();
        foreach ($orderItems as $orderItem) {
            if ($orderItem->getProductType() !== Type::TYPE_CODE) {
                continue;
            }
            /** @var \Magento\Sales\Api\Data\OrderInterface $expectedVirtualOrder */
            $expectedVirtualOrder = array_shift($expectedData['virtual_order']);

            /** @var \Magento\Sales\Api\Data\OrderInterface $bundleOrder */
            $bundleOrder = $this->invokeMethod(
                $addChildrenOfBundleHandler,
                'getDummyOrderBasedOnBundle',
                [$orderItem, $recalculateResult]
            );

            if (!$expectedVirtualOrder) {
                ExpectedMaker::dump($bundleOrder);
            }

            TableOutput::dumpOrder($bundleOrder, '2. Based on Bundle Virtual');

            self::assertEquals(
                $expectedVirtualOrder->getSubtotalInclTax(),
                $bundleOrder->getSubtotalInclTax(),
                'Virtual order: SubtotalInclTax failed'
            );
            self::assertEquals(
                $expectedVirtualOrder->getSubtotal(),
                $bundleOrder->getSubtotal(),
                'Virtual order: Subtotal failed'
            );
            self::assertEquals(
                $expectedVirtualOrder->getGrandTotal(),
                $bundleOrder->getGrandTotal(),
                'Virtual order: GrandTotal failed'
            );

            $expectedVirtualItems = $expectedVirtualOrder->getAllVisibleItems() ?? $expectedVirtualOrder->getAllItems();
            foreach ($bundleOrder->getAllVisibleItems() ?? $bundleOrder->getAllItems() as $item) {
                $expectedItem = array_shift($expectedVirtualItems);
                $name = $item->getName();

                self::assertEquals(
                    $expectedItem->getPriceInclTax(),
                    $item->getPriceInclTax(),
                    $name . ' Price of virtual item failed'
                );
                self::assertEquals(
                    $expectedItem->getRowTotalInclTax(),
                    $item->getRowTotalInclTax(),
                    $name . ' RowTotalInclTax of virtual item failed'
                );
                self::assertEquals(
                    $expectedItem->getQty(),
                    $item->getQty(),
                    $name . ' Qty of virtual item failed'
                );
                self::assertEquals(
                    $expectedItem->getTaxAmount(),
                    $item->getTaxAmount(),
                    $name . ' TaxAmount of virtual item failed'
                );
                self::assertEquals(
                    $expectedItem->getDiscountAmount(),
                    $item->getDiscountAmount(),
                    $name . ' DiscountAmount of virtual item failed'
                );
            }
        }
    }
}
