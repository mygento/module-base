<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper;

use Magento\Sales\Api\Data\CreditmemoInterface as Creditmemo;
use Magento\Sales\Api\Data\CreditmemoItemInterface as CreditmemoItem;
use Magento\Sales\Api\Data\InvoiceInterface as Invoice;
use Magento\Sales\Api\Data\InvoiceItemInterface as InvoiceItem;
use Magento\Sales\Api\Data\OrderInterface as Order;
use Magento\Sales\Api\Data\OrderItemInterface as OrderItem;
use Mygento\Base\Api\DiscountHelperInterface;
use Mygento\Base\Helper\Discount\Math;
use Mygento\Base\Helper\Discount\Tax;

/**
 * @inheritDoc
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Discount implements DiscountHelperInterface
{
    /**
     * @var bool Does item exist with price not divisible evenly?
     *           Есть ли item, цена которого не делится нацело
     */
    protected $wryItemUnitPriceExists = false;

    /** @var bool Возможность разделять одну товарную позицию на 2, если цена не делится нацело */
    protected $isSplitItemsAllowed = false;

    /** @var bool Включить перерасчет? */
    protected $doCalculation = true;

    /** @var bool Размазывать ли скидку по всей позициям? */
    protected $spreadDiscOnAllUnits = false;

    /** @var bool Добавлять ли giftCard в цены позиций? */
    protected $isAddGiftCardToPrice = true;

    /** @var bool Добавлять ли баллы в цены позиций? */
    protected $isAddRewardsToPrice = true;

    /**
     * @var \Mygento\Base\Helper\Data
     */
    private $generalHelper;

    /**
     * @var Creditmemo|Invoice|Order
     */
    private $entity;

    /**
     * @var string
     */
    private $taxValue;

    /**
     * @var string
     */
    private $taxAttributeCode;

    /**
     * @var string
     */
    private $shippingTaxValue;

    /**
     * @var string
     */
    private $markingAttributeCode;

    /**
     * @var string
     */
    private $markingListAttributeCode;

    /**
     * @var string
     */
    private $markingRefundAttributeCode;

    /**
     * @var float
     */
    private $discountlessSum = 0.0;

    /**
     * @var \Mygento\Base\Helper\Product\Attribute
     */
    private $attributeHelper;

    /**
     * Discount constructor.
     * @param \Mygento\Base\Helper\Data $baseHelper
     * @param \Mygento\Base\Helper\Product\Attribute $attributeHelper
     */
    public function __construct(
        \Mygento\Base\Helper\Data $baseHelper,
        \Mygento\Base\Helper\Product\Attribute $attributeHelper
    ) {
        $this->generalHelper = $baseHelper;
        $this->attributeHelper = $attributeHelper;
    }

    /**
     * @inheritdoc
     */
    public function getRecalculated(
        $entity,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = '',
        $markingAttributeCode = '',
        $markingListAttributeCode = '',
        $markingRefundAttributeCode = ''
    ) {
        if (!$entity) {
            return null;
        }

        if (!extension_loaded('bcmath')) {
            $this->generalHelper->debug('Fatal Error: bcmath php extension is not available.');

            throw new \Exception('BCMath extension is not available in this PHP version.');
        }
        $this->entity = $entity;
        $this->taxValue = $taxValue;
        $this->taxAttributeCode = $taxAttributeCode;
        $this->shippingTaxValue = $shippingTaxValue;
        $this->markingAttributeCode = $markingAttributeCode;
        $this->markingListAttributeCode = $markingListAttributeCode;
        $this->markingRefundAttributeCode = $markingRefundAttributeCode;

        $globalDiscount = $this->getGlobalDiscount();

        $this->generalHelper->debug(
            '== START == Recalculation of entity prices. Helper Version: '
            . self::VERSION . '.  Entity class: ' . get_class($entity)
            . ". Entity id: {$entity->getEntityId()}"
        );
        $this->generalHelper->debug('Do calculation: ' . ($this->doCalculation ? 'Yes' : 'No'));
        $this->generalHelper->debug(
            'Spread discount: ' . ($this->spreadDiscOnAllUnits ? 'Yes' : 'No')
        );
        $this->generalHelper->debug('Split items: ' . ($this->isSplitItemsAllowed ? 'Yes' : 'No'));
        //Если есть RewardPoints - то калькуляцию применять необходимо принудительно
        if ($globalDiscount !== 0.00) {
            $this->doCalculation = true;
            $this->generalHelper->debug(
                'SplitItems and DoCalculation set to true'
                . ' because of global Discount (e.g. reward points)'
            );
        }
        switch (true) {
            case (!$this->doCalculation):
                $this->generalHelper->debug('No calculation at all.');
                break;
            case ($this->checkSpread()):
                $this->applyDiscount();
                $this->generalHelper->debug("'Apply Discount' logic was applied");
                break;
            default:
                //Это случай, когда не нужно размазывать копейки по позициям
                //и при этом, позиции могут иметь скидки, равномерно делимые.
                $this->setSimplePrices();
                $this->generalHelper->debug("'Simple prices' logic was applied");
                break;
        }
        $this->generalHelper->debug(
            '== STOP == Recalculation. Entity class: ' . get_class(
                $entity
            ) . ". Entity id: {$entity->getEntityId()}"
        );

        return $this->buildFinalArray();
    }

    /**
     * @inheritdoc
     */
    public function setIsSplitItemsAllowed(bool $isSplitItemsAllowed)
    {
        $this->isSplitItemsAllowed = $isSplitItemsAllowed;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDoCalculation(bool $doCalculation)
    {
        $this->doCalculation = $doCalculation;

        return $this;
    }

    /**
     * @param bool $isAddGiftCardToPrice
     * @return $this
     */
    public function setIsAddGiftCardToPrice(bool $isAddGiftCardToPrice)
    {
        $this->isAddGiftCardToPrice = $isAddGiftCardToPrice;

        return $this;
    }

    /**
     * @param bool $isAddRewardsToPrice
     * @return $this
     */
    public function setIsAddRewardsToPrice(bool $isAddRewardsToPrice)
    {
        $this->isAddRewardsToPrice = $isAddRewardsToPrice;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSpreadDiscOnAllUnits(bool $spreadDiscOnAllUnits)
    {
        $this->spreadDiscOnAllUnits = $spreadDiscOnAllUnits;

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @param mixed|null $order
     * @param mixed $amountToSpread
     */
    public function applyDiscount($order = null, $amountToSpread = 0): void
    {
        if ($order) {
            $this->entity = $order;
        }

        $subTotal = $this->entity->getSubtotalInclTax() ?? 0;
        $discount = $this->getEntityDiscountAmountInclTax($this->entity) ?? 0.0;

        /** @var float Скидка на весь заказ.
         * Например, rewardPoints или storeCredit
         */
        $superGrandDiscount = $this->getGlobalDiscount();

        //Bug NN-347. -1 коп в доставке, если Magento неверно посчитала grandTotal заказа
        if ($superGrandDiscount && abs($superGrandDiscount) < 10.00) {
            $this->preFixLowDiscount();
            $superGrandDiscount = 0.00;
        }
        $grandDiscount = $superGrandDiscount;

        //Если размазываем скидку - то размазываем всё: (скидки товаров + $superGrandDiscount) Но не скидку на доставку!
        if ($this->spreadDiscOnAllUnits) {
            $shippingDiscount = $this->getShippingDiscountAmountInclTax($this->entity);

            $grandDiscount = $discount + $this->getGlobalDiscount() + $shippingDiscount;
        }

        $percentageSum = 0;

        $items = $this->getAllItems();
        foreach ($items as $item) {
            if (!$this->isValidItem($item)) {
                continue;
            }

            $price = $item->getData('price_incl_tax');
            $qty = $item->getQty() ?: $item->getQtyOrdered();
            $rowTotal = $item->getData('row_total_incl_tax');
            $rowDiscount = (-1.00) * Tax::getDiscountAmountInclTax($item);

            // ==== Start Calculate Percentage. The heart of logic. ====

            /** @var float Вычисляем знаменатель дроби rowTotal/сумма.
             * Если скидка должна распространиться на все позиции - то это subTotal.
             * Если же позиции без скидок должны остаться без изменений - то это
             * subTotal за вычетом всех позиций без скидок.*/
            $numerator = $rowTotal;
            $denominator = $subTotal - $this->discountlessSum;

            /**
             * @var
             * Может ли супер-скидка (rewards, gift cards etc) распределиться только
             * по тем позициям, которые уже имеют скидку?
             * Проверка нужна, чтобы при распределении супер-скидки цена не ушла в минус
             * для дешевых или бесплатных позиций
             */
            $canSpreadSuperGrandDiscount = $subTotal - $this->discountlessSum + $discount > abs($superGrandDiscount);

            if ($superGrandDiscount !== 0.00 && !$canSpreadSuperGrandDiscount) {
                $numerator = $rowTotal + $rowDiscount;
                $denominator = $subTotal + $discount;
            }

            if ($this->spreadDiscOnAllUnits || $subTotal == $this->discountlessSum || $canSpreadSuperGrandDiscount) {
                $numerator = $rowTotal;
                $denominator = $subTotal;
            }

            $rowPercentage = $numerator / $denominator;

            // ==== End Calculate Percentage. ====

            if (!$this->spreadDiscOnAllUnits && $rowDiscount === 0.00 && $superGrandDiscount === 0.00) {
                $rowPercentage = 0;
            }
            $percentageSum += $rowPercentage;

            if ($this->spreadDiscOnAllUnits) {
                $rowDiscount = 0;
            }

            $rowGrandDiscount = $rowPercentage * $grandDiscount;

            //Проверяем, не превышает ли скидка ряда его тотал.
            // Если да - делаем скидку равной тоталу, чтобы тот не ушел в минус.
            if (abs($rowGrandDiscount) > $rowTotal) {
                $rowGrandDiscount = (-1) * $rowTotal;
            }

            $discountPerUnitRaw = bcadd($rowDiscount, $rowGrandDiscount, 4) / $qty;

            //Если это наценка - то мы должны иначе округлять. Не вверх, а вниз. Из-за отличия в знаке.
            $discountPerUnit = $grandDiscount > 0
                ? Math::slyFloor($discountPerUnitRaw)
                : Math::slyCeil($discountPerUnitRaw);

            //Set посчитанная на ряд $amountToSpread. Округленная вверх.
            $amountToSpreadPerUnit = Math::slyCeil($rowPercentage * $amountToSpread / $qty);
            $item->setData(self::NAME_ROW_AMOUNT_TO_SPREAD, $amountToSpreadPerUnit * $qty);

            $priceWithDiscount = bcadd($price, (string) $discountPerUnit, 2);

            //Set Recalculated unit price for the item
            $item->setData(self::NAME_UNIT_PRICE, $priceWithDiscount);

            $rowTotalNew = round($priceWithDiscount * $qty, 2);

            $rowDiscountNew = $rowDiscount + round($rowGrandDiscount, 2);

            $rowDiff = round($rowTotal + $rowDiscountNew - $rowTotalNew, 2) * 100;

            $item->setData(self::NAME_ROW_DIFF, $rowDiff);
            $item->setData(self::NAME_NEW_DISC, $rowDiscountNew);
        }

        if ($this->spreadDiscOnAllUnits && $this->isSplitItemsAllowed) {
            $this->postFixLowDiscount();
        }

        $this->generalHelper->debug("Sum of all percentages: {$percentageSum}");
    }

    /**
     * Возвращает скидку на весь заказ (если есть). Например, rewardPoints или storeCredit.
     * Если нет скидки - возвращает 0.00
     * @return float
     */
    private function getGlobalDiscount()
    {
        $items = $this->getAllItems();
        $totalItemsSum = 0;
        foreach ($items as $item) {
            $totalItemsSum += $item->getData('row_total_incl_tax');
        }

        $shippingAmount = $this->entity->getShippingInclTax() ?? 0.00;
        $grandTotal = $this->getGrandTotal();
        $discount = $this->getEntityDiscountAmountInclTax($this->entity);
        $excludedDiscounts = $this->getExcludedDiscounts();

        return round($grandTotal - $shippingAmount - $totalItemsSum - $discount + $excludedDiscounts, 2);
    }

    /**
     * Calculates extra discounts and adds them to items $item->setData('discount_amount', ...)
     * @return int count of iterations
     */
    private function preFixLowDiscount()
    {
        $items = $this->getAllItems();
        $globalDiscount = $this->getGlobalDiscount();

        $sign = (int) ($globalDiscount / abs($globalDiscount));
        $i = (int) round(abs($globalDiscount) * 100);
        $count = count($items);
        $iter = 0;

        while ($i > 0) {
            $item = current($items);

            $itDisc = Tax::getDiscountAmountInclTax($item);
            $itTotal = $item->getData('row_total_incl_tax');

            $inc = Math::getDiscountIncrement($sign * $i, $count, $itTotal, $itDisc);
            $item->setData(self::DA_INCL_TAX, $itDisc - $inc / 100);
            $i = (int) ($i - abs($inc));

            $next = next($items);
            if (!$next) {
                reset($items);
            }
            $iter++;
        }

        return $iter;
    }

    /**
     * Calculates extra discounts and adds them to items rowDiscount value
     * @return int count of iterations
     */
    private function postFixLowDiscount()
    {
        $items = $this->getAllItems();
        $grandTotal = $this->getGrandTotal();
        $shippingAmount = $this->entity->getShippingInclTax() ?? 0.00;
        $shippingDiscount = $this->getShippingDiscountAmountInclTax($this->entity);

        $newItemsSum = 0;
        foreach ($items as $item) {
            $qty = $item->getQty() ?: $item->getQtyOrdered();
            $rowTotalNew = $item->getData(self::NAME_UNIT_PRICE) * $qty
                + ($item->getData(self::NAME_ROW_DIFF) / 100);
            $newItemsSum += $rowTotalNew;
        }

        if ($newItemsSum === 0.00) {
            return 0;
        }

        $lostDiscount = round($grandTotal - $shippingAmount - $newItemsSum + $shippingDiscount, 2);

        if ($lostDiscount === 0.00) {
            return 0;
        }

        $sign = (int) ($lostDiscount / abs($lostDiscount));
        $i = (int) round(abs($lostDiscount) * 100);
        $count = count($items);
        $iter = 0;
        reset($items);
        while ($i > 0) {
            $item = current($items);

            $qty = $item->getQty() ?: $item->getQtyOrdered();
            $rowDiff = $item->getData(self::NAME_ROW_DIFF);
            $itTotalNew = $item->getData(self::NAME_UNIT_PRICE) * $qty + $rowDiff / 100;

            $inc = Math::getDiscountIncrement($sign * $i, $count, $itTotalNew, 0);

            $item->setData(self::NAME_ROW_DIFF, $item->getData(self::NAME_ROW_DIFF) + $inc);
            $i = (int) ($i - abs($inc));

            $next = next($items);
            if (!$next) {
                reset($items);
            }
            $iter++;
        }

        return $iter;
    }

    /**
     * If everything is evenly divisible - set up prices without extra recalculations
     * like applyDiscount() method does.
     */
    private function setSimplePrices()
    {
        $items = $this->getAllItems();
        foreach ($items as $item) {
            if (!$this->isValidItem($item)) {
                continue;
            }

            $qty = $item->getQty() ?: $item->getQtyOrdered();
            $rowTotal = $item->getData('row_total_incl_tax');
            $discountAmountInclTax = Tax::getDiscountAmountInclTax($item);

            $priceWithDiscount = ($rowTotal - $discountAmountInclTax) / $qty;
            $item->setData(self::NAME_UNIT_PRICE, $priceWithDiscount);
        }
    }

    /**
     * @throws \Exception
     * @return array
     */
    private function buildFinalArray()
    {
        $grandTotal = $this->getGrandTotal();

        $items = $this->getAllItems();
        $itemsFinal = [];
        $itemsSum = 0.00;
        foreach ($items as $item) {
            if (!$this->isValidItem($item)) {
                continue;
            }

            $splitedItems = $this->getProcessedItem($item);
            $itemsFinal = $itemsFinal + $splitedItems;
        }

        //Calculate sum
        foreach ($itemsFinal as $item) {
            $itemsSum = bcadd($item[self::SUM], $itemsSum, 2);
        }

        $receipt = [
            self::SUM => $itemsSum,
            self::ORIG_GRAND_TOTAL => $grandTotal,
        ];

        $shippingAmount = $this->entity->getShippingInclTax() ?? 0.00;

        //GiftCard, баллы могут не участвовать в расчетах
        $excludedDiscounts = $this->getExcludedDiscounts();
        $itemsSumDiff = round(Math::slyFloor($grandTotal - $itemsSum - $shippingAmount + $excludedDiscounts, 3), 2);

        $this->generalHelper->debug("Items sum: {$itemsSum}. Shipping increase: {$itemsSumDiff}");

        $shippingItem = [
            self::NAME => $this->getShippingName($this->entity),
            self::PRICE => $shippingAmount + $itemsSumDiff,
            self::QUANTITY => 1.0,
            self::SUM => $shippingAmount + $itemsSumDiff,
            self::TAX => $this->shippingTaxValue,
        ];

        $itemsFinal[self::SHIPPING] = $shippingItem;
        $receipt[self::ITEMS] = $itemsFinal;

        if (!$this->checkReceipt($receipt)) {
            $this->generalHelper->debug(
                'WARNING: Calculation error! Sum of items is not equal to grandTotal!'
            );
        }

        $this->generalHelper->debug('Final array:', ['receipt' => $receipt]);

        return $receipt;
    }

    /**
     * @param CreditmemoItem|InvoiceItem|OrderItem $item
     * @param float $price
     * @param string $taxValue
     * @throws \Exception
     * @return array
     *
     * @psalm-suppress UndefinedMethod
     */
    private function buildItem($item, $price, $taxValue = '')
    {
        $qty = $item->getQty() ?: $item->getQtyOrdered();
        if (!$qty) {
            throw new \Exception(
                'Divide by zero. Qty of the item is equal to zero! Item: ' . $item->getId()
            );
        }

        $entityItem = [
            self::PRICE => round($price, 2),
            self::NAME => $item->getName(),
            self::QUANTITY => round($qty, 2),
            self::SUM => round($price * $qty, 2),
            self::TAX => $taxValue,
        ];

        if (!$this->doCalculation) {
            $discountAmountInclTax = Tax::getDiscountAmountInclTax($item);

            $entityItem[self::SUM] = round(
                $item->getData('row_total_incl_tax') - $discountAmountInclTax,
                2
            );
            $entityItem[self::PRICE] = 1;
        }

        $context = [
            'Item id' => $item->getId(),
            'Item rowTotalInclTax' => $item->getData('row_total_incl_tax'),
            'Result' => $entityItem,
        ];
        $this->generalHelper->debug('Item calculation details:', $context);

        return $entityItem;
    }

    /**
     * Make item array and split (if needed) it into 2 items with different prices
     *
     * @param CreditmemoItem|InvoiceItem|OrderItem $item
     * @throws \Exception
     * @return array
     *
     * @psalm-suppress UndefinedMethod
     */
    private function getProcessedItem($item)
    {
        $taxValue = $this->taxAttributeCode
            ? $this->addTaxValue($this->taxAttributeCode, $item)
            : $this->taxValue;
        $price = !($item->getData(self::NAME_UNIT_PRICE) === null)
            ? $item->getData(self::NAME_UNIT_PRICE)
            : $item->getData('price_incl_tax');

        $entityItem = $this->buildItem($item, $price, $taxValue);

        $rowDiff = $item->getData(self::NAME_ROW_DIFF);

        if (!$rowDiff || !$this->isSplitItemsAllowed || !$this->doCalculation) {
            return $this->splitMarkFinalItems($item, $entityItem);
        }

        $qty = $item->getQty() ?: $item->getQtyOrdered();

        /** @var int Сколько товаров из ряда нуждаются в изменении цены
         *  Если $qtyUpdate =0 - то цена всех товаров должна быть увеличина
         */
        $qtyUpdate = abs(bcmod($rowDiff, $qty));
        $sign = abs($rowDiff) / $rowDiff;

        //2 кейса:
        //$qtyUpdate == 0 - то всем товарам увеличить цену, не разделяя.
        //$qtyUpdate > 0  - считаем сколько товаров будут увеличены

        /** @var int "$inc + 1 коп" На столько должны быть увеличены цены */
        $inc = (int) ($rowDiff / $qty);

        $this->generalHelper->debug("Item {$item->getId()} has rowDiff={$rowDiff}.");
        $this->generalHelper->debug("qtyUpdate={$qtyUpdate}. inc={$inc} kop.");

        $item1 = $entityItem;
        $item2 = $entityItem;

        $item1[self::PRICE] = $item1[self::PRICE] + $inc / 100;
        $item1[self::QUANTITY] = $qty - $qtyUpdate;
        $item1[self::SUM] = round($item1[self::QUANTITY] * $item1[self::PRICE], 2);

        if ($qtyUpdate == 0) {
            return $this->splitMarkFinalItems($item, $item1);
        }

        $item2[self::PRICE] = $item2[self::PRICE] + $sign * 0.01 + $inc / 100;
        $item2[self::QUANTITY] = $qtyUpdate;
        $item2[self::SUM] = round($item2[self::QUANTITY] * $item2[self::PRICE], 2);

        return $this->splitMarkFinalItems($item, $item1, $item2);
    }

    /**
     * @param CreditmemoItem|InvoiceItem|OrderItem $item
     * @param array $items1
     * @param array $items2
     * @return array
     */
    private function splitMarkFinalItems($item, array $items1, array $items2 = [])
    {
        $items = [$items1];

        if (!empty($items2)) {
            $items[] = $items2;
        }

        $needMark = $this->canMark() ? $this->isItemNeedMark($item) : false;

        if ($needMark) {
            // make a full split and mark each item
            $items = $this->fullSplit($items);
            $items = $this->markItems(
                $items,
                array_map(
                    'base64_decode',
                    array_map(
                        'trim',
                        explode(',', $this->getItemMark($item))
                    )
                )
            );
        }

        return $this->packItems($item, $items);
    }

    /**
     * @param array $items
     * @return array
     */
    private function fullSplit(array $items)
    {
        $result = [];

        foreach ($items as $item) {
            $qty = $item[self::QUANTITY];

            if ($qty == 1) {
                $result[] = $item;
                continue;
            }

            for ($i = 0; $i < $qty; $i++) {
                $item[self::QUANTITY] = 1;
                $item[self::SUM] = $item[self::PRICE];
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @param array $items
     * @param array $marks
     * @return array
     */
    private function markItems(array $items, array $marks)
    {
        return array_map(function (array $item) use (&$marks) {
            $item[self::MARKING] = array_shift($marks);

            return $item;
        }, $items);
    }

    /**
     * @param mixed $item
     * @param array $items
     * @return array
     */
    private function packItems($item, array $items)
    {
        $result = [];

        if (count($items) == 1) {
            $result[$item->getId()] = array_shift($items);

            return $result;
        }

        $index = 1;
        foreach ($items as $resultItem) {
            $result[$item->getId() . '_' . $index] = $resultItem;
            $index++;
        }

        return $result;
    }

    /**
     * @param Creditmemo|Invoice|Order $entity
     * @return string
     */
    private function getShippingName($entity)
    {
        /** @psalm-suppress UndefinedMethod */
        return $entity->getShippingDescription()
            ?: ($entity->getOrder() ? $entity->getOrder()->getShippingDescription() : '');
    }

    /**
     * Validation method. It sums up all items and compares it to grandTotal.
     * @param array $receipt
     * @return bool True if all items price equal to grandTotal. False - if not.
     */
    private function checkReceipt($receipt)
    {
        $sum = array_reduce(
            $receipt[self::ITEMS],
            function ($carry, $item) {
                $carry += $item[self::SUM];

                return $carry;
            }
        );

        return bcsub($sum, $receipt[self::ORIG_GRAND_TOTAL], 2) === '0.00';
    }

    /**
     * @param CreditmemoItem|InvoiceItem|OrderItem $item
     * @return bool
     */
    private function isValidItem($item)
    {
        return $item->getRowTotalInclTax() !== null;
    }

    /**
     * @param string $taxAttributeCode code of product attribute containing tax value
     * @param CreditmemoItem|InvoiceItem|OrderItem $item
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return string
     */
    private function addTaxValue($taxAttributeCode, $item)
    {
        $id = $item->getProductId();

        if (!$taxAttributeCode || $id === null) {
            return '';
        }

        return $this->attributeHelper->getValue($taxAttributeCode, $id);
    }

    /**
     * It checks do we need to spread discount on all units and sets flag
     * $this->spreadDiscOnAllUnits
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function checkSpread()
    {
        $items = $this->getAllItems();

        $this->discountlessSum = 0.00;
        $discountSum = 0;
        foreach ($items as $item) {
            $qty = $item->getQty() ?: $item->getQtyOrdered();
            $discountAmountInclTax = Tax::getDiscountAmountInclTax($item);

            $rowPrice = $item->getData('row_total_incl_tax') - $discountAmountInclTax;

            if ($discountAmountInclTax === 0.00) {
                $this->discountlessSum += $item->getData('row_total_incl_tax');
            }

            // Означает, что есть item, цена которого не делится нацело
            if (!$this->wryItemUnitPriceExists) {
                $decimals = Math::getDecimalsCountAfterDiv($rowPrice, $qty);

                $this->wryItemUnitPriceExists = $decimals > 2 ? true : false;
            }

            $discountSum += $discountAmountInclTax;
        }

        //Есть ли общая скидка на Чек. bccomp returns 0 if operands are equal
        $isGlobalDiscountExist = bccomp((string) $this->getGlobalDiscount(), '0.00', 2) !== 0;
        if ($isGlobalDiscountExist) {
            $this->generalHelper->debug('1. Global discount on whole cheque.');

            return true;
        }

        //ok, есть товар, который не делится нацело
        if ($this->wryItemUnitPriceExists) {
            $this->generalHelper->debug('2. Item with price which is not divisible evenly.');

            return true;
        }

        $isDiscountExist = bccomp($discountSum, '0.00', 2) !== 0;
        if ($this->spreadDiscOnAllUnits && $isDiscountExist) {
            $this->generalHelper->debug('3. SpreadDiscount = Yes.');

            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    private function getAllItems()
    {
        /** @psalm-suppress UndefinedMethod */
        return $this->entity->getAllVisibleItems()
            ? $this->entity->getAllVisibleItems()
            : $this->entity->getAllItems();
    }

    /**
     * Calculates grandTotal manually
     * @return float
     */
    private function getGrandTotal()
    {
        /** @psalm-suppress UndefinedMethod */
        return round($this->entity->getGrandTotal(), 2);
    }

    /**
     * @param Creditmemo|Invoice|Order $entity
     * @return float
     */
    private function getEntityDiscountAmountInclTax($entity)
    {
        if ($entity->getData(self::DA_INCL_TAX)) {
            return $entity->getData(self::DA_INCL_TAX);
        }

        $items = $this->getAllItems();

        $discountAmountInclTax = 0.00;
        foreach ($items as $item) {
            $discountAmountInclTax += Tax::getDiscountAmountInclTax($item);
        }
        //Учет налога в скидке на  доставку
        $shippingDiscount = $this->getShippingDiscountAmountInclTax($entity);

        $discountAmountInclTax += $shippingDiscount;

        $entity->setData(self::DA_INCL_TAX, (-1.00 * $discountAmountInclTax));

        return $entity->getData(self::DA_INCL_TAX);
    }

    /**
     * Process shipping discount with tax
     * @param Creditmemo|Invoice|Order $entity
     * @return float
     */
    private function getShippingDiscountAmountInclTax($entity)
    {
        if ($entity->getData(self::SHIPPING_DA_INCL_TAX)) {
            return $entity->getData(self::SHIPPING_DA_INCL_TAX);
        }

        $ratio = 1;

        //bccomp returns 0 if operands are equal
        $isShippingsEqual = bccomp($entity->getShippingAmount(), $entity->getShippingInclTax(), 2) === 0;
        $isShippingNotNull = bccomp($entity->getShippingAmount(), 0.00, 2) !== 0;

        if (!$isShippingsEqual && $isShippingNotNull) {
            $ratio = round($entity->getShippingInclTax() / $entity->getShippingAmount(), 2);
        }

        $shippingDiscount = $entity->getShippingDiscountAmount();

        $isOrder = $entity instanceof Order;
        if (!$isOrder) {
            $shippingDiscount = $entity->getOrder()->getShippingDiscountAmount();
        }

        //При различных настройках налогов Magento - налог на скидку доставки либо уже применен либо нет.
        //Если ShTA === (ShAmount - DiscShip) * 20% - то налог должен быть посчитан на скидку доставки
        //Если ShTA === ShAmount * 20% - то налог уже включен в скидку и доп расчет не нужен
        //где ShTA - shipping_tax_amount, ShAmount - shipping_amount, DiscShip - shipping_discount_amount
        $hasTaxInShippingDiscount = bccomp(
            $entity->getShippingTaxAmount(),
            $entity->getShippingAmount() * ($ratio - 1),
            2
        ) === 0;

        $shippingDiscountWithTax = $hasTaxInShippingDiscount
            ? $shippingDiscount
            : $shippingDiscount * $ratio;

        $entity->setData(self::SHIPPING_DA_INCL_TAX, $shippingDiscountWithTax);

        return $entity->getData(self::SHIPPING_DA_INCL_TAX);
    }

    /**
     * @param CreditmemoItem|InvoiceItem|OrderItem $item
     * @return string
     */
    private function getItemTaxPercent($item)
    {
        $isOrderItem = $item instanceof OrderItem;
        if (!$isOrderItem) {
            return $item->getOrderItem()->getTaxPercent();
        }

        return $item->getTaxPercent();
    }

    /**
     * @param CreditmemoItem|InvoiceItem|OrderItem $item
     * @return bool
     */
    private function isItemNeedMark($item): bool
    {
        $isOrderItem = $item instanceof OrderItem;
        if (!$isOrderItem) {
            return (bool) $item->getOrderItem()->getData($this->markingAttributeCode);
        }

        return (bool) $item->getData($this->markingAttributeCode);
    }

    /**
     * @param CreditmemoItem|InvoiceItem|OrderItem $item
     * @return string
     */
    private function getItemMark($item)
    {
        $attr = $this->markingListAttributeCode;

        if ($item instanceof CreditmemoItem) {
            $attr = $this->markingRefundAttributeCode;
        }

        $isOrderItem = $item instanceof OrderItem;
        if (!$isOrderItem) {
            return $item->getOrderItem()->getData($attr);
        }

        return $item->getData($attr);
    }

    /**
     * @param CreditmemoItem|InvoiceItem|OrderItem $item
     * @return string
     */
    private function getItemTaxAmount($item)
    {
        $isOrderItem = $item instanceof OrderItem;
        if (!$isOrderItem) {
            return $item->getOrderItem()->getTaxAmount();
        }

        return $item->getTaxAmount();
    }

    /**
     * @return bool
     */
    private function canMark(): bool
    {
        return $this->markingAttributeCode
            && $this->markingListAttributeCode
            && $this->markingRefundAttributeCode;
    }

    /**
     * Accordingly to settings gift_card_amount, reward_currency_amount, customer_balance_amount
     * might be excluded from item prices calculation
     * @return float
     */
    private function getExcludedDiscounts(): float
    {
        $excludedDiscounts = 0.00;
        if (!$this->isAddGiftCardToPrice) {
            $excludedDiscounts += $this->entity->getData('gift_cards_amount');
        }

        if (!$this->isAddRewardsToPrice) {
            $excludedDiscounts += $this->entity->getData('reward_currency_amount');
        }

        return $excludedDiscounts;
    }
}
