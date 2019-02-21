<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api;

interface ProductAttributeHelperInterface
{
    const CONFIG_PATH_DEFAULT_SUFFIX = '_default';

    /**
     * Fetch attribute code from $pathToParam and then get it from product.
     * Or returns default value ($pathToParam . _default) if $pathToParam is empty.
     * @param string $pathToParam config path like module/general/param
     * @param string|int $productId
     *
     * @return mixed attribute value
     */
    public function getValueByConfigPathOrDefault($pathToParam, $productId);

    /**
     * Returns attribute value or attribute text (for dropdown attributes)
     *
     * @param string $attributeCode
     * @param string|int|null $productId
     * @return mixed
     */
    public function getValue($attributeCode, $productId);
}
