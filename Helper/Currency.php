<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper;

class Currency extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);

        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * @param float|int $amountValue
     * @param string $currencyCodeFrom
     * @param string $currencyCodeTo
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return float|int
     */
    public function convert($amountValue, $currencyCodeFrom = null, $currencyCodeTo = null)
    {
        /**
         * If is not specified the currency code from which we want to convert
         * - use current currency
         */
        if (!$currencyCodeFrom) {
            $currencyCodeFrom = $this->storeManager->getStore()
                ->getCurrentCurrency()->getCode();
        }

        /**
         * If is not specified the currency code to which we want to convert
         * - use base currency
         */
        if (!$currencyCodeTo) {
            $currencyCodeTo = $this->storeManager->getStore()
                ->getBaseCurrency()->getCode();
        }

        /**
         * Do not convert if currency is same
         */
        if ($currencyCodeFrom == $currencyCodeTo) {
            return $amountValue;
        }

        /** @var float $rate */
        $rate = $this->currencyFactory->create()
            ->load($currencyCodeFrom)->getAnyRate($currencyCodeTo);

        if (!$rate) {
            throw new \Exception(__('Cannot find currency rate'));
        }

        // Get amount in new currency
        return $amountValue * $rate;
    }
}
