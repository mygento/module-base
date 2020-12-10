<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Recalculator;

use Mygento\Base\Api\Data\RecalculateResultInterface;

/**
 * Factory class for @see \Mygento\Base\Api\Data\RecalculateResultInterface
 */
class ResultFactory
{
    /**
     * @var \Mygento\Base\Api\Data\RecalculateResultInterfaceFactory
     */
    private $resultInterfaceFactory;

    /**
     * @var \Mygento\Base\Api\Data\RecalculateResultItemInterfaceFactory
     */
    private $itemInterfaceFactory;

    public function __construct(
        \Mygento\Base\Api\Data\RecalculateResultInterfaceFactory $resultInterfaceFactory,
        \Mygento\Base\Api\Data\RecalculateResultItemInterfaceFactory $itemInterfaceFactory
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
