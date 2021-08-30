<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade\Handlers;

use Mygento\Base\Api\Data\RecalculateResultItemInterface;
use Mygento\Base\Service\PostHandlers\AddChildrenOfBundle;
use Mygento\Base\Service\RecalculatorFacade;
use Mygento\Base\Test\Extra\ExpectedMaker;
use Mygento\Base\Test\Extra\TableOutput;
use Mygento\Base\Test\Unit\Facade\AbstractFacadeTest;

class AddChildrenOfBundleHandlerTest extends AbstractFacadeTest
{
    /**
     * @dataProvider \Mygento\Base\Test\Unit\Facade\Handlers\DataProvider\BundlesDataProvider::dataProviderBundles
     * @param mixed $order
     * @param mixed $expected
     * @throws \Exception
     */
    public function testCalculation($order, $expected)
    {
        $facade = $this->getFacadeInstance();

        TableOutput::dumpOrder($order);

        $result = $facade->execute($order);

        TableOutput::dumpResult($result);

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

            foreach ($recalcItem->getChildren() as $child) {
                self::assertArrayHasKey(RecalculateResultItemInterface::CHILDREN, $expectedItem);
                $expectedChild = array_shift($expectedItem[RecalculateResultItemInterface::CHILDREN]);

                self::assertEquals($expectedChild['price'], $child->getPrice(), 'Price of item failed');
                self::assertEquals($expectedChild['quantity'], $child->getQuantity());
                self::assertEquals($expectedChild['sum'], $child->getSum(), 'Sum of item failed');
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

        return $this->getObjectManager()->getObject(
            RecalculatorFacade::class,
            [
                'discountHelper' => $discountHelperFactory->create(),
                'recalculateResultFactory' => $resultFactory,
                'postHandlers' => [$addChildrenOfBundleHandler],
            ]
        );
    }
}
