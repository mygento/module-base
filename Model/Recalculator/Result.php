<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
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

    public function getSum()
    {
        return $this->getData(self::SUM_FIELD_NAME);
    }
}
