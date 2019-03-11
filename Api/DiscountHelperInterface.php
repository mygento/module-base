<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api;

use Magento\Sales\Api\Data\CreditmemoInterface as Creditmemo;
use Magento\Sales\Api\Data\InvoiceInterface as Invoice;
use Magento\Sales\Api\Data\OrderInterface as Order;

/**
 * Interface DiscountInterface
 *
 * Calculates prices of 1 unit for each item.
 * Recalculates order/invoice/creditmemo.
 * e.g. can spreads one item discount to all items
 *
 * @package Mygento\Base\Api
 */
interface DiscountHelperInterface
{
    /**
     * Returns all items of the entity (order|invoice|creditmemo) with properly calculated discount
     * and properly calculated Sum
     * @param Creditmemo|Invoice|Order $entity
     * @param string $taxValue
     * @param string $taxAttributeCode Set it if info about tax is stored in product in certain
     *                                 attr
     * @param string $shippingTaxValue
     * @throws \Exception
     * @return array|null with calculated items and sum
     */
    public function getRecalculated(
        $entity,
        $taxValue = '',
        $taxAttributeCode = '',
        $shippingTaxValue = ''
    );

    /**
     * @param bool $isSplitItemsAllowed
     * @return $this
     */
    public function setIsSplitItemsAllowed(bool $isSplitItemsAllowed);

    /**
     * @param bool $doCalculation
     * @return $this
     */
    public function setDoCalculation(bool $doCalculation);

    /**
     * @param bool $spreadDiscOnAllUnits
     * @return $this
     */
    public function setSpreadDiscOnAllUnits(bool $spreadDiscOnAllUnits);

    /**
     * Custom floor() function
     * @param float $val
     * @param int $precision
     * @return float|int
     */
    public function slyFloor($val, $precision = 2);

    /**
     * Custom ceil() function
     * @param float $val
     * @param int $precision
     * @return float|int
     */
    public function slyCeil($val, $precision = 2);
}
