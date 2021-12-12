<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade\Handlers\SkipItem;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mygento\Base\Model\Recalculator\ResultFactory;
use Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemFixer;
use Mygento\Base\Test\Extra\DiscountHelperInterfaceFactory;
use Mygento\Base\Test\Extra\ExpectedMaker;
use Mygento\Base\Test\Extra\GetRecalculateResultFactory;
use PHPUnit\Framework\TestCase;

class SkippedItemFixerTest extends TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectMan;

    /**
     * @dataProvider \Mygento\Base\Test\Unit\Facade\Handlers\SkipItem\SkippedItemFixerDataProvider::dataProviderItemsToSkip
     * @param mixed $item
     * @param mixed $expected
     * @throws \Exception
     */
    public function testCalculation($item, $expected)
    {
        $fixer = $this->getFixerInstance();

        $itemResult = $fixer->execute($item);

        if (!$expected) {
            ExpectedMaker::dump($itemResult);
        }

        self::assertEquals($expected['sum'], $itemResult->getSum(), 'Sum failed');
        self::assertEquals($expected['price'], $itemResult->getPrice(), 'Price failed');
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
     * @return \Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemFixer
     */
    private function getFixerInstance()
    {
        /** @var \Mygento\Base\Test\Extra\DiscountHelperInterfaceFactory $discountHelperFactory */
        $discountHelperFactory = $this->getObjectManager()->getObject(
            DiscountHelperInterfaceFactory::class,
            ['objectManager' => $this->objectMan]
        );

        $resultFactory = $this->getRecalculateResultFactory();

        return $this->getObjectManager()->getObject(
            SkippedItemFixer::class,
            [
                'discountHelperFactory' => $discountHelperFactory,
                'recalculateResultFactory' => $resultFactory,
            ]
        );
    }

    /**
     * @return ResultFactory
     */
    private function getRecalculateResultFactory(): ResultFactory
    {
        /** @var \Mygento\Base\Test\Extra\GetRecalculateResultFactory $recalculateResultFactory */
        $recalculateResultFactory = $this->getObjectManager()->getObject(
            GetRecalculateResultFactory::class
        );

        return $recalculateResultFactory->get($this);
    }
}
