<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Extra;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mygento\Base\Model\Recalculator\ResultFactory;

class GetRecalculateResultFactory
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectMan;

    /**
     * @param $testCase
     * @return ResultFactory
     */
    public function get($testCase): ResultFactory
    {
        /** @var \Mygento\Base\Test\Extra\RecalculateResultInterfaceFactory $recalculateResultFactory */
        $recalculateResultFactory = $this->getObjectManager($testCase)->getObject(
            RecalculateResultInterfaceFactory::class,
            ['objectManager' => $this->objectMan]
        );

        /** @var \Mygento\Base\Test\Extra\RecalculateResultItemInterfaceFactory $recalculateResultItemFactory */
        $recalculateResultItemFactory = $this->getObjectManager($testCase)->getObject(
            RecalculateResultItemInterfaceFactory::class,
            ['objectManager' => $this->objectMan]
        );

        return $this->getObjectManager($testCase)->getObject(
            ResultFactory::class,
            [
                'resultInterfaceFactory' => $recalculateResultFactory,
                'itemInterfaceFactory' => $recalculateResultItemFactory,
            ]
        );
    }

    /**
     * @param $testCase
     * @return \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private function getObjectManager($testCase): ObjectManager
    {
        if (!$this->objectMan) {
            $this->objectMan = new ObjectManager(
                $testCase
            );
        }

        return $this->objectMan;
    }
}
