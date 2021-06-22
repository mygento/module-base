<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service\PreHandlers\SkipItems;

use Magento\Sales\Api\Data\OrderInterface;

class SkippedItemsCollector
{
    /** @var \Mygento\Base\Api\ItemSkipper[] */
    private $skippers;

    /** @var array */
    private $skippedItems = [];

    /**
     * @param \Mygento\Base\Api\ItemSkipper[] $skippers
     */
    public function __construct(array $skippers = [])
    {
        $this->skippers = $skippers;
    }

    /**
     * @param OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderItemInterface[]
     */
    public function getItemsToSkip(OrderInterface $order): array
    {
        if (empty($this->skippers)) {
            return [];
        }

        if (isset($this->skippedItems[$order->getEntityId()])) {
            return $this->skippedItems[$order->getEntityId()];
        }

        $this->skippedItems[$order->getEntityId()] = [];
        foreach ($this->skippers as $skipper) {
            foreach ($order->getAllVisibleItems() as $item) {
                if ($skipper->isShouldBeSkipped($item)) {
                    $this->skippedItems[$order->getEntityId()][] = $item;
                }
            }
        }

        return $this->skippedItems[$order->getEntityId()];
    }

    /**
     * @param OrderInterface $order
     * @return int[]
     */
    public function getItemIdsToSkip(OrderInterface $order): array
    {
        $items = $this->getItemsToSkip($order);

        return array_map(
            static function ($item) {
                return (int) $item->getId();
            },
            $items
        );
    }
}
