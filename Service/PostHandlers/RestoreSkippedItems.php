<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Service\PostHandlers;

use Magento\Sales\Api\Data\OrderInterface;
use Mygento\Base\Api\Data\RecalculateResultInterface;
use Mygento\Base\Api\RecalculationPostHandlerInterface;
use Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemFixer;
use Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemsCollector;

class RestoreSkippedItems implements RecalculationPostHandlerInterface
{
    /**
     * @var \Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemFixer
     */
    private $skippedItemFixer;

    /**
     * @var \Mygento\Base\Service\PreHandlers\SkipItems\SkippedItemsCollector
     */
    private $skippedItemsCollector;

    public function __construct(
        SkippedItemsCollector $skippedItemsCollector,
        SkippedItemFixer $skippedItemFixer
    ) {
        $this->skippedItemFixer = $skippedItemFixer;
        $this->skippedItemsCollector = $skippedItemsCollector;
    }

    /**
     * @param OrderInterface $order
     * @param RecalculateResultInterface|null $recalcOriginal
     * @throws \Exception
     * @return RecalculateResultInterface
     */
    public function handle(OrderInterface $order, RecalculateResultInterface $recalcOriginal): RecalculateResultInterface
    {
        $itemsToSkip = $this->skippedItemsCollector->getItemsToSkip($order);

        if (empty($itemsToSkip)) {
            return $recalcOriginal;
        }

        $recalculatedItems = [];
        foreach ($itemsToSkip as $item) {
            $skippedRecalculatedItem = $this->skippedItemFixer->execute($item);
            $recalculatedItems[$item->getItemId()] = $skippedRecalculatedItem;
        }

        //Add fixed skipped items to main result
        $this->reassembleRecalculateResult($recalcOriginal, $recalculatedItems);

        return $recalcOriginal;
    }

    /**
     * @param \Mygento\Base\Api\Data\RecalculateResultInterface $recalcOriginal
     * @param array $recalculatedItems
     */
    private function reassembleRecalculateResult(RecalculateResultInterface $recalcOriginal, array $recalculatedItems): void
    {
        $items = $recalcOriginal->getItems();
        $items += $recalculatedItems;
        $recalcOriginal->setItems($items);

        /** @see \Mygento\Base\Service\PostHandlers\AddChildrenOfBundle::updateSum */
        $newSum = array_sum(
            array_map(
                static function ($item, $key) {
                    return $key !== 'shipping' ? $item->getSum() : 0;
                },
                $recalcOriginal->getItems(),
                array_keys($recalcOriginal->getItems())
            )
        );

        $recalcOriginal->setSum($newSum);
    }
}
