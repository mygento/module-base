<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Plugin;

class Transaction
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Magento\Sales\Model\Order\Payment\Transaction $subject
     * @param mixed $result
     */
    public function afterGetTransactionTypes(
        \Magento\Sales\Model\Order\Payment\Transaction $subject,
        $result
    ) {
        return array_merge($result, [
            \Mygento\Base\Model\Payment\Transaction::TYPE_CAPTURE_CONFIRM => __('Capture confirm'),
            \Mygento\Base\Model\Payment\Transaction::TYPE_FISCAL => __('Fiscal receipt'),
            \Mygento\Base\Model\Payment\Transaction::TYPE_FISCAL_PREPAYMENT => __('Fiscal receipt prepayment'),
            \Mygento\Base\Model\Payment\Transaction::TYPE_FISCAL_REFUND => __('Fiscal receipt refund'),
        ]);
    }
}
