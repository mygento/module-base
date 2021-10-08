<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Source;

class State implements \Magento\Framework\Data\OptionSourceInterface
{
    public const UNDEFINED_OPTION_LABEL = '-- Please Select --';

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    private $orderConfig;

    /**
     * @var array
     */
    private $options;

    /**
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     */
    public function __construct(\Magento\Sales\Model\Order\Config $orderConfig)
    {
        $this->orderConfig = $orderConfig;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $states = $this->orderConfig->getStates();
            $this->options = [['value' => '', 'label' => __('-- Please Select --')]];
            foreach ($states as $code => $label) {
                $this->options[] = ['value' => $code, 'label' => $label];
            }
        }

        return $this->options;
    }
}
