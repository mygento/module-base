<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mygento\Base\Api\Data\RecalculateResultItemInterface;
use Mygento\Base\Model\Recalculator\ResultFactory;
use Mygento\Base\Service\Handlers\AddChildrenOfBundle;
use Mygento\Base\Service\RecalculatorFacade;
use Mygento\Base\Test\Extra\DiscountHelperInterfaceFactory;
use PHPUnit\Framework\TestCase;

class AddChildrenOfBundleHandlerTest extends TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectMan;

    /**
     * @dataProvider \Mygento\Base\Test\Unit\Facade\BundlesDataProvider::dataProviderBundles
     * @param mixed $order
     * @param mixed $expected
     * @throws \Exception
     */
    public function testCalculation($order, $expected)
    {
        $facade = $this->getFacadeInstance();

        $result = $facade->execute($order);

        if (!$expected) {
            $this->dumpExpected($result);
        }

        self::assertEquals($result->getSum(), $expected['sum'], 'Total sum failed');

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
     * @param \Mygento\Base\Api\Data\RecalculateResultInterface $recalcOriginal
     */
    protected function dumpExpected($recalcOriginal)
    {
        $items = [];
        foreach ($recalcOriginal->getItems() as $itemId => $item) {
            $itemArray = $item->toArray();
            foreach ($item->getChildren() as $key => $child) {
                $itemArray['children'][$key] = $child->toArray();
            }

            $items[$itemId] = $itemArray;
        }
        $recalcOriginal->setItems($items);

        echo "\033[1;33m"; // yellow
        $storedValue = ini_get('serialize_precision');
        ini_set('serialize_precision', 12);
        var_export($recalcOriginal->toArray());
        ini_set('serialize_precision', $storedValue);
        echo "\033[0m"; // reset color
        exit();
    }

    /**
     * @return \Mygento\Base\Service\RecalculatorFacade
     */
    public function getFacadeInstance()
    {
        //Вместо моков нам нужны реальные объекты, которые участвуют в рассчете:
        /** @var \Mygento\Base\Test\Extra\DiscountHelperInterfaceFactory $discountHelperFactory */
        $discountHelperFactory = $this->getObjectManager()->getObject(
            DiscountHelperInterfaceFactory::class,
            ['objectManager' => $this->objectMan]
        );

        $discountHelper = $discountHelperFactory->create();
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
                'discountHelper' => $discountHelper,
                'recalculateResultFactory' => $resultFactory,
                'handlers' => [$addChildrenOfBundleHandler]
            ]
        );
    }

    /**
     * @return \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        if (!$this->objectMan) {
            $this->objectMan = new ObjectManager(
                $this
            );
        }

        return $this->objectMan;
    }

    /**
     * @return ResultFactory
     */
    private function getRecalculateResultFactory(): ResultFactory
    {
        /** @var \Mygento\Base\Test\Extra\GetRecalculateResultFactory $recalculateResultFactory */
        $recalculateResultFactory = $this->getObjectManager()->getObject(
            \Mygento\Base\Test\Extra\GetRecalculateResultFactory::class
        );

        return $recalculateResultFactory->get($this);
    }
}
