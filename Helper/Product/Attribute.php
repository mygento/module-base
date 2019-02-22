<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper\Product;

use Mygento\Base\Api\ProductAttributeHelperInterface;

/**
 * Class Attribute helps to fetch easier Product Attributes
 * @package Mygento\Base\Helper\Product
 */
class Attribute implements ProductAttributeHelperInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;
    /**
     * @var \Mygento\Base\Helper\Data
     */
    private $generalHelper;

    /**
     * Attribute constructor.
     * @param \Mygento\Base\Helper\Data $generalHelper
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Mygento\Base\Helper\Data $generalHelper,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
        $this->generalHelper = $generalHelper;
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getValueByConfigPathOrDefault(string $pathToParam, $productId)
    {
        $attributeCode = $this->generalHelper->getConfig($pathToParam);
        if (!$attributeCode || '0' === $attributeCode || 0 === $attributeCode) {
            return $this->generalHelper->getConfig(
                $pathToParam . self::CONFIG_PATH_DEFAULT_SUFFIX
            );
        }

        return $this->getValue($attributeCode, $productId);
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @psalm-suppress UndefinedMethod
     */
    public function getValue(string $attributeCode, $productId)
    {
        $product = $this->getProduct($productId);

        $attrText = $product->getAttributeText($attributeCode);
        if ($attrText) {
            return $attrText;
        }

        return $product->getData($attributeCode);
    }

    /**
     * @param string|int|null $productId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    private function getProduct($productId)
    {
        return $this->productRepository->getById((int)$productId);
    }
}
