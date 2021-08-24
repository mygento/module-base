<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade\AllHandlers;

use Magento\Bundle\Model\Product\Type;
use Mygento\Base\Api\Data\RecalculateResultItemInterface;
use Mygento\Base\Service\PostHandlers\AddChildrenOfBundle;
use Mygento\Base\Service\PostHandlers\AddExtraDiscounts;
// use Mygento\Base\Service\PostHandlers\RestoreSkippedItems;
use Mygento\Base\Service\PreHandlers\SkipItems;
use Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemsCollector;
use Mygento\Base\Service\RecalculatorFacade;
use Mygento\Base\Test\Extra\ExpectedMaker;
use Mygento\Base\Test\Extra\Table;
use Mygento\Base\Test\Extra\TestItemSkipper;
use Mygento\Base\Test\Unit\Facade\AbstractFacadeTest;

class BundleTest extends AbstractFacadeTest
{
    /**
     * @dataProvider \Mygento\Base\Test\Unit\Facade\AllHandlers\BundleDataProvider::provide
     * @param mixed $order
     * @param mixed $expected
     */
    public function testCalculation($order, $expected)
    {
        //Table::dumpOrder($order, 'init');
        $facade = $this->getFacadeInstance();

        $vanillaResult = $facade->execute($order);
        //Table::dumpResult($vanillaResult, 'vanilla');

        if (!$expected) {
            ExpectedMaker::dump($vanillaResult);
        }

        $expectedItems = $expected['items'];

        self::assertEquals($expected['sum'], $vanillaResult->getSum(), 'Vanilla Total sum failed');
        foreach ($vanillaResult->getItems() as $key => $recalcItem) {
            $expectedItem = array_shift($expectedItems);
            self::assertEquals($expectedItem['price'], $recalcItem->getPrice(), $key . ' Price of item failed');
            self::assertEquals($expectedItem['quantity'], $recalcItem->getQuantity());
            self::assertEquals($expectedItem['sum'], $recalcItem->getSum(), $key . ' Sum of item failed');
        }

        /** @var AddExtraDiscounts $addExtraDiscountsHandler */
        $addExtraDiscountsHandler = $this->getObjectManager()->getObject(
            AddExtraDiscounts::class,
            [
                'discountHelperFactory' => $this->getDiscountHelperFactory(),
            ]
        );

        $extraResult = $addExtraDiscountsHandler->handle($order, $vanillaResult);
        Table::dumpResult($extraResult, 'Extra');

        $expectedItems = $expected['items'];
        self::assertEquals($expected['sum'], $extraResult->getSum(), 'Extra Total sum failed');
        foreach ($extraResult->getItems() as $key => $recalcItem) {
            $expectedItem = array_shift($expectedItems);
            self::assertEquals($expectedItem['price'], $recalcItem->getPrice(), $key . ' Price of item failed');
            self::assertEquals($expectedItem['quantity'], $recalcItem->getQuantity());
            self::assertEquals($expectedItem['sum'], $recalcItem->getSum(), $key . ' Sum of item failed');
            if (isset($expectedItem['gift_cards_amount'])) {
                self::assertEquals($expectedItem['gift_cards_amount'], $recalcItem->getGiftCardAmount(), $key . ' GC of item failed');
            }
        }

        /** @var AddChildrenOfBundle $addChildrenOfBundleHandler */
        $addChildrenOfBundleHandler = $this->getObjectManager()->getObject(
            AddChildrenOfBundle::class,
            [
                'discountHelperFactory' => $this->getDiscountHelperFactory(),
                'recalculateResultFactory' => $this->getRecalculateResultFactory(),
            ]
        );

        $orderItems = $order->getAllVisibleItems() ?? $order->getAllItems();
        foreach ($orderItems as $orderItem) {
            if ($orderItem->getProductType() !== Type::TYPE_CODE) {
                continue;
            }
            $bundleOrder = $this->invokeMethod($addChildrenOfBundleHandler, 'getDummyOrderBasedOnBundle', [$orderItem, $extraResult]);
            Table::dumpOrder($bundleOrder, 'child');
        }

        $childrenResult = $addChildrenOfBundleHandler->handle($order, $extraResult);
        Table::dumpResult($childrenResult, 'Child');
        $expectedItems = $expected['items'];
        foreach ($childrenResult->getItems() as $key => $recalcItem) {
            $expectedItem = array_shift($expectedItems);
            self::assertEquals($expectedItem['price'], $recalcItem->getPrice(), $key . ' Price of item failed');
            self::assertEquals($expectedItem['quantity'], $recalcItem->getQuantity());
            self::assertEquals($expectedItem['sum'], $recalcItem->getSum(), $key . ' Sum of item failed');
            if (isset($expectedItem['gift_cards_amount'])) {
                self::assertEquals($expectedItem['gift_cards_amount'], $recalcItem->getGiftCardAmount(), $key . ' GC of item failed');
            }

            foreach ($recalcItem->getChildren() as $child) {
                self::assertArrayHasKey(RecalculateResultItemInterface::CHILDREN, $expectedItem);
                $expectedChild = array_shift($expectedItem[RecalculateResultItemInterface::CHILDREN]);

                self::assertEquals($expectedChild['price'], $child->getPrice(), $key . ' Price of item failed');
                self::assertEquals($expectedChild['quantity'], $child->getQuantity());
                self::assertEquals($expectedChild['sum'], $child->getSum(), $key . ' Sum of item failed');
                if (isset($expectedChild['gift_cards_amount'])) {
                    self::assertEquals($expectedChild['gift_cards_amount'], $child->getGiftCardAmount(), $key . ' GC of child item failed');
                }
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

        $addChildrenOfBundleHandler = $this->getObjectManager()->getObject(
            AddChildrenOfBundle::class,
            [
                'discountHelperFactory' => $discountHelperFactory,
                'recalculateResultFactory' => $resultFactory,
            ]
        );
        $addExtraDiscountsHandler = $this->getObjectManager()->getObject(
            AddExtraDiscounts::class,
            [
                'discountHelperFactory' => $discountHelperFactory,
            ]
        );
//        $addRestoreSkipHandler = $this->getObjectManager()->getObject(
//            RestoreSkippedItems::class,
//            [
//                'skippedItemsCollector' => $discountHelperFactory,
//                'skippedItemFixer' => $discountHelperFactory,
//            ]
//        );

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
                'postHandlers' => [
                    //$addExtraDiscountsHandler,
                    //$addChildrenOfBundleHandler,
                    //$addRestoreSkipHandler
                ],
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
}
