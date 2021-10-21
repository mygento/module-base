<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api;

use Magento\Sales\Api\Data\OrderInterface as Order;
use Mygento\Base\Api\Data\RecalculateResultInterface;

interface RecalculationPostHandlerInterface
{
    /**
     * @param Order $order
     * @param RecalculateResultInterface $recalcOriginal
     * @param string $taxValue
     * @param string $taxAttributeCode
     * @param string $shippingTaxValue
     * @param string $markingAttributeCode
     * @param string $markingListAttributeCode
     * @param string $markingRefundAttributeCode
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return RecalculateResultInterface
     */
    public function handle(
        Order $order,
        RecalculateResultInterface $recalcOriginal,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = '',
        $markingAttributeCode = '',
        $markingListAttributeCode = '',
        $markingRefundAttributeCode = ''
    ): RecalculateResultInterface;
}
