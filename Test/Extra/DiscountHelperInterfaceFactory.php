<?php

namespace Mygento\Base\Test\Extra;

require_once (__DIR__ . '/../../vendor/generate/generated/code/Mygento/Base/Api/DiscountHelperInterfaceFactory.php') ;

/**
 * Factory class for @see \Mygento\Base\Api\DiscountHelperInterface
 */
class DiscountHelperInterfaceFactory extends \Mygento\Base\Api\DiscountHelperInterfaceFactory
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
     * @return \Mygento\Base\Api\DiscountHelperInterface
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->getObject(\Mygento\Base\Helper\Discount::class, $data);
    }
}