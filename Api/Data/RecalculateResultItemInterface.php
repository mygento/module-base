<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
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
    public const MARKING = 'marking';
    public const CHILDREN = 'children';

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
     * @return float|null
     */
    public function getPrice(): ?float;

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice(float $price): RecalculateResultItemInterface;

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
     * @return float|null
     */
    public function getSum(): ?float;

    /**
     * @param float|null $sum
     * @return $this
     */
    public function setSum(?float $sum): RecalculateResultItemInterface;

    /**
     * @return string|null
     */
    public function getTax(): ?string;

    /**
     * @param string|null $tax
     * @return $this
     */
    public function setTax(?string $tax): RecalculateResultItemInterface;

    /**
     * @return float
     */
    public function getRewardCurrencyAmount(): ?float;

    /**
     * @param float|null $rewards
     * @return $this
     */
    public function setRewardCurrencyAmount(?float $rewards): RecalculateResultItemInterface;

    /**
     * @return float
     */
    public function getGiftCardAmount(): ?float;

    /**
     * @param float|null $amount
     * @return $this
     */
    public function setGiftCardAmount(?float $amount): RecalculateResultItemInterface;

    /**
     * @return float
     */
    public function getCustomerBalanceAmount(): ?float;

    /**
     * @param float|null $amount
     * @return $this
     */
    public function setCustomerBalanceAmount(?float $amount): RecalculateResultItemInterface;

    /**
     * @return string|null
     */
    public function getMarking(): ?string;

    /**
     * @param string|null $marking
     * @return $this
     */
    public function setMarking(?string $marking): RecalculateResultItemInterface;

    /**
     * @return RecalculateResultItemInterface[]
     */
    public function getChildren(): ?array;

    /**
     * @param $children RecalculateResultItemInterface[]|null
     * @return \Mygento\Base\Api\Data\RecalculateResultItemInterface
     */
    public function setChildren(?array $children): RecalculateResultItemInterface;
}
