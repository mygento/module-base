<?php

namespace Mygento\Base\Test\Extra;

require_once (__DIR__ . '/../../vendor/generate/generated/code/Mygento/Base/Api/Data/RecalculateResultInterfaceFactory.php') ;
require_once (__DIR__ . '/../../vendor/generate/generated/code/Mygento/Base/Api/Data/RecalculateResultItemInterfaceFactory.php') ;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Mygento\Base\Model\Recalculator\ResultFactory;
use Mygento\Base\Test\Extra\RecalculateResultInterfaceFactory;
use Mygento\Base\Test\Extra\RecalculateResultItemInterfaceFactory;

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
            RecalculateResultItemInterfaceFactory ::class,
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