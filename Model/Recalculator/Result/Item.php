<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Recalculator\Result;

use Magento\Framework\DataObject;
use Mygento\Base\Api\Data\RecalculateResultItemInterface;

class Item extends DataObject implements RecalculateResultItemInterface
{
    /**
     * @inheritdoc
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME_FIELD_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): RecalculateResultItemInterface
    {
        $this->setData(self::NAME_FIELD_KEY, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrice(): float
    {
        return $this->getData(self::PRICE_FIELD_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setPrice($price): RecalculateResultItemInterface
    {
        $this->setData(self::PRICE_FIELD_KEY, $price);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getQuantity(): float
    {
        return $this->getData(self::QUANTITY_FIELD_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setQuantity($quantity): RecalculateResultItemInterface
    {
        $this->setData(self::QUANTITY_FIELD_KEY, $quantity);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSum(): float
    {
        return $this->getData(self::QUANTITY_FIELD_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setSum($sum): RecalculateResultItemInterface
    {
        $this->setData(self::SUM_FIELD_KEY, $sum);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTax(): ?string
    {
        return $this->getData(self::TAX_FIELD_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setTax($tax): RecalculateResultItemInterface
    {
        $this->setData(self::TAX_FIELD_KEY, $tax);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRewardCurrencyAmount(): ?float
    {
        return $this->getData(self::REWARDS_FIELD_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setRewardCurrencyAmount($rewards): RecalculateResultItemInterface
    {
        $this->setData(self::REWARDS_FIELD_KEY, $rewards);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGiftCardAmount(): ?float
    {
        return $this->getData(self::GIFT_CARD_AMOUNT_FIELD_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setGiftCardAmount($amount): RecalculateResultItemInterface
    {
        $this->setData(self::GIFT_CARD_AMOUNT_FIELD_KEY, $amount);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerBalanceAmount(): ?float
    {
        return $this->getData(self::CUSTOMER_BALANCE_AMOUNT_FIELD_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerBalanceAmount($amount): RecalculateResultItemInterface
    {
        $this->setData(self::CUSTOMER_BALANCE_AMOUNT_FIELD_KEY, $amount);

        return $this;
    }
}
