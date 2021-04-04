<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Recalculator;

use Magento\Framework\DataObject;
use Mygento\Base\Api\Data\RecalculateResultInterface;
use Mygento\Base\Api\Data\RecalculateResultItemInterface;

class Result extends DataObject implements RecalculateResultInterface
{
    /**
     * @return RecalculateResultItemInterface[]
     */
    public function getItems(): array
    {
        return $this->getData(self::ITEMS_FIELD_NAME);
    }

    /**
     * @return float|string|null
     */
    public function getSum()
    {
        return $this->getData(self::SUM_FIELD_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setSum($sum)
    {
        return $this->setData(self::SUM_FIELD_NAME, $sum);
    }

    /**
     * @inheritDoc
     */
    public function getItemById($itemId): ?RecalculateResultItemInterface
    {
        $item = $this->getItems()[$itemId]
            //При разделении позиций первая имеет меньшую цену
            ?? $this->getItems()[$itemId . '_1']
            ?? null;

        if ($item) {
            return $item;
        }

        //Если не найден - поищем среди дочерних
        foreach ($this->getItems() as $it) {
            foreach ((array) $it->getChildren() as $id => $childItem) {
                if ($id == $itemId) {
                    return $childItem;
                }
            }
        }
    }
}
