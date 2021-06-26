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
use Mygento\Base\Service\PostHandlers\AddExtraDiscounts;
use Mygento\Base\Service\RecalculatorFacade;
use Mygento\Base\Test\Extra\DiscountHelperInterfaceFactory;
use Mygento\Base\Test\Extra\ExpectedMaker;
use PHPUnit\Framework\TestCase;

class AddExtraDiscountsHandlerTest extends TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectMan;

    /**
     * @dataProvider \Mygento\Base\Test\Unit\Facade\ExtraDiscountsDataProvider::dataProvider
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
     * @dataProvider \Mygento\Base\Test\Unit\Facade\ExtraDiscountsDataProvider::dataProviderDivisionByZero
     * @param mixed $order
     * @param array|\Exception $expected
     * @throws \Exception
     */
    public function testDivisionByZero($order, $expected)
    {
        $facade = $this->getFacadeInstance();

        if ($expected instanceof \Exception) {
            $this->expectExceptionObject($expected);
            $facade->execute($order);

            return;
        }

        $this->testCalculation($order, $expected);
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

        $addExtraDiscountsHandler = $this->getObjectManager()->getObject(
            AddExtraDiscounts::class,
            [
                'discountHelperFactory' => $discountHelperFactory,
            ]
        );

        return $this->getObjectManager()->getObject(
            RecalculatorFacade::class,
            [
                'discountHelper' => $discountHelper,
                'recalculateResultFactory' => $resultFactory,
                'postHandlers' => [$addExtraDiscountsHandler],
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
