<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Facade;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mygento\Base\Model\Recalculator\ResultFactory;
use Mygento\Base\Service\RecalculatorFacade;
use Mygento\Base\Test\Extra\DiscountHelperInterfaceFactory;
use Mygento\Base\Test\Extra\GetRecalculateResultFactory;
use PHPUnit\Framework\TestCase;

abstract class AbstractFacadeTest extends TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectMan;

    /**
     * @return \Mygento\Base\Service\RecalculatorFacade
     */
    abstract protected function getFacadeInstance(): RecalculatorFacade;

    /**
     * @return ResultFactory
     */
    protected function getRecalculateResultFactory(): ResultFactory
    {
        /** @var \Mygento\Base\Test\Extra\GetRecalculateResultFactory $recalculateResultFactory */
        $recalculateResultFactory = $this->getObjectManager()->getObject(
            GetRecalculateResultFactory::class
        );

        return $recalculateResultFactory->get($this);
    }

    /**
     * @return \Mygento\Base\Test\Extra\DiscountHelperInterfaceFactory
     */
    protected function getDiscountHelperFactory(): DiscountHelperInterfaceFactory
    {
        //Вместо моков нам нужны реальные объекты, которые участвуют в рассчете:
        return $this->getObjectManager()->getObject(
            DiscountHelperInterfaceFactory::class,
            ['objectManager' => $this->getObjectManager()]
        );
    }

    /**
     * @return \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected function getObjectManager(): ObjectManager
    {
        if (!$this->objectMan) {
            $this->objectMan = new ObjectManager(
                $this
            );
        }

        return $this->objectMan;
    }
}
