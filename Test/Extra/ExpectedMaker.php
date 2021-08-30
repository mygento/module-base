<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Extra;

use Mygento\Base\Api\Data\RecalculateResultInterface;

class ExpectedMaker
{
    /**
     * @param array|RecalculateResultInterface $recalcResult
     */
    public static function dump($recalcResult)
    {
        if ($recalcResult instanceof RecalculateResultInterface) {
            self::dumpExpectedFromObject($recalcResult);
        }

        self::dumpExpectedFromArray($recalcResult);
    }

    /**
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @param \Mygento\Base\Api\Data\RecalculateResultInterface $recalcOriginal
     */
    private static function dumpExpectedFromObject($recalcOriginal)
    {
        $items = [];
        foreach ($recalcOriginal->getItems() as $itemId => $item) {
            $itemArray = $item->toArray();
            unset($itemArray['name']);
            $itemArray['price'] = round($itemArray['price'], 2);
            $itemArray['sum'] = round($itemArray['sum'], 2);
            foreach ($item->getChildren() as $key => $child) {
                $childArray = $child->toArray();
                $childArray['price'] = round($childArray['price'], 2);
                $childArray['sum'] = round($childArray['sum'], 2);
                unset($childArray['name']);
                $itemArray['children'][$key] = $childArray;
            }

            $items[$itemId] = $itemArray;
        }
        $recalcOriginal->setItems($items);

        echo "\033[1;33m"; // yellow
        $storedValue = ini_get('serialize_precision');
        ini_set('serialize_precision', 12);
        var_export($recalcOriginal->toArray());
        ini_set('serialize_precision', $storedValue);
        echo "\033[0m"; // reset color
        exit();
    }

    /**
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @param array $recalcResult
     */
    private static function dumpExpectedFromArray($recalcResult)
    {
        echo "\033[1;33m"; // yellow
        $storedValue = ini_get('serialize_precision');
        ini_set('serialize_precision', 12);
        var_export($recalcResult);
        ini_set('serialize_precision', $storedValue);
        echo "\033[0m"; // reset color
        exit();
    }
}
