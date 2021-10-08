<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Payment;

use Magento\Framework\Exception\LocalizedException;

class Transaction extends \Magento\Sales\Model\Order\Payment\Transaction
{
    public const TYPE_CAPTURE_CONFIRM = 'capture_confirm';
    public const TYPE_FISCAL = 'fiscal_receipt';
    public const TYPE_FISCAL_PREPAYMENT = 'fiscal_prepay';
    public const TYPE_FISCAL_REFUND = 'fiscal_refund';

    /**
     * Check whether specified or set transaction type is supported
     *
     * @param string $txnType
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _verifyTxnType($txnType = null)
    {
        if (null === $txnType) {
            $txnType = $this->getTxnType();
        }
        switch ($txnType) {
            case self::TYPE_PAYMENT:
            case self::TYPE_ORDER:
            case self::TYPE_AUTH:
            case self::TYPE_CAPTURE:
            case self::TYPE_CAPTURE_CONFIRM:
            case self::TYPE_VOID:
            case self::TYPE_REFUND:
            case self::TYPE_FISCAL:
            case self::TYPE_FISCAL_PREPAYMENT:
            case self::TYPE_FISCAL_REFUND:
                break;
            default:
                throw new LocalizedException(
                    __('We found an unsupported transaction type "%1".', $txnType)
                );
        }
    }
}
