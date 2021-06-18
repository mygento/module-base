<?php

namespace Mygento\Base\Service\PreHandlers\SkipItems;

use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Mygento\Base\Api\Data\RecalculateResultItemInterface;

class SkippedItemFixer
{
    /**
     * @param OrderItemInterface|InvoiceItemInterface|CreditmemoItemInterface $item
     * @throws \Exception
     * @return RecalculateResultItemInterface
     */
    public function execute($item): RecalculateResultItemInterface
    {

    }
}
