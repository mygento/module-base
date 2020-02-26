<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper\Product;

use Mygento\Base\Api\ProductAttributeHelperInterface;

class Attribute implements ProductAttributeHelperInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productResource;

    /**
     * @var \Mygento\Base\Helper\Data
     */
    private $generalHelper;

    /**
     * @param \Mygento\Base\Helper\Data $generalHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Mygento\Base\Helper\Data $generalHelper,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->generalHelper = $generalHelper;
        $this->productResource = $productResource;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function getValueByConfigPathOrDefault(string $pathToParam, $productId)
    {
        $attributeCode = $this->generalHelper->getGlobalConfig($pathToParam);
        if (!$attributeCode || '0' === $attributeCode || 0 === $attributeCode) {
            return $this->generalHelper->getGlobalConfig(
                $pathToParam . self::CONFIG_PATH_DEFAULT_SUFFIX
            );
        }

        $value = $this->getAttrValue($attributeCode, $productId);
        if (!empty($value)) {
            return $value;
        }

        return $this->generalHelper->getGlobalConfig(
            $pathToParam . self::CONFIG_PATH_DEFAULT_SUFFIX
        );
    }

    /**
     * @inheritdoc
     */
    public function getAttrValue(string $attributeCode, $productId)
    {
        $attribute = $this->productResource->getAttribute($attributeCode);
        $store = $this->storeManager->getStore();

        $value = $this->productResource->getAttributeRawValue($productId, $attributeCode, $store);
        if (!$attribute->usesSource()) {
            return $value;
        }

        if (empty($value)) {
            return $value;
        }

        return $attribute->getSource()->getOptionText($value);
    }
}
