<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api;

use Magento\Sales\Api\Data\OrderInterface;

interface RecalculationPreHandlerInterface
{
    /**
     * @param OrderInterface $entity
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return OrderInterface
     */
    public function handle(OrderInterface $entity): OrderInterface;

    /**
     * @param OrderInterface $entity
     * @return bool
     */
    public function isShouldBeApplied(OrderInterface $entity): bool;
}
