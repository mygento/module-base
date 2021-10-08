<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Source;

class Payments implements \Magento\Framework\Data\OptionSourceInterface
{
    /** @var array */
    protected $options;

    /** @var \Magento\Payment\Helper\Data */
    protected $paymentHelper;

    /**
     * @param \Magento\Payment\Helper\Data $paymentHelper
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            //Do not use flag "withGroups" because some methods are absent
            $this->options = $this->paymentHelper->getPaymentMethodList(true, true, false);
        }

        return $this->options;
    }
}
