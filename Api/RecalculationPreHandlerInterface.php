<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;

interface RecalculationPreHandlerInterface
{
    /**
     * @param OrderInterface|InvoiceInterface|CreditmemoInterface $entity
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return OrderInterface|InvoiceInterface|CreditmemoInterface
     */
    public function handle($entity);

    /**
     * @param OrderInterface|InvoiceInterface|CreditmemoInterface $entity
     * @return bool
     */
    public function isShouldBeApplied($entity): bool;
}
