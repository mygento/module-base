<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Recalculator;

use Mygento\Base\Api\Data\RecalculateResultInterface;
use Mygento\Base\Api\Data\RecalculateResultInterfaceFactory;
use Mygento\Base\Api\Data\RecalculateResultItemInterfaceFactory;

/**
 * Factory class for @see \Mygento\Base\Api\Data\RecalculateResultInterface
 */
class ResultFactory
{
    /**
     * @var RecalculateResultInterfaceFactory
     */
    private $resultInterfaceFactory;

    /**
     * @var RecalculateResultItemInterfaceFactory
     */
    private $itemInterfaceFactory;

    /**
     * @param RecalculateResultInterfaceFactory $resultInterfaceFactory
     * @param RecalculateResultItemInterfaceFactory $itemInterfaceFactory
     */
    public function __construct(
        RecalculateResultInterfaceFactory $resultInterfaceFactory,
        RecalculateResultItemInterfaceFactory $itemInterfaceFactory
    ) {
        $this->itemInterfaceFactory = $itemInterfaceFactory;
        $this->resultInterfaceFactory = $resultInterfaceFactory;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Mygento\Base\Api\Data\RecalculateResultInterface
     */
    public function create(array $data = [])
    {
        $items = [];
        foreach ($data[RecalculateResultInterface::ITEMS_FIELD_NAME] as $key => $itemData) {
            $items[$key] = $this->itemInterfaceFactory->create(['data' => $itemData]);
        }

        $data[RecalculateResultInterface::ITEMS_FIELD_NAME] = $items;

        return $this->resultInterfaceFactory->create(['data' => $data]);
    }
}
