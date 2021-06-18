<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service\PostHandlers;

use Magento\Sales\Api\Data\OrderInterface as Order;
use Mygento\Base\Api\Data\RecalculateResultInterface;
use Mygento\Base\Api\RecalculationPostHandlerInterface;

class RestoreSkippedItems implements RecalculationPostHandlerInterface
{
    /**
     * @param Order $order
     * @param RecalculateResultInterface|null $recalcOriginal
     * @throws \Exception
     * @return RecalculateResultInterface
     */
    public function handle(Order $order, RecalculateResultInterface $recalcOriginal): RecalculateResultInterface
    {
        /* TODO: Implement it */

        return $recalcOriginal;
    }
}
