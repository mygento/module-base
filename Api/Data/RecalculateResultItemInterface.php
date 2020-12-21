<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api\Data;

interface RecalculateResultItemInterface
{
    public const NAME_FIELD_KEY = 'name';
    public const PRICE_FIELD_KEY = 'price';
    public const QUANTITY_FIELD_KEY = 'quantity';
    public const SUM_FIELD_KEY = 'sum';
    public const TAX_FIELD_KEY = 'tax';
    public const REWARDS_FIELD_KEY = 'reward_currency_amount';
    public const GIFT_CARD_AMOUNT_FIELD_KEY = 'gift_cards_amount';
    public const CUSTOMER_BALANCE_AMOUNT_FIELD_KEY = 'customer_balance_amount';

    /**
     * @return string
     */
    public function getName(): ?string;

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): RecalculateResultItemInterface;

    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @param float|string $price
     * @return $this
     */
    public function setPrice($price): RecalculateResultItemInterface;

    /**
     * @return float
     */
    public function getQuantity(): float;

    /**
     * @param float|int|string $quantity
     * @return $this
     */
    public function setQuantity($quantity): RecalculateResultItemInterface;

    /**
     * @return float
     */
    public function getSum(): float;

    /**
     * @param float|string $sum
     * @return $this
     */
    public function setSum($sum): RecalculateResultItemInterface;

    /**
     * @return string
     */
    public function getTax(): ?string;

    /**
     * @param string $tax
     * @return $this
     */
    public function setTax($tax): RecalculateResultItemInterface;

    /**
     * @return float
     */
    public function getRewardCurrencyAmount(): ?float;

    /**
     * @param float|string $rewards
     * @return $this
     */
    public function setRewardCurrencyAmount($rewards): RecalculateResultItemInterface;

    /**
     * @return float
     */
    public function getGiftCardAmount(): ?float;

    /**
     * @param float|string $amount
     * @return $this
     */
    public function setGiftCardAmount($amount): RecalculateResultItemInterface;

    /**
     * @return float
     */
    public function getCustomerBalanceAmount(): ?float;

    /**
     * @param float|string $amount
     * @return $this
     */
    public function setCustomerBalanceAmount($amount): RecalculateResultItemInterface;
}
