<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper;

use Magento\Sales\Api\Data\CreditmemoInterface as Creditmemo;
use Magento\Sales\Api\Data\CreditmemoItemInterface as CreditmemoItem;
use Magento\Sales\Api\Data\InvoiceInterface as Invoice;
use Magento\Sales\Api\Data\InvoiceItemInterface as InvoiceItem;
use Magento\Sales\Api\Data\OrderInterface as Order;
use Magento\Sales\Api\Data\OrderItemInterface as OrderItem;

/**
 * Class Discount
 * @package Mygento\Base\Helper
 */
final class Discount
{
    const VERSION         = '1.0.19';
    const NAME_UNIT_PRICE = 'disc_hlpr_price';
    const NAME_ROW_DIFF   = 'recalc_row_diff';

    /**
     * @var \Mygento\Base\Helper\Data
     */
    private $generalHelper;
    /**
     * @var Order|Invoice|Creditmemo
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
     * @var float
     */
    private $discountlessSum = 0.0;

    /**
     * @var boolean Does item exist with price not divisible evenly?
     * Есть ли item, цена которого не делится нацело
     */
    protected $_wryItemUnitPriceExists = false;

    /** @var boolean Возможность разделять одну товарную позицию на 2, если цена не делится нацело */
    protected $isSplitItemsAllowed = false;

    /** @var boolean Включить перерасчет? */
    protected $doCalculation = true;

    /** @var boolean Размазывать ли скидку по всей позициям? */
    protected $spreadDiscOnAllUnits = false;

    /**
     * @param \Mygento\Base\Helper\Data $baseHelper
     */
    public function __construct(\Mygento\Base\Helper\Data $baseHelper)
    {
        $this->generalHelper = $baseHelper;
    }

