<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Source;

class OrderItem implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var array|null
     */
    private $options;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(\Magento\Framework\App\ResourceConnection $resource)
    {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
    }

    /**
     * @inherit
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [
                [
                    'label' => __('No usage'),
                    'value' => 0,
                ]
            ];

            $table = $this->connection->describeTable(
                $this->resource->getTableName('sales_order_item'),
            );

            foreach ($table as $fieldName => $fieldData) {
                $this->options[] = [
                    'value' => $fieldName,
                    'label' => $fieldData['COLUMN_NAME'],
                ];
            }
        }

        return $this->options;
    }
}
