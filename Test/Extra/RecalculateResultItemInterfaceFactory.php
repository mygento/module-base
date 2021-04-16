<?php

namespace Mygento\Base\Test\Extra;

require_once (__DIR__ . '/../../vendor/generate/generated/code/Mygento/Base/Api/Data/RecalculateResultItemInterfaceFactory.php') ;

class RecalculateResultItemInterfaceFactory extends \Mygento\Base\Api\Data\RecalculateResultItemInterfaceFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\TestFramework\Unit\Helper\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Framework\TestFramework\Unit\Helper\ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Mygento\Base\Model\Recalculator\Result\Item
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->getObject(\Mygento\Base\Model\Recalculator\Result\Item::class, $data);
    }
}