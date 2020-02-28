<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Source;

abstract class AbstractAttributes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $showEmpty = true;

    /**
     * @var bool
     */
    protected $flatOnly = true;

    /**
     * @var array
     */
    protected $filterTypesNotEqual = [];

    /**
     * @var array
     */
    protected $filterTypesEqual = [];

    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    private $entityType;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $attrColFactory;

    /**
     * @param \Magento\Eav\Model\Entity\Type $entityType
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attrColFactory
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attrColFactory
    ) {
        $this->entityType = $entityType;
        $this->attrColFactory = $attrColFactory;

        $this->entityType->loadByCode(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE
        );
    }

    /**
     * All price attributes of Product entity
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->options === null) {
            $col = $this->attrColFactory->create();

            $keyEntityType = \Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID;
            $col->addFieldToFilter($keyEntityType, $this->entityType->getId());

            //Filter Type is NOT In Array
            if (!empty($this->filterTypesNotEqual)) {
                $filter = ['nin' => $this->filterTypesNotEqual];
                $col->addFieldToFilter('main_table.frontend_input', $filter);
            }

            //Filter Type IS In Array
            if (!empty($this->filterTypesEqual)) {
                $filter = ['in' => $this->filterTypesEqual];
                $col->addFieldToFilter('main_table.frontend_input', $filter);
            }

            if ($this->flatOnly) {
                $col->addFieldToFilter('used_in_product_listing', 1);
            }

            $col->setOrder('frontend_label', 'ASC');
            $col = $this->additionalFilter($col);

            $attrAll = $col->load()->getItems();

            $this->options = [];

            if ($this->showEmpty) {
                $this->options[] = [
                    'label' => __('No usage'),
                    'value' => 0,
                ];
            }

            // Loop over all attributes
            foreach ($attrAll as $attr) {
                $label = $attr->getStoreLabel() ?? $attr->getFrontendLabel();
                if ('' != $label) {
                    $this->options[] = ['label' => $label, 'value' => $attr->getAttributeCode()];
                }
            }
        }

        return $this->options;
    }

    /**
     * To option array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * Additional Filter
     *
     * @param mixed $collection
     * @return mixed $collection
     */
    protected function additionalFilter($collection)
    {
        return $collection;
    }
}
