<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service\PreHandlers\SkipItems;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;

class SkippedItemsCollector
{
    /**
     * @var \Mygento\Base\Api\ItemSkipper[]
     */
    private $skippers;

    /**
     * @param \Mygento\Base\Api\ItemSkipper[] $skippers
     */
    public function __construct(array $skippers = [])
    {
        $this->skippers = $skippers;
    }

    /**
     * @param OrderInterface|InvoiceInterface|CreditmemoInterface $entity
     * @return array
     */
    public function getItemsToSkip($entity): array
    {
        $items = $entity->getAllVisibleItems() ?? $entity->getAllItems();
        $itemsToSkip = [];

        if (empty($this->skippers)) {
            return $itemsToSkip;
        }

        foreach ($this->skippers as $skipper) {
            foreach ($items as $item) {
                if ($skipper->isShouldBeSkipped($item)) {
                    continue;
                }
                $itemsToSkip[] = $item;
            }
        }

        return $itemsToSkip;
    }
}
