<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model;

use Magento\Sales\Api\Data\OrderInterface;

class OrderRepository extends \Magento\Sales\Model\OrderRepository
{
    /**
     * Clear repository cache
     */
    public function clearCache(): void
    {
        $this->registry = [];
    }

    /**
     * @param int $orderId
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function reloadOrder($orderId): OrderInterface
    {
        $this->clearCache();

        return $this->get($orderId);
    }
}
