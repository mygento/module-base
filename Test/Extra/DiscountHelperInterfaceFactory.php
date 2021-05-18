<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Extra;

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
