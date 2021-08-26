<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade;

use Mygento\Base\Api\Data\RecalculateResultItemInterface;
use Mygento\Base\Service\PostHandlers\AddChildrenOfBundle;
use Mygento\Base\Service\PostHandlers\AddExtraDiscounts;
use Mygento\Base\Service\PostHandlers\RestoreSkippedItems;
use Mygento\Base\Service\PreHandlers\SkipItems;
use Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemFixer;
use Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemsCollector;
use Mygento\Base\Service\RecalculatorFacade;
use Mygento\Base\Test\Extra\ExpectedMaker;
use Mygento\Base\Test\Extra\TestItemSkipper;

class AllHandlersTest extends AbstractFacadeTest
{
    /**
     * @dataProvider \Mygento\Base\Test\Unit\Facade\AllHandlersDataProvider::dataProvider
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

        foreach ($result->getItems() as $key => $recalcItem) {
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

        $testItemSkipper = $this->getObjectManager()->getObject(
            TestItemSkipper::class
        );

        $skippedItemsCollector = $this->getObjectManager()->getObject(
            SkippedItemsCollector::class,
            [
                'skippers' => [$testItemSkipper],
            ]
        );

        $skippedItemFixer = $this->getObjectManager()->getObject(
            SkippedItemFixer::class,
            [
                'discountHelperFactory' => $discountHelperFactory,
                'recalculateResultFactory' => $resultFactory,
            ]
        );

        $addRestoreSkipHandler = $this->getObjectManager()->getObject(
            RestoreSkippedItems::class,
            [
                'skippedItemsCollector' => $skippedItemsCollector,
                'skippedItemFixer' => $skippedItemFixer,
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
                    $addExtraDiscountsHandler,
                    $addChildrenOfBundleHandler,
                    $addRestoreSkipHandler,
                ],
            ]
        );
    }
}
