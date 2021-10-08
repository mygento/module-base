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
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return RecalculateResultInterface
     */
    public function handle(Order $order, RecalculateResultInterface $recalcOriginal): RecalculateResultInterface;
}
