<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api\Data;

interface RecalculateResultInterface
{
    public const ITEMS_FIELD_NAME = 'items';
    public const SUM_FIELD_NAME = 'sum';

    /**
     * @return mixed
     */
    public function toArray();

    /**
     * @return RecalculateResultItemInterface[]
     */
    public function getItems(): array;

    /**
     * @return float|string
     */
    public function getSum();
}