    /**
     * Returns all items of the entity (order|invoice|creditmemo) with properly calculated discount
     * and properly calculated Sum
     * @param Order|Invoice|Creditmemo $entity
     * @param string $taxValue
     * @param string $taxAttributeCode Set it if info about tax is stored in product in certain
     *     attr
     * @param string $shippingTaxValue
     * @throws \Exception
     * @return null|array with calculated items and sum
     */
    public function getRecalculated(
        $entity,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = ''
    ) {
        if (!$entity) {
            return null;
        }

        if (!extension_loaded('bcmath')) {
            $this->generalHelper->debug('Fatal Error: bcmath php extension is not available.');
            throw new \Exception('BCMath extension is not available in this PHP version.');
        }
        $this->entity           = $entity;
        $this->taxValue         = $taxValue;
        $this->taxAttributeCode = $taxAttributeCode;
        $this->shippingTaxValue = $shippingTaxValue;

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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function applyDiscount(): void
    {
        $subTotal = $this->entity->getSubtotalInclTax() ?? 0;
        $discount = $this->entity->getDiscountAmount() ?? 0;

        /** @var float $superGrandDiscount Скидка на весь заказ.
         * Например, rewardPoints или storeCredit
         */
        $superGrandDiscount = $this->getGlobalDiscount();

        //Bug NN-347. -1 коп в доставке, если Magento неверно посчитала grandTotal заказа
        if ($superGrandDiscount && abs($superGrandDiscount) < 10.00) {
            $this->preFixLowDiscount();
            $superGrandDiscount = 0.00;
        }
        $grandDiscount = $superGrandDiscount;

        //Если размазываем скидку - то размазываем всё: (скидки товаров + $superGrandDiscount)
        if ($this->spreadDiscOnAllUnits) {
            $grandDiscount = $discount + $this->getGlobalDiscount();
        }

        $percentageSum = 0;

        $items    = $this->getAllItems();
        $itemsSum = 0.00;
        foreach ($items as $item) {
            if (!$this->isValidItem($item)) {
                continue;
            }

            $price       = $item->getData('price_incl_tax');
            $qty         = $item->getQty() ?: $item->getQtyOrdered();
            $rowTotal    = $item->getData('row_total_incl_tax');
            $rowDiscount = round((-1.00) * $item->getDiscountAmount(), 2);

            // ==== Start Calculate Percentage. The heart of logic. ====

            /** @var float $denominator Это знаменатель дроби (rowTotal/сумма).
             * Если скидка должна распространиться на все позиции - то это subTotal.
             * Если же позиции без скидок должны остаться без изменений - то это
             * subTotal за вычетом всех позиций без скидок.*/
            $denominator = $subTotal - $this->discountlessSum;

            if ($this->spreadDiscOnAllUnits
                || ($subTotal == $this->discountlessSum)
                || ($superGrandDiscount !== 0.00)) {
                $denominator = $subTotal;
            }

            $rowPercentage = $rowTotal / $denominator;

            // ==== End Calculate Percentage. ====

            if (!$this->spreadDiscOnAllUnits
                && ($rowDiscount === 0.00)
                && ($superGrandDiscount === 0.00)) {
                $rowPercentage = 0;
            }
            $percentageSum += $rowPercentage;

            if ($this->spreadDiscOnAllUnits) {
                $rowDiscount = 0;
            }

            $discountPerUnit = $this->slyCeil(
                ($rowDiscount + $rowPercentage * $grandDiscount) / $qty
            );

            $priceWithDiscount = bcadd($price, (string)$discountPerUnit, 2);

            //Set Recalculated unit price for the item
            $item->setData(self::NAME_UNIT_PRICE, $priceWithDiscount);

            $rowTotalNew = round($priceWithDiscount * $qty, 2);
            $itemsSum    += $rowTotalNew;

            $rowDiscountNew = $rowDiscount + round($rowPercentage * $grandDiscount, 2);

            $rowDiff = round($rowTotal + $rowDiscountNew - $rowTotalNew, 2) * 100;

            $item->setData(self::NAME_ROW_DIFF, $rowDiff);
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
    protected function getGlobalDiscount()
    {
        $items         = $this->getAllItems();
        $totalItemsSum = 0;
        foreach ($items as $item) {
            $totalItemsSum += $item->getData('row_total_incl_tax');
        }

        $entityDiscount = $this->entity->getDiscountAmount() ?? 0.00;
        $shippingAmount = $this->entity->getShippingInclTax() ?? 0.00;
        $grandTotal     = $this->getGrandTotal();
        $discount       = round($entityDiscount, 2);

        $globDisc = round($grandTotal - $shippingAmount - $totalItemsSum - $discount, 2);

        return $globDisc;
    }

    /**
     * Calculates extra discounts and adds them to items $item->setData('discount_amount', ...)
     * @return int count of iterations
     */
    protected function preFixLowDiscount()
    {
        $items          = $this->getAllItems();
        $globalDiscount = $this->getGlobalDiscount();

        $sign  = (int)($globalDiscount / abs($globalDiscount));
        $i     = (int)round(abs($globalDiscount) * 100);
        $count = count($items);
        $iter  = 0;

        while ($i > 0) {
            $item = current($items);

            $itDisc  = $item->getData('discount_amount');
            $itTotal = $item->getData('row_total_incl_tax');

            $inc = $this->getDiscountIncrement($sign * $i, $count, $itTotal, $itDisc);
            $item->setData('discount_amount', $itDisc - $inc / 100);
            $i = (int)($i - abs($inc));

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
    protected function postFixLowDiscount()
    {
        $items          = $this->getAllItems();
        $grandTotal     = $this->getGrandTotal();
        $shippingAmount = $this->entity->getShippingInclTax() ?? 0.00;

        $newItemsSum = 0;
        $rowDiffSum  = 0;
        foreach ($items as $item) {
            $qty         = $item->getQty() ?: $item->getQtyOrdered();
            $rowTotalNew = $item->getData(self::NAME_UNIT_PRICE) * $qty
                + ($item->getData(self::NAME_ROW_DIFF) / 100);
            $rowDiffSum  += $item->getData(self::NAME_ROW_DIFF);
            $newItemsSum += $rowTotalNew;
        }

        $lostDiscount = round($grandTotal - $shippingAmount - $newItemsSum, 2);

        if ($lostDiscount === 0.00) {
            return 0;
        }

        $sign  = (int)($lostDiscount / abs($lostDiscount));
        $i     = (int)round(abs($lostDiscount) * 100);
        $count = count($items);
        $iter  = 0;
        reset($items);
        while ($i > 0) {
            $item = current($items);

            $qty        = $item->getQty() ?: $item->getQtyOrdered();
            $rowDiff    = $item->getData(self::NAME_ROW_DIFF);
            $itTotalNew = $item->getData(self::NAME_UNIT_PRICE) * $qty + $rowDiff / 100;

            $inc = $this->getDiscountIncrement($sign * $i, $count, $itTotalNew, 0);

            $item->setData(self::NAME_ROW_DIFF, $item->getData(self::NAME_ROW_DIFF) + $inc);
            $i = (int)($i - abs($inc));

            $next = next($items);
            if (!$next) {
                reset($items);
            }
            $iter++;
        }

        return $iter;
    }

    /**
     * Calculates how many kopeyki can be added to item
     * considering number of items, rowTotal and rowDiscount
     * @param int $amountToSpread (in kops)
     * @param int $itemsCount
     * @param float $itemTotal
     * @param float $itemDiscount
     * @return int
     */
    public function getDiscountIncrement($amountToSpread, $itemsCount, $itemTotal, $itemDiscount)
    {
        $sign = (int)($amountToSpread / abs($amountToSpread));

        //Пытаемся размазать поровну
        $discPerItem = (int)(abs($amountToSpread) / $itemsCount);
        $inc         = ($discPerItem > 1) && ($itemTotal - $itemDiscount) > $discPerItem
            ? $sign * $discPerItem
            : $sign;

        //Изменяем скидку позиции
        if (($itemTotal - $itemDiscount) > abs($inc)) {
            return $inc;
        }

        return 0;
    }

    /**
     * If everything is evenly divisible - set up prices without extra recalculations
     * like applyDiscount() method does.
     */
    public function setSimplePrices()
    {
        $items = $this->getAllItems();
        foreach ($items as $item) {
            if (!$this->isValidItem($item)) {
                continue;
            }

            $qty      = $item->getQty() ?: $item->getQtyOrdered();
            $rowTotal = $item->getData('row_total_incl_tax');

            $priceWithDiscount = ($rowTotal - $item->getData('discount_amount')) / $qty;
            $item->setData(self::NAME_UNIT_PRICE, $priceWithDiscount);
        }
    }

    /**
     * @throws \Exception
     * @return array
     */
    public function buildFinalArray()
    {
        $grandTotal = $this->getGrandTotal();

        $items      = $this->getAllItems();
        $itemsFinal = [];
        $itemsSum   = 0.00;
        foreach ($items as $item) {
            if (!$this->isValidItem($item)) {
                continue;
            }

            $splitedItems = $this->getProcessedItem($item);
            $itemsFinal   = $itemsFinal + $splitedItems;
        }

        //Calculate sum
        foreach ($itemsFinal as $item) {
            $itemsSum += $item['sum'];
        }

        $receipt = [
            'sum'            => $itemsSum,
            'origGrandTotal' => $grandTotal,
        ];

        $shippingAmount = $this->entity->getShippingInclTax() ?? 0.00;
        $itemsSumDiff   = round($this->slyFloor($grandTotal - $itemsSum - $shippingAmount, 3), 2);

        $this->generalHelper->debug("Items sum: {$itemsSum}. Shipping increase: {$itemsSumDiff}");

        $shippingItem = [
            'name'     => $this->getShippingName($this->entity),
            'price'    => $shippingAmount + $itemsSumDiff,
            'quantity' => 1.0,
            'sum'      => $shippingAmount + $itemsSumDiff,
            'tax'      => $this->shippingTaxValue,
        ];

        $itemsFinal['shipping'] = $shippingItem;
        $receipt['items']       = $itemsFinal;

        if (!$this->_checkReceipt($receipt)) {
            $this->generalHelper->debug(
                'WARNING: Calculation error! Sum of items is not equal to grandTotal!'
            );
        }

        $this->generalHelper->debug('Final array:');
        $this->generalHelper->debug($receipt);

        return $receipt;
    }

    /**
     * @param OrderItem|InvoiceItem|CreditmemoItem $item
     * @param float $price
     * @param string $taxValue
     * @throws \Exception
     * @return array
     *
     * @psalm-suppress UndefinedMethod
     */
    protected function _buildItem($item, $price, $taxValue = '')
    {
        $qty = $item->getQty() ?: $item->getQtyOrdered();
        if (!$qty) {
            throw new \Exception(
                'Divide by zero. Qty of the item is equal to zero! Item: ' . $item->getId()
            );
        }

        $entityItem = [
            'price'    => round($price, 2),
            'name'     => $item->getName(),
            'quantity' => round($qty, 2),
            'sum'      => round($price * $qty, 2),
            'tax'      => $taxValue,
        ];

        if (!$this->doCalculation) {
            $entityItem['sum']   = round(
                $item->getData('row_total_incl_tax') - $item->getData('discount_amount'),
                2
            );
            $entityItem['price'] = 1;
        }

        $this->generalHelper->debug('Item calculation details:');
        $this->generalHelper->debug(
            "Item id: {$item->getId()}. Orig price: {$price} Item rowTotalInclTax:"
            . " {$item->getData('row_total_incl_tax')}"
            . " PriceInclTax of 1 piece: {$price}. Result of calc:"
        );
        $this->generalHelper->debug($entityItem);

        return $entityItem;
    }

    /**
     * Make item array and split (if needed) it into 2 items with different prices
     *
     * @param OrderItem|InvoiceItem|CreditmemoItem $item
     * @throws \Exception
     * @return array
     *
     * @psalm-suppress UndefinedMethod
     */
    public function getProcessedItem($item)
    {
        $final = [];

        $taxValue = $this->taxAttributeCode
            ? $this->addTaxValue($this->taxAttributeCode, $item)
            : $this->taxValue;
        $price = !($item->getData(self::NAME_UNIT_PRICE) === null)
            ? $item->getData(self::NAME_UNIT_PRICE)
            : $item->getData('price_incl_tax');

        $entityItem = $this->_buildItem($item, $price, $taxValue);

        $rowDiff = $item->getData(self::NAME_ROW_DIFF);

        if (!$rowDiff || !$this->isSplitItemsAllowed || !$this->doCalculation) {
            $final[$item->getId()] = $entityItem;

            return $final;
        }

        $qty = $item->getQty() ?: $item->getQtyOrdered();

        /** @var int $qtyUpdate Сколько товаров из ряда нуждаются в изменении цены
         *  Если $qtyUpdate =0 - то цена всех товаров должна быть увеличина
         */
        $qtyUpdate = abs(bcmod($rowDiff, $qty));
        $sign = abs($rowDiff)/$rowDiff;

        //2 кейса:
        //$qtyUpdate == 0 - то всем товарам увеличить цену, не разделяя.
        //$qtyUpdate > 0  - считаем сколько товаров будут увеличены

        /** @var int "$inc + 1 коп" На столько должны быть увеличены цены */
        $inc = (int)($rowDiff / $qty);

        $this->generalHelper->debug("Item {$item->getId()} has rowDiff={$rowDiff}.");
        $this->generalHelper->debug("qtyUpdate={$qtyUpdate}. inc={$inc} kop.");

        $item1 = $entityItem;
        $item2 = $entityItem;

        $item1['price']    = $item1['price'] + $inc / 100;
        $item1['quantity'] = $qty - $qtyUpdate;
        $item1['sum']      = round($item1['quantity'] * $item1['price'], 2);

        if ($qtyUpdate == 0) {
            $final[$item->getId()] = $item1;

            return $final;
        }

        $item2['price']    = $item2['price'] + $sign*0.01 + $inc / 100;
        $item2['quantity'] = $qtyUpdate;
        $item2['sum']      = round($item2['quantity'] * $item2['price'], 2);

        $final[$item->getId() . '_1'] = $item1;
        $final[$item->getId() . '_2'] = $item2;

        return $final;
    }

    /**
     * @param Order|Invoice|Creditmemo $entity
     * @return string
     */
    public function getShippingName($entity)
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
    protected function _checkReceipt($receipt)
    {
        $sum = array_reduce(
            $receipt['items'],
            function ($carry, $item) {
                $carry += $item['sum'];

                return $carry;
            }
        );

        return bcsub($sum, $receipt['origGrandTotal'], 2) === '0.00';
    }

    /**
     * @param OrderItem|InvoiceItem|CreditmemoItem $item
     * @return bool
     */
    public function isValidItem($item)
    {
        return $item->getRowTotalInclTax() !== null;
    }

    /**
     * Custom floor() function
     * @param float $val
     * @param int $precision
     * @return float|int
     */
    public function slyFloor($val, $precision = 2)
    {
        $factor  = 1.00;
        $divider = pow(10, $precision);

        if ($val < 0) {
            $factor = -1.00;
        }

        return (floor(abs($val) * $divider) / $divider) * $factor;
    }

    /**
     * Custom ceil() function
     * @param float $val
     * @param int $precision
     * @return float|int
     */
    public function slyCeil($val, $precision = 2)
    {
        $factor  = 1.00;
        $divider = pow(10, $precision);

        if ($val < 0) {
            $factor = -1.00;
        }

        return (ceil(abs($val) * $divider) / $divider) * $factor;
    }

    /**
     * @param string $taxAttributeCode code of product attribute containing tax value
     * @param OrderItem|InvoiceItem|CreditmemoItem $item
     * @return string
     */
    protected function addTaxValue($taxAttributeCode, $item)
    {
        if (!$taxAttributeCode) {
            return '';
        }

        return $this->generalHelper->getAttributeValue($taxAttributeCode, $item->getProductId());
    }

    /**
     * It checks do we need to spread discount on all units and sets flag
     * $this->spreadDiscOnAllUnits
     * @return bool
     */
    public function checkSpread()
    {
        $items = $this->getAllItems();

        $this->discountlessSum = 0.00;
        foreach ($items as $item) {
            $qty      = $item->getQty() ?: $item->getQtyOrdered();
            $rowPrice = $item->getData('row_total_incl_tax') - $item->getData('discount_amount');

            if ((float)$item->getData('discount_amount') === 0.00) {
                $this->discountlessSum += $item->getData('row_total_incl_tax');
            }

            /* Означает, что есть item, цена которого не делится нацело*/
            if (!$this->_wryItemUnitPriceExists) {
                $decimals = $this->getDecimalsCountAfterDiv($rowPrice, $qty);

                $this->_wryItemUnitPriceExists = $decimals > 2 ? true : false;
            }
        }

        //Есть ли общая скидка на Чек. bccomp returns 0 if operands are equal
        if (bccomp((string)$this->getGlobalDiscount(), '0.00', 2) !== 0) {
            $this->generalHelper->debug('1. Global discount on whole cheque.');

            return true;
        }

        //ok, есть товар, который не делится нацело
        if ($this->_wryItemUnitPriceExists) {
            $this->generalHelper->debug('2. Item with price which is not divisible evenly.');

            return true;
        }

        if ($this->spreadDiscOnAllUnits) {
            $this->generalHelper->debug('3. SpreadDiscount = Yes.');

            return true;
        }

        return false;
    }

    /**
     * @param int $x
     * @param int $y
     * @return int
     */
    public function getDecimalsCountAfterDiv($x, $y)
    {
        $divRes   = (string)round($x / $y, 20);

        $pos = strrchr($divRes, '.');
        $decimals = $pos !== false ? strlen($pos) - 1 : 0;

        return $decimals;
    }

    /**
     * @return mixed
     */
    public function getAllItems()
    {
        /** @psalm-suppress UndefinedMethod */
        return $this->entity->getAllVisibleItems()
            ? $this->entity->getAllVisibleItems()
            : $this->entity->getAllItems();
    }

    /**
     * @param bool $isSplitItemsAllowed
     */
    public function setIsSplitItemsAllowed($isSplitItemsAllowed)
    {
        $this->isSplitItemsAllowed = (bool)$isSplitItemsAllowed;
    }

    /**
     * @param bool $doCalculation
     */
    public function setDoCalculation($doCalculation)
    {
        $this->doCalculation = (bool)$doCalculation;
    }

    /**
     * @param bool $spreadDiscOnAllUnits
     */
    public function setSpreadDiscOnAllUnits($spreadDiscOnAllUnits)
    {
        $this->spreadDiscOnAllUnits = (bool)$spreadDiscOnAllUnits;
    }

    /**
     * Calculates grandTotal manually
     * due to Gift Card and Customer Balance should be visible in tax receipt
     * @return float
     */
    protected function getGrandTotal()
    {
        /** @psalm-suppress UndefinedMethod */
        return round(
            $this->entity->getGrandTotal()
            //Magento Commerce Features
            + $this->entity->getData('gift_cards_amount')
            + $this->entity->getData('customer_balance_amount'),
            2
        );
    }
}
